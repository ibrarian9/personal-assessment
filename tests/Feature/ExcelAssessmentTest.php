<?php

namespace Tests\Feature;

use App\Models\LingkupMateri;
use App\Models\MataPelajaran;
use App\Models\NilaiSumatifTp;
use App\Models\Siswa;
use App\Models\TujuanPembelajaran;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Tests\TestCase;

class ExcelAssessmentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Setup base subject
        $this->mapel = MataPelajaran::create([
            'kode_mapel' => 'BIO-SMA-X',
            'nama_mapel' => 'Biologi SMA',
            'fase' => 'Fase E',
            'tingkat' => 'Kelas X',
            'kkm' => 75.00,
            'deskripsi' => 'Biologi Kurikulum Merdeka',
        ]);

        $this->lm = LingkupMateri::create([
            'mata_pelajaran_id' => $this->mapel->id,
            'kode_lm' => 'Materi 1',
            'judul_materi' => 'Keanekaragaman Hayati',
            'urutan' => 1,
        ]);

        $this->tp1 = TujuanPembelajaran::create([
            'lingkup_materi_id' => $this->lm->id,
            'kode_tp' => 'TP 1.1',
            'deskripsi_tp' => 'Mengidentifikasi keanekaragaman gen dan jenis',
            'target_ketercapaian' => 75.00,
        ]);

        $this->tp2 = TujuanPembelajaran::create([
            'lingkup_materi_id' => $this->lm->id,
            'kode_tp' => 'TP 1.2',
            'deskripsi_tp' => 'Menganalisis ancaman kepunahan flora fauna',
            'target_ketercapaian' => 75.00,
        ]);
    }

    public function test_can_download_excel_template(): void
    {
        $response = $this->get(route('penilaian.template-excel', [
            'mata_pelajaran_id' => $this->mapel->id,
            'kelas' => 'X-A',
        ]));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }

    public function test_can_import_excel_matrix_with_two_tier_headers(): void
    {
        // Buat file spreadsheet dummy dengan struktur:
        // Row 1: NO | NAMA SISWA | NISN | Materi 1: Keanekaragaman Hayati | Materi 1: Keanekaragaman Hayati | Materi 2: Ekosistem
        // Row 2: NO | NAMA SISWA | NISN | TP 1.1                          | TP 1.2                          | TP 2.1
        // Row 3: 1  | Budi Santoso| 3141112233 | 88                             | 60                              | 92

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Row 1 (Lingkup Materi)
        $sheet->setCellValue('A1', 'NO');
        $sheet->setCellValue('B1', 'NAMA SISWA');
        $sheet->setCellValue('C1', 'NISN');
        $sheet->setCellValue('D1', 'Materi 1: Keanekaragaman Hayati');
        $sheet->setCellValue('E1', 'Materi 1: Keanekaragaman Hayati');
        $sheet->setCellValue('F1', 'Materi 2: Ekosistem dan Lingkungan');

        // Row 2 (Kode TP)
        $sheet->setCellValue('A2', 'NO');
        $sheet->setCellValue('B2', 'NAMA SISWA');
        $sheet->setCellValue('C2', 'NISN');
        $sheet->setCellValue('D2', 'TP 1.1');
        $sheet->setCellValue('E2', 'TP 1.2');
        $sheet->setCellValue('F2', 'TP 2.1');

        // Row 3 (Data Siswa 1)
        $sheet->setCellValue('A3', 1);
        $sheet->setCellValue('B3', 'Budi Santoso');
        $sheet->setCellValue('C3', '3141112233');
        $sheet->setCellValue('D3', 88); // TP 1.1 -> Tuntas (>= 75)
        $sheet->setCellValue('E3', 60); // TP 1.2 -> Belum Tuntas (< 75)
        $sheet->setCellValue('F3', 92); // TP 2.1 (Auto-created TP) -> Tuntas

        // Row 4 (Data Siswa 2)
        $sheet->setCellValue('A4', 2);
        $sheet->setCellValue('B4', 'Citra Dewi');
        $sheet->setCellValue('C4', '3142223344');
        $sheet->setCellValue('D4', 95);
        $sheet->setCellValue('E4', 80);
        $sheet->setCellValue('F4', 78);

        $tempPath = tempnam(sys_get_temp_dir(), 'excel_test_') . '.xlsx';
        $writer = new Xlsx($spreadsheet);
        $writer->save($tempPath);

        $uploadedFile = new UploadedFile($tempPath, 'Test_Import_Nilai.xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', null, true);

        $response = $this->post(route('penilaian.import-excel'), [
            'file_excel' => $uploadedFile,
            'mata_pelajaran_id' => $this->mapel->id,
            'kelas' => 'X-A',
        ]);

        $response->assertRedirect(route('penilaian.matrix', [
            'mata_pelajaran_id' => $this->mapel->id,
            'kelas' => 'X-A',
        ]));
        $response->assertSessionHas('success');

        // Verifikasi Siswa Terdaftar
        $this->assertDatabaseHas('siswas', [
            'nisn' => '3141112233',
            'nama' => 'Budi Santoso',
            'kelas' => 'X-A',
        ]);

        $this->assertDatabaseHas('siswas', [
            'nisn' => '3142223344',
            'nama' => 'Citra Dewi',
            'kelas' => 'X-A',
        ]);

        // Verifikasi Auto-created Lingkup Materi & TP baru (Materi 2 & TP 2.1)
        $this->assertDatabaseHas('lingkup_materis', [
            'mata_pelajaran_id' => $this->mapel->id,
            'kode_lm' => 'Materi 2',
        ]);

        $this->assertDatabaseHas('tujuan_pembelajarans', [
            'kode_tp' => 'TP 2.1',
        ]);

        // Verifikasi Nilai dan Status Ketuntasan
        $budi = Siswa::where('nisn', '3141112233')->first();
        $this->assertNotNull($budi);

        // TP 1.1 (88 -> Tuntas)
        $this->assertDatabaseHas('nilai_sumatif_tps', [
            'siswa_id' => $budi->id,
            'tujuan_pembelajaran_id' => $this->tp1->id,
            'nilai' => 88,
            'status_ketuntasan' => 'Tuntas',
        ]);

        // TP 1.2 (60 -> Belum Tuntas)
        $this->assertDatabaseHas('nilai_sumatif_tps', [
            'siswa_id' => $budi->id,
            'tujuan_pembelajaran_id' => $this->tp2->id,
            'nilai' => 60,
            'status_ketuntasan' => 'Belum Tuntas',
        ]);

        // Verifikasi Personalisasi AI tergenerate
        $this->assertDatabaseHas('personalisasis', [
            'siswa_id' => $budi->id,
        ]);

        if (file_exists($tempPath)) {
            @unlink($tempPath);
        }
    }
}
