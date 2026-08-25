<?php

namespace Tests\Feature;

use App\Models\LingkupMateri;
use App\Models\MataPelajaran;
use App\Models\NilaiSumatifTp;
use App\Models\Personalisasi;
use App\Models\Setting;
use App\Models\Siswa;
use App\Models\TujuanPembelajaran;
use App\Services\AiPersonalizationService;
use App\Services\ExcelAssessmentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Tests\TestCase;

class ComprehensiveAssessmentEdgeCasesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_ai_personalization_handles_student_with_zero_scores(): void
    {
        $siswa = Siswa::create([
            'nisn' => 'ZERO001',
            'nama' => 'Siswa Baru Tanpa Nilai',
            'kelas' => 'X-A',
            'gaya_belajar' => 'Auditori',
            'status' => 'Aktif',
        ]);

        $service = new AiPersonalizationService();
        $personalisasi = $service->generateForSiswa($siswa);

        $this->assertNotNull($personalisasi);
        $this->assertEquals($siswa->id, $personalisasi->siswa_id);
        $this->assertEquals(0, $personalisasi->skor_performa);
        $this->assertEquals('Auditori', $personalisasi->gaya_belajar_rekomendasi);
        $this->assertStringContainsString('Siswa baru terdaftar', $personalisasi->catatan_guru);
    }

    public function test_ai_personalization_handles_perfect_score_student(): void
    {
        $siswa = Siswa::create([
            'nisn' => 'HIGH001',
            'nama' => 'Siswa Jenius',
            'kelas' => 'X-A',
            'gaya_belajar' => 'Visual',
            'status' => 'Aktif',
        ]);

        $tps = TujuanPembelajaran::take(3)->get();
        foreach ($tps as $tp) {
            NilaiSumatifTp::create([
                'siswa_id' => $siswa->id,
                'tujuan_pembelajaran_id' => $tp->id,
                'nilai' => 98.00,
                'status_ketuntasan' => 'Tuntas',
            ]);
        }

        $service = new AiPersonalizationService();
        $personalisasi = $service->generateForSiswa($siswa);

        $this->assertEquals('Tinggi', $personalisasi->kategori_pemahaman);
        $this->assertEquals(98.00, $personalisasi->skor_performa);
        $this->assertStringContainsString('Tutor Sebaya', $personalisasi->rekomendasi_aktivitas);
    }

    public function test_ai_personalization_handles_all_remedial_score_student(): void
    {
        $siswa = Siswa::create([
            'nisn' => 'LOW001',
            'nama' => 'Siswa Butuh Bimbingan',
            'kelas' => 'X-A',
            'gaya_belajar' => 'Kinestetik',
            'status' => 'Aktif',
        ]);

        $tps = TujuanPembelajaran::take(3)->get();
        foreach ($tps as $tp) {
            NilaiSumatifTp::create([
                'siswa_id' => $siswa->id,
                'tujuan_pembelajaran_id' => $tp->id,
                'nilai' => 50.00,
                'status_ketuntasan' => 'Belum Tuntas',
            ]);
        }

        $service = new AiPersonalizationService();
        $personalisasi = $service->generateForSiswa($siswa);

        $this->assertEquals('Perlu Pendampingan', $personalisasi->kategori_pemahaman);
        $this->assertEquals(50.00, $personalisasi->skor_performa);
        $this->assertEquals(3, $personalisasi->tp_belum_tuntas_count);
        $this->assertStringContainsString('remedial', strtolower($personalisasi->rekomendasi_aktivitas));
    }

    public function test_excel_import_handles_comma_decimals_and_spaces(): void
    {
        $mapel = MataPelajaran::first();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Row 1
        $sheet->setCellValue('A1', 'NO');
        $sheet->setCellValue('B1', 'NAMA SISWA');
        $sheet->setCellValue('C1', 'NISN');
        $sheet->setCellValue('D1', 'Materi 1: Keanekaragaman Hayati');
        $sheet->setCellValue('E1', 'Materi 1: Keanekaragaman Hayati');

        // Row 2
        $sheet->setCellValue('A2', 'NO');
        $sheet->setCellValue('B2', 'NAMA SISWA');
        $sheet->setCellValue('C2', 'NISN');
        $sheet->setCellValue('D2', 'TP 1.1');
        $sheet->setCellValue('E2', 'TP 1.2');

        // Row 3 (With Indonesian comma decimal '85,5' and string with spaces ' 62,5 ')
        $sheet->setCellValue('A3', 1);
        $sheet->setCellValue('B3', 'Rian Hidayat');
        $sheet->setCellValue('C3', '3149998877');
        $sheet->setCellValue('D3', '85,5');
        $sheet->setCellValue('E3', ' 62,5 ');

        $tempPath = tempnam(sys_get_temp_dir(), 'edge_excel_') . '.xlsx';
        $writer = new Xlsx($spreadsheet);
        $writer->save($tempPath);

        $uploadedFile = new UploadedFile($tempPath, 'Edge_Test.xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', null, true);

        $service = app(ExcelAssessmentService::class);
        $result = $service->importExcel($uploadedFile, $mapel->id, 'X-A');

        $this->assertEquals(1, $result['students_count']);
        $this->assertEquals(2, $result['scores_count']);

        $rian = Siswa::where('nisn', '3149998877')->first();
        $this->assertNotNull($rian);

        $tp1 = TujuanPembelajaran::where('kode_tp', 'TP 1.1')->first();
        $tp2 = TujuanPembelajaran::where('kode_tp', 'TP 1.2')->first();

        $score1 = NilaiSumatifTp::where('siswa_id', $rian->id)->where('tujuan_pembelajaran_id', $tp1->id)->first();
        $score2 = NilaiSumatifTp::where('siswa_id', $rian->id)->where('tujuan_pembelajaran_id', $tp2->id)->first();

        $this->assertEquals(85.5, $score1->nilai);
        $this->assertEquals('Tuntas', $score1->status_ketuntasan);

        $this->assertEquals(62.5, $score2->nilai);
        $this->assertEquals('Belum Tuntas', $score2->status_ketuntasan);

        if (file_exists($tempPath)) {
            @unlink($tempPath);
        }
    }

    public function test_pages_render_without_errors_when_no_mapel_exists(): void
    {
        // Delete all subjects to test empty state views
        TujuanPembelajaran::query()->delete();
        LingkupMateri::query()->delete();
        NilaiSumatifTp::query()->delete();
        Personalisasi::query()->delete();
        MataPelajaran::query()->delete();

        // 1. Matrix View
        $response = $this->get(route('penilaian.matrix'));
        $response->assertStatus(200);
        $response->assertSee('Belum Ada Mata Pelajaran Terdaftar');

        // 2. TP Index View
        $response = $this->get(route('tujuan-pembelajaran.index'));
        $response->assertStatus(200);
        $response->assertSee('Belum Ada Mata Pelajaran');

        // 3. Penilaian Index View
        $response = $this->get(route('penilaian.index'));
        $response->assertStatus(200);
        $response->assertSee('Data Penilaian Sumatif TP');

        // 4. Laporan Index View
        $response = $this->get(route('laporan.index'));
        $response->assertStatus(200);
        $response->assertSee('Rekapitulasi Capaian TP');
    }

    public function test_settings_can_be_updated_successfully(): void
    {
        $response = $this->post(route('setting.update'), [
            'nama_institusi' => 'SMA Unggulan Merdeka 1',
            'tahun_akademik' => '2026/2027 Ganjil',
            'kkm_default' => 78,
            'kurikulum' => 'Kurikulum Merdeka SMA',
            'kepala_sekolah' => 'Drs. Ahmad Dahlan, M.Pd.',
            'nip_kepala_sekolah' => '197001011995011001',
            'ai_provider' => 'gemini',
        ]);

        $response->assertRedirect(route('setting.index'));
        $response->assertSessionHas('success');

        $this->assertEquals('SMA Unggulan Merdeka 1', Setting::get('nama_institusi'));
        $this->assertEquals('78', Setting::get('kkm_default'));
    }
}
