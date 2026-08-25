<?php

namespace Tests\Feature;

use App\Models\LingkupMateri;
use App\Models\MataPelajaran;
use App\Models\NilaiSumatifTp;
use App\Models\Personalisasi;
use App\Models\Siswa;
use App\Models\TujuanPembelajaran;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KurikulumMerdekaAssessmentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_dashboard_renders_successfully(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('Dashboard Analitik Kurikulum Merdeka');
        $response->assertSee('Total Siswa SMA');
        $response->assertSee('Tujuan Pembelajaran');
    }

    public function test_siswa_crud_lifecycle(): void
    {
        $response = $this->get('/siswa');
        $response->assertStatus(200);
        $response->assertSee('Data Siswa SMA');

        $testNisn = '9999000111';

        // Create
        $response = $this->post('/siswa', [
            'nisn' => $testNisn,
            'nama' => 'Siswa Pengujian Baru',
            'kelas' => 'X-A',
            'email' => 'siswa.test@sman1merdeka.sch.id',
            'gaya_belajar' => 'Kinestetik',
            'status' => 'Aktif',
        ]);
        $response->assertRedirect('/siswa');

        $siswa = Siswa::where('nisn', $testNisn)->first();
        $this->assertNotNull($siswa);
        $this->assertEquals('Siswa Pengujian Baru', $siswa->nama);

        // Show
        $response = $this->get("/siswa/{$siswa->id}");
        $response->assertStatus(200);
        $response->assertSee($testNisn);

        // Edit
        $response = $this->get("/siswa/{$siswa->id}/edit");
        $response->assertStatus(200);

        // Update
        $response = $this->put("/siswa/{$siswa->id}", [
            'nisn' => $testNisn,
            'nama' => 'Siswa Pengujian Updated',
            'kelas' => 'XI-IPA 1',
            'email' => 'siswa.test@sman1merdeka.sch.id',
            'gaya_belajar' => 'Auditori',
            'status' => 'Aktif',
        ]);
        $response->assertRedirect('/siswa');
        $this->assertEquals('Siswa Pengujian Updated', $siswa->fresh()->nama);

        // Destroy
        $response = $this->delete("/siswa/{$siswa->id}");
        $response->assertRedirect('/siswa');
        $this->assertNull(Siswa::find($siswa->id));
    }

    public function test_matapelajaran_and_tp_crud(): void
    {
        $response = $this->get('/matapelajaran');
        $response->assertStatus(200);
        $response->assertSee('Mata Pelajaran SMA');

        $testKode = 'TEST-MAPEL';

        // Create Mapel
        $response = $this->post('/matapelajaran', [
            'kode_mapel' => $testKode,
            'nama_mapel' => 'Biologi Terapan Pengujian',
            'fase' => 'Fase E',
            'tingkat' => 'Kelas X',
            'kkm' => 75.00,
            'deskripsi' => 'Mapel pengujian',
        ]);
        $response->assertRedirect('/matapelajaran');

        $mapel = MataPelajaran::where('kode_mapel', $testKode)->first();
        $this->assertNotNull($mapel);

        // Create Lingkup Materi
        $response = $this->post('/lingkup-materi', [
            'mata_pelajaran_id' => $mapel->id,
            'kode_lm' => 'Materi Uji',
            'judul_materi' => 'Lingkup Materi Testing',
            'urutan' => 1,
        ]);
        $response->assertRedirect("/tujuan-pembelajaran?mata_pelajaran_id={$mapel->id}");

        $lm = LingkupMateri::where('mata_pelajaran_id', $mapel->id)->first();
        $this->assertNotNull($lm);

        // Create TP
        $response = $this->post('/tujuan-pembelajaran', [
            'lingkup_materi_id' => $lm->id,
            'kode_tp' => 'TP Uji 1.1',
            'deskripsi_tp' => 'Menganalisis deskripsi TP testing',
            'target_ketercapaian' => 75.00,
            'urutan' => 1,
        ]);
        $response->assertRedirect("/tujuan-pembelajaran?mata_pelajaran_id={$mapel->id}");

        $tp = TujuanPembelajaran::where('lingkup_materi_id', $lm->id)->first();
        $this->assertNotNull($tp);

        // Cleanup
        $tp->delete();
        $lm->delete();
        $mapel->delete();
    }

    public function test_penilaian_matrix_and_batch_save(): void
    {
        $response = $this->get('/penilaian');
        $response->assertStatus(200);
        $response->assertSee('Data Penilaian Sumatif TP');

        $response = $this->get('/penilaian/matrix');
        $response->assertStatus(200);
        $response->assertSee('NILAI SUMATIF PER TUJUAN PEMBELAJARAN SETIAP LINGKUP MATERI');

        $siswa = Siswa::first();
        $tp = TujuanPembelajaran::first();
        $mapel = MataPelajaran::first();

        // Batch matrix save
        $response = $this->post('/penilaian/matrix', [
            'mata_pelajaran_id' => $mapel->id,
            'kelas' => $siswa->kelas,
            'scores' => [
                $siswa->id => [
                    $tp->id => 92.00,
                ]
            ]
        ]);
        $response->assertRedirect("/penilaian/matrix?mata_pelajaran_id={$mapel->id}&kelas={$siswa->kelas}");

        $savedScore = NilaiSumatifTp::where('siswa_id', $siswa->id)
            ->where('tujuan_pembelajaran_id', $tp->id)
            ->first();

        $this->assertNotNull($savedScore);
        $this->assertEquals(92.00, $savedScore->nilai);
        $this->assertEquals('Tuntas', $savedScore->status_ketuntasan);
    }

    public function test_personalisasi_and_ai_routes(): void
    {
        $response = $this->get('/personalisasi');
        $response->assertStatus(200);
        $response->assertSee('Engine Personalisasi AI Kurikulum Merdeka');

        $siswa = Siswa::first();
        if ($siswa) {
            $response = $this->get("/personalisasi/{$siswa->id}");
            $response->assertStatus(200);
            $response->assertSee('Profil Personalisasi & Diagnostik TP', false);

            // Update catatan guru
            $response = $this->post("/personalisasi/{$siswa->id}/catatan", [
                'catatan_guru' => 'Catatan bimbingan khusus dari automated feature test.',
            ]);
            $response->assertRedirect("/personalisasi/{$siswa->id}");
            $this->assertDatabaseHas('personalisasis', [
                'siswa_id' => $siswa->id,
                'catatan_guru' => 'Catatan bimbingan khusus dari automated feature test.',
            ]);

            // Single AI analysis route
            $response = $this->post("/personalisasi/{$siswa->id}/ai-analyze");
            $response->assertRedirect("/personalisasi/{$siswa->id}");
            $response->assertSessionHas('success');
        }

        // Batch generate
        $response = $this->post('/personalisasi/generate');
        $response->assertRedirect('/personalisasi');
    }

    public function test_laporan_pages(): void
    {
        $response = $this->get('/laporan');
        $response->assertStatus(200);
        $response->assertSee('Laporan & Rekapitulasi Capaian TP');

        $response = $this->get('/laporan/cetak');
        $response->assertStatus(200);
        $response->assertSee('REKAPITULASI NILAI SUMATIF PER TUJUAN PEMBELAJARAN (TP)');
    }

    public function test_setting_page_and_update(): void
    {
        $response = $this->get('/setting');
        $response->assertStatus(200);
        $response->assertSee('Pengaturan Sistem Sekolah & AI Engine');

        // Valid update
        $response = $this->post('/setting', [
            'nama_institusi' => 'SMA Negeri 1 Merdeka Unggulan',
            'tahun_akademik' => '2025/2026 Ganjil',
            'kkm_default' => 75.00,
            'kepala_sekolah' => 'Dr. H. Bambang Sutrisno, M.Pd.',
            'nip_kepala_sekolah' => '196805121994031005',
            'kurikulum' => 'Kurikulum Merdeka (Fase E & F)',
            'ai_provider' => 'gemini',
            'recalculate' => 1,
        ]);
        $response->assertRedirect('/setting');
    }
}
