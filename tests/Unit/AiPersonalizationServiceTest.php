<?php

namespace Tests\Unit;

use App\Models\LingkupMateri;
use App\Models\MataPelajaran;
use App\Models\NilaiSumatifTp;
use App\Models\Personalisasi;
use App\Models\Siswa;
use App\Models\TujuanPembelajaran;
use App\Services\AiPersonalizationService;
use App\Services\PersonalizationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AiPersonalizationServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_fallback_generates_and_persists_to_database(): void
    {
        $siswa = Siswa::create([
            'nisn' => 'AITEST01',
            'nama' => 'Test Siswa SMA',
            'kelas' => 'X-A',
            'email' => 'aistudent@example.com',
            'gaya_belajar' => 'Visual',
            'status' => 'Aktif',
        ]);

        $mapel = MataPelajaran::first();
        $lm = LingkupMateri::first();
        $tp = TujuanPembelajaran::first();

        NilaiSumatifTp::create([
            'siswa_id' => $siswa->id,
            'tujuan_pembelajaran_id' => $tp->id,
            'nilai' => 88.00,
            'status_ketuntasan' => 'Tuntas',
        ]);

        $service = new AiPersonalizationService();
        $personalisasi = $service->generateForSiswa($siswa);

        $this->assertNotNull($personalisasi);
        $this->assertDatabaseHas('personalisasis', [
            'siswa_id' => $siswa->id,
            'kategori_pemahaman' => 'Tinggi',
        ]);
        $this->assertEquals('Visual', $personalisasi->gaya_belajar_rekomendasi);

        // Cleanup
        NilaiSumatifTp::where('siswa_id', $siswa->id)->delete();
        Personalisasi::where('siswa_id', $siswa->id)->delete();
        $siswa->delete();
    }

    public function test_ai_mocked_gemini_api_call_and_db_persistence(): void
    {
        $siswa = Siswa::create([
            'nisn' => 'AITEST02',
            'nama' => 'Test Gemini Siswa',
            'kelas' => 'X-A',
            'email' => 'gemini@example.com',
            'gaya_belajar' => 'Kinestetik',
            'status' => 'Aktif',
        ]);

        $tp = TujuanPembelajaran::first();

        NilaiSumatifTp::create([
            'siswa_id' => $siswa->id,
            'tujuan_pembelajaran_id' => $tp->id,
            'nilai' => 60.00,
            'status_ketuntasan' => 'Belum Tuntas',
        ]);

        // Mock Gemini response
        Http::fake([
            'https://generativelanguage.googleapis.com/*' => Http::response([
                'candidates' => [
                    [
                        'content' => [
                            'parts' => [
                                [
                                    'text' => json_encode([
                                        'skor_performa' => 60.0,
                                        'kategori_pemahaman' => 'Perlu Pendampingan',
                                        'gaya_belajar_rekomendasi' => 'Kinestetik',
                                        'rekomendasi_materi' => 'Praktikum ulang pemodelan sistem gerak dengan torso dan lembar kerja terbimbing.',
                                        'rekomendasi_aktivitas' => "• Sesi bimbingan remedial khusus TP 1.1\n• Tes formatif ulang setelah praktikum",
                                        'catatan_guru' => 'Siswa memerlukan penguatan pada konsep organ gerak melalui pendekatan kinestetik.',
                                    ])
                                ]
                            ]
                        ]
                    ]
                ]
            ], 200)
        ]);

        $service = new AiPersonalizationService();
        $personalisasi = $service->generateForSiswa($siswa);

        $this->assertNotNull($personalisasi);
        $this->assertEquals('Perlu Pendampingan', $personalisasi->kategori_pemahaman);
        $this->assertStringContainsString('Praktikum ulang', $personalisasi->rekomendasi_materi);
        $this->assertDatabaseHas('personalisasis', [
            'siswa_id' => $siswa->id,
            'kategori_pemahaman' => 'Perlu Pendampingan',
        ]);

        // Cleanup
        NilaiSumatifTp::where('siswa_id', $siswa->id)->delete();
        Personalisasi::where('siswa_id', $siswa->id)->delete();
        $siswa->delete();
    }
}
