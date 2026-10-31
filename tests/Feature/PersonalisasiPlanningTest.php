<?php

namespace Tests\Feature;

use App\Models\Siswa;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PersonalisasiPlanningTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    /**
     * Requirement 1:
     * Rekomendasi AI untuk setiap TP akan ditampilkan di bawah deskripsi TP masing-masing
     * pada kolom Riwayat Nilai per TP
     */
    public function test_rekomendasi_ai_per_tp_displayed_under_tp_description(): void
    {
        $siswa = Siswa::first();
        $this->assertNotNull($siswa);

        $response = $this->get("/personalisasi/{$siswa->id}");
        $response->assertStatus(200);

        // Verify table and TP recommendation elements
        $response->assertSee('Riwayat Nilai per Tujuan Pembelajaran (TP)');
        $response->assertSee('Deskripsi TP & Rekomendasi AI', false);
        $response->assertSee('Rekomendasi AI (', false);

        // Check that NilaiSumatifTp has catatan filled
        $firstTp = $siswa->nilaiSumatifTps()->first();
        if ($firstTp) {
            $this->assertNotEmpty($firstTp->catatan);
            $response->assertSee($firstTp->catatan);
        }
    }

    /**
     * Requirement 2:
     * Rangkuman rekomendasi dan analisis AI akan dituliskan pada kolom Rekomendasi bahan
     * ajar modul terarah dan Strategi Aktivasi pembelajaran Adaptif
     */
    public function test_rangkuman_rekomendasi_dan_strategi_adaptif_displayed(): void
    {
        $siswa = Siswa::first();
        $response = $this->get("/personalisasi/{$siswa->id}");
        $response->assertStatus(200);

        // Verify card headers and summaries
        $response->assertSee('Rekomendasi Bahan Ajar & Modul Terarah', false);
        $response->assertSee('Rangkuman bahan ajar terarah per butir TP dan modalitas belajar');
        $response->assertSee('Strategi Aktivitas Pembelajaran Adaptif');
        $response->assertSee('Rangkuman analisis strategi aktivasi pembelajaran adaptif Kurikulum Merdeka');
    }

    /**
     * Requirement 3:
     * Status diagnostik AI juga diisi oleh AI (ada tulisan rekomendasi guru). Tetapi kalau guru ingin
     * mengubah atau menambahnya, dapat dengan mengisi kolom paling bawah dulu, lalu
     * disimpan (kolom Catatan Diagnostik dan Rencana Intervensi Guru)
     */
    public function test_status_diagnostik_ai_default_and_teacher_override(): void
    {
        $siswa = Siswa::first();

        // 1. Initial show: filled by AI with "Rekomendasi Guru (AI)"
        $response = $this->get("/personalisasi/{$siswa->id}");
        $response->assertStatus(200);
        $response->assertSee('Status Diagnostik AI');
        $response->assertSee('Ulasan Evaluasi & Rekomendasi Guru (AI):', false);
        $response->assertSee('Rekomendasi Guru (AI):');

        // Verify bottom form exists
        $response->assertSee('Catatan Diagnostik & Rencana Intervensi Guru', false);
        $response->assertSee('id="catatan_guru_section"', false);
        $response->assertSee('Simpan Catatan Guru');

        // 2. Teacher modifies and submits form at the bottom
        $customGuruNote = 'Guru Pengampu: Siswa telah diberikan penugasan pengayaan khusus mandiri dan tutor sebaya.';
        $postResponse = $this->post("/personalisasi/{$siswa->id}/catatan", [
            'catatan_guru' => $customGuruNote,
        ]);

        $postResponse->assertRedirect("/personalisasi/{$siswa->id}");
        $postResponse->assertSessionHas('success');

        // 3. Database updated
        $this->assertDatabaseHas('personalisasis', [
            'siswa_id' => $siswa->id,
            'catatan_guru' => $customGuruNote,
        ]);

        // 4. View reflects custom teacher note in the top Status Diagnostik AI box
        $reloadResponse = $this->get("/personalisasi/{$siswa->id}");
        $reloadResponse->assertStatus(200);
        $reloadResponse->assertSee($customGuruNote);
    }

    /**
     * Requirement:
     * Pada ulasan tidak boleh banyak dipuji jika nilai tidak bagus,
     * melainkan memberikan evaluasi objektif dan masukan yang membangun.
     */
    public function test_ulasan_evaluasi_tidak_memuji_berlebihan_jika_nilai_tidak_bagus_dan_memberi_masukan_membangun(): void
    {
        // Cari siswa dengan performa rendah / pendampingan (misal Dimas Anggara)
        $siswaRendah = Siswa::where('nama', 'Dimas Anggara')->first() ?? Siswa::whereHas('nilaiSumatifTps', function($q) {
            $q->where('status_ketuntasan', 'Belum Tuntas');
        })->first();

        $this->assertNotNull($siswaRendah);
        $personalisasi = $siswaRendah->personalisasis->first();
        $this->assertNotNull($personalisasi);

        // Pastikan ulasan tidak mengandung kata sanjungan berlebihan
        $catatan = $personalisasi->catatan_guru;
        $this->assertStringNotContainsString('sangat memuaskan', strtolower($catatan));
        $this->assertStringNotContainsString('sangat baik', strtolower($catatan));
        $this->assertStringNotContainsString('luar biasa', strtolower($catatan));

        // Pastikan ulasan memberikan masukan yang membangun & evaluasi kendala
        $this->assertTrue(
            str_contains($catatan, 'kendala') ||
            str_contains($catatan, 'bimbingan') ||
            str_contains($catatan, 'perhatian') ||
            str_contains($catatan, 'remedial')
        );

        $response = $this->get("/personalisasi/{$siswaRendah->id}");
        $response->assertStatus(200);
        $response->assertSee('Ulasan Evaluasi & Rekomendasi Guru (AI):', false);
    }

    /**
     * Requirement:
     * Pada analisis diagnostik menampilkan apa saja gaya belajarnya dan persentasenya berapa.
     */
    public function test_analisis_diagnostik_menampilkan_seluruh_gaya_belajar_dan_persentasenya(): void
    {
        $siswa = Siswa::first();
        $personalisasi = $siswa->personalisasis->first();
        $this->assertNotNull($personalisasi);

        $distribusi = $personalisasi->distribusi_gaya_belajar;
        $this->assertIsArray($distribusi);
        $this->assertArrayHasKey('Visual', $distribusi);
        $this->assertArrayHasKey('Auditori', $distribusi);
        $this->assertArrayHasKey('Kinestetik', $distribusi);
        $this->assertArrayHasKey('Baca dan Tulis', $distribusi);

        // Total persentase harus tepat 100%
        $this->assertEquals(100, array_sum($distribusi));

        // Halaman web menampilkan kartu distribusi VARK
        $response = $this->get("/personalisasi/{$siswa->id}");
        $response->assertStatus(200);
        $response->assertSee('Analisis Diagnostik Gaya Belajar Siswa (Modalitas VARK)');
        $response->assertSee('Visual');
        $response->assertSee('Auditori');
        $response->assertSee('Kinestetik');
        $response->assertSee('Baca dan Tulis');
        $response->assertSee('%');
    }

    /**
     * Requirement:
     * Pada analisis rekomendasi tindak lanjut membuat 3 rekomendasi per TP-nya.
     */
    public function test_analisis_rekomendasi_tindak_lanjut_membuat_3_rekomendasi_per_tp(): void
    {
        $siswa = Siswa::first();
        $response = $this->get("/personalisasi/{$siswa->id}");
        $response->assertStatus(200);

        // Pastikan setiap butir TP memiliki 3 butir rekomendasi
        $tps = $siswa->nilaiSumatifTps;
        $this->assertNotEmpty($tps);

        foreach ($tps as $nst) {
            $catatan = $nst->catatan;
            $this->assertNotEmpty($catatan);
            $lines = array_filter(array_map('trim', explode("\n", $catatan)));
            $this->assertGreaterThanOrEqual(3, count($lines), "TP {$nst->tujuanPembelajaran->kode_tp} harus memiliki minimal 3 rekomendasi.");
        }

        $response->assertSee('Rekomendasi AI (3 Rekomendasi Tindak Lanjut', false);
    }

    /**
     * Requirement:
     * Menampilkan notifikasi dan badge apakah dibuat oleh Gemini, OpenAI, atau diambil dari database / fallback.
     */
    public function test_notif_dan_badge_sumber_analisis_dimuat_dari_database(): void
    {
        $siswa = Siswa::first();
        $response = $this->get("/personalisasi/{$siswa->id}");

        $response->assertStatus(200);
        $response->assertSee('Status Sumber Analisis:', false);
        $response->assertSee('Dimuat dari Database', false);
    }

    public function test_notif_dan_sumber_analisis_fallback_heuristik(): void
    {
        $siswa = Siswa::first();
        $response = $this->post("/personalisasi/{$siswa->id}/ai-analyze");

        $response->assertRedirect("/personalisasi/{$siswa->id}");
        $response->assertSessionHas('success');
        $sessionMsg = session('success');
        $this->assertStringContainsString('Fallback Heuristik', $sessionMsg);

        $followResponse = $this->get("/personalisasi/{$siswa->id}");
        $followResponse->assertSee('Fallback Heuristik', false);
    }

    public function test_notif_dan_sumber_analisis_gemini(): void
    {
        $siswa = Siswa::first();
        \App\Models\Setting::set('ai_provider', 'gemini');

        \Illuminate\Support\Facades\Http::fake([
            'https://generativelanguage.googleapis.com/*' => \Illuminate\Support\Facades\Http::response([
                'candidates' => [
                    [
                        'content' => [
                            'parts' => [
                                [
                                    'text' => json_encode([
                                        'skor_performa' => 85.0,
                                        'kategori_pemahaman' => 'Tinggi',
                                        'gaya_belajar_rekomendasi' => 'Visual',
                                        'distribusi_gaya_belajar' => [
                                            'Visual' => 45,
                                            'Auditori' => 20,
                                            'Kinestetik' => 20,
                                            'Baca dan Tulis' => 15,
                                        ],
                                        'rekomendasi_materi' => 'Modul biologi lanjut terintegrasi diagram alur.',
                                        'rekomendasi_aktivitas' => 'Mini riset pengayaan mandiri.',
                                        'catatan_guru' => 'Siswa menuntaskan seluruh TP dengan pemahaman konsep yang baik.',
                                    ])
                                ]
                            ]
                        ]
                    ]
                ]
            ], 200)
        ]);

        $response = $this->post("/personalisasi/{$siswa->id}/ai-analyze");

        $response->assertRedirect("/personalisasi/{$siswa->id}");
        $response->assertSessionHas('success');
        $sessionMsg = session('success');
        $this->assertStringContainsString('Google Gemini', $sessionMsg);

        $followResponse = $this->get("/personalisasi/{$siswa->id}");
        $followResponse->assertSee('Google Gemini', false);
    }

    public function test_notif_dan_sumber_analisis_openai(): void
    {
        $siswa = Siswa::first();
        \App\Models\Setting::set('ai_provider', 'openai');

        \Illuminate\Support\Facades\Http::fake([
            'https://api.openai.com/*' => \Illuminate\Support\Facades\Http::response([
                'choices' => [
                    [
                        'message' => [
                            'content' => json_encode([
                                'skor_performa' => 78.0,
                                'kategori_pemahaman' => 'Sedang',
                                'gaya_belajar_rekomendasi' => 'Auditori',
                                'distribusi_gaya_belajar' => [
                                    'Visual' => 20,
                                    'Auditori' => 48,
                                    'Kinestetik' => 16,
                                    'Baca dan Tulis' => 16,
                                ],
                                'rekomendasi_materi' => 'Audio penjelasan materi TP.',
                                'rekomendasi_aktivitas' => 'Diskusi kelompok terarah.',
                                'catatan_guru' => 'Capaian akademik cukup, perlu penguatan pada TP 1.2.',
                            ])
                        ]
                    ]
                ]
            ], 200)
        ]);

        $response = $this->post("/personalisasi/{$siswa->id}/ai-analyze");

        $response->assertRedirect("/personalisasi/{$siswa->id}");
        $response->assertSessionHas('success');
        $sessionMsg = session('success');
        $this->assertStringContainsString('OpenAI', $sessionMsg);

        $followResponse = $this->get("/personalisasi/{$siswa->id}");
        $followResponse->assertSee('OpenAI', false);
    }
}

