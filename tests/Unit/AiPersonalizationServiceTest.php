<?php

namespace Tests\Unit;

use App\Models\LingkupMateri;
use App\Models\MataPelajaran;
use App\Models\NilaiSumatifTp;
use App\Models\Personalisasi;
use App\Models\Setting;
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

        \App\Models\Setting::set('ai_provider', 'gemini');

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

    public function test_ai_mocked_openai_api_call_and_db_persistence(): void
    {
        $siswa = Siswa::create([
            'nisn' => 'AITEST03',
            'nama' => 'Test OpenAI Siswa',
            'kelas' => 'X-A',
            'email' => 'openai@example.com',
            'gaya_belajar' => 'Visual',
            'status' => 'Aktif',
        ]);

        $tp = TujuanPembelajaran::first();

        NilaiSumatifTp::create([
            'siswa_id' => $siswa->id,
            'tujuan_pembelajaran_id' => $tp->id,
            'nilai' => 95.00,
            'status_ketuntasan' => 'Tuntas',
        ]);

        \App\Models\Setting::set('ai_provider', 'openai');

        // Mock OpenAI response
        Http::fake([
            'https://api.openai.com/*' => Http::response([
                'choices' => [
                    [
                        'message' => [
                            'content' => json_encode([
                                'skor_performa' => 95.0,
                                'kategori_pemahaman' => 'Tinggi',
                                'gaya_belajar_rekomendasi' => 'Visual',
                                'rekomendasi_materi' => 'Pengayaan analisis komparatif sistem gerak vertebrata.',
                                'rekomendasi_aktivitas' => "• Proyek mandiri infografis anatomi\n• Menjadi tutor sebaya",
                                'catatan_guru' => 'Siswa memiliki penguasaan materi yang sangat baik.',
                                'analisis_per_tp' => [
                                    [
                                        'kode_tp' => $tp->kode_tp,
                                        'nilai' => 95.0,
                                        'status_ketuntasan' => 'Tuntas',
                                        'interpretasi_capaian' => 'Sangat Baik. Menguasai materi dengan sangat memuaskan.',
                                        'tingkat_pemahaman_materi' => 'Tingkat pemahaman tinggi terhadap organ gerak.',
                                        'aspek_tindak_lanjut' => 'Perlu dipertahankan: penguasaan struktur organ gerak.',
                                        'umpan_balik_siswa' => 'Kerja yang luar biasa!',
                                        'rekomendasi_siswa' => 'Eksplorasi hubungan antar sistem organ.',
                                        'rekomendasi_guru' => 'Berikan modul studi kasus anatomi perbandingan.',
                                    ]
                                ]
                            ])
                        ]
                    ]
                ]
            ], 200)
        ]);

        $service = new AiPersonalizationService();
        $personalisasi = $service->generateForSiswa($siswa);

        $this->assertNotNull($personalisasi);
        $this->assertEquals('Tinggi', $personalisasi->kategori_pemahaman);
        $this->assertStringContainsString('Pengayaan analisis komparatif', $personalisasi->rekomendasi_materi);
        $this->assertEquals('OpenAI', $personalisasi->sumber_analisis);

        // Cleanup
        NilaiSumatifTp::where('siswa_id', $siswa->id)->delete();
        Personalisasi::where('siswa_id', $siswa->id)->delete();
        $siswa->delete();
    }

    public function test_generate_for_siswa_using_router9_api_when_configured(): void
    {
        Setting::set('ai_provider', 'router9');
        Setting::set('router9_api_key', 'sk-r9k-fake-test-key');
        Setting::set('router9_model', 'google/gemini-2.5-flash');
        Setting::set('router9_base_url', 'https://api.router9.com/v1');

        $siswa = Siswa::create([
            'nisn' => '9999999993',
            'nama' => 'Siswa Router9 Test',
            'kelas' => 'X-A',
            'gaya_belajar' => 'Visual',
        ]);

        $mp = MataPelajaran::firstOrCreate(
            ['kode_mapel' => 'BIO-TEST'],
            ['nama_mapel' => 'Biologi Test', 'fase' => 'E', 'tingkat' => 10]
        );

        $lm = LingkupMateri::firstOrCreate(
            ['mata_pelajaran_id' => $mp->id, 'kode_lm' => 'LM-R9'],
            ['judul_materi' => 'Materi Router9 Test']
        );

        $tp = TujuanPembelajaran::firstOrCreate(
            ['lingkup_materi_id' => $lm->id, 'kode_tp' => 'TP-R9'],
            ['deskripsi_tp' => 'Memahami ekosistem via Router9', 'target_ketercapaian' => 75.0]
        );

        NilaiSumatifTp::create([
            'siswa_id' => $siswa->id,
            'tujuan_pembelajaran_id' => $tp->id,
            'nilai' => 92.0,
            'status_ketuntasan' => 'Tuntas',
        ]);

        Http::fake([
            'https://api.router9.com/*' => Http::response([
                'choices' => [
                    [
                        'message' => [
                            'content' => json_encode([
                                'skor_performa' => 92.0,
                                'kategori_pemahaman' => 'Tinggi',
                                'gaya_belajar_rekomendasi' => 'Visual',
                                'rekomendasi_materi' => 'Pengayaan analisis ekosistem digital.',
                                'rekomendasi_aktivitas' => "• Membuat visualisasi ekosistem\n• Presentasi kelompok",
                                'catatan_guru' => 'Rekomendasi Guru (AI): Pemahaman siswa luar biasa.',
                                'analisis_per_tp' => [
                                    [
                                        'kode_tp' => $tp->kode_tp,
                                        'nilai' => 92.0,
                                        'status_ketuntasan' => 'Tuntas',
                                        'interpretasi_capaian' => 'Sangat Baik. Menguasai materi ekosistem.',
                                        'tingkat_pemahaman_materi' => 'Pemahaman materi sangat kuat.',
                                        'aspek_tindak_lanjut' => 'Perlu dipertahankan: konsep jaring-jaring makanan.',
                                        'umpan_balik_siswa' => 'Terus pertahankan semangat belajarmu!',
                                        'rekomendasi_siswa' => 'Pelajari dinamika populasi lanjutan.',
                                        'rekomendasi_guru' => 'Berikan tugas analisis data lingkungan.',
                                    ]
                                ]
                            ])
                        ]
                    ]
                ]
            ], 200)
        ]);

        $service = new AiPersonalizationService();
        $personalisasi = $service->generateForSiswa($siswa);

        $this->assertNotNull($personalisasi);
        $this->assertEquals('Tinggi', $personalisasi->kategori_pemahaman);
        $this->assertEquals('Router9', $personalisasi->sumber_analisis);
        $this->assertStringContainsString('Router9', $personalisasi->keterangan_sumber);

        $badge = $personalisasi->sumber_analisis_badge;
        $this->assertEquals('Router9 Gemini', $badge['nama']);
        $this->assertEquals('router9', $badge['tipe']);

        // Cleanup
        NilaiSumatifTp::where('siswa_id', $siswa->id)->delete();
        Personalisasi::where('siswa_id', $siswa->id)->delete();
        $siswa->delete();
    }

    public function test_generate_for_siswa_using_router9_fleet_model(): void
    {
        Setting::set('ai_provider', 'router9');
        Setting::set('router9_api_key', 'sk-r9k-fake-test-key');
        Setting::set('router9_model', 'ag/gemini-3.8-flash-high');
        Setting::set('router9_base_url', 'https://api.router9.com/v1');

        $siswa = Siswa::create([
            'nisn' => '9999999994',
            'nama' => 'Siswa Fleet Test',
            'kelas' => 'X-B',
            'gaya_belajar' => 'Auditori',
        ]);

        $mp = MataPelajaran::firstOrCreate(
            ['kode_mapel' => 'KIM-TEST'],
            ['nama_mapel' => 'Kimia Test', 'fase' => 'E', 'tingkat' => 10]
        );

        $lm = LingkupMateri::firstOrCreate(
            ['mata_pelajaran_id' => $mp->id, 'kode_lm' => 'LM-R9-2'],
            ['judul_materi' => 'Materi Reaksi Kimia']
        );

        $tp = TujuanPembelajaran::firstOrCreate(
            ['lingkup_materi_id' => $lm->id, 'kode_tp' => 'TP-R9-2'],
            ['deskripsi_tp' => 'Menganalisis stoikiometri', 'target_ketercapaian' => 75.0]
        );

        NilaiSumatifTp::create([
            'siswa_id' => $siswa->id,
            'tujuan_pembelajaran_id' => $tp->id,
            'nilai' => 88.0,
            'status_ketuntasan' => 'Tuntas',
        ]);

        Http::fake([
            'https://api.router9.com/*' => Http::response([
                'choices' => [
                    [
                        'message' => [
                            'content' => json_encode([
                                'skor_performa' => 88.0,
                                'kategori_pemahaman' => 'Tinggi',
                                'gaya_belajar_rekomendasi' => 'Auditori',
                                'rekomendasi_materi' => 'Pendalaman konsep stoikiometri.',
                                'rekomendasi_aktivitas' => "• Diskusi kelompok audio\n• Penjelasan rekaman",
                                'catatan_guru' => 'Rekomendasi Guru (AI): Siswa memahami stoikiometri dengan sangat baik.',
                                'analisis_per_tp' => []
                            ])
                        ]
                    ]
                ]
            ], 200)
        ]);

        $service = new AiPersonalizationService();
        $personalisasi = $service->generateForSiswa($siswa);

        $this->assertNotNull($personalisasi);
        $this->assertEquals('Router9', $personalisasi->sumber_analisis);
        $this->assertStringContainsString('ag/gemini-3.8-flash-high', $personalisasi->keterangan_sumber);

        $badge = $personalisasi->sumber_analisis_badge;
        $this->assertEquals('ag/gemini-3.8-flash-high', $badge['model']);
        $this->assertEquals('Router9 Gemini', $badge['nama']);

        // Cleanup
        NilaiSumatifTp::where('siswa_id', $siswa->id)->delete();
        Personalisasi::where('siswa_id', $siswa->id)->delete();
        $siswa->delete();
    }

    public function test_test_connection_for_fallback_and_router9(): void
    {
        $service = app(AiPersonalizationService::class);
        $fallback = $service->testConnection('fallback');
        $this->assertTrue($fallback['success']);
        $this->assertEquals('Mode Heuristik Offline', $fallback['provider']);

        Http::fake([
            'https://api.router9.com/*' => Http::response([
                'choices' => [
                    ['message' => ['content' => 'Koneksi Router9 Berhasil.']]
                ]
            ], 200),
        ]);

        Setting::set('router9_api_key', 'test-key');
        Setting::set('router9_base_url', 'https://api.router9.com/v1');
        $r9 = $service->testConnection('router9');
        $this->assertTrue($r9['success']);
        $this->assertEquals('Router9 Gateway', $r9['provider']);
    }
}
