<?php

namespace App\Services;

use App\Models\Siswa;
use App\Models\Personalisasi;
use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiPersonalizationService
{
    /**
     * Generate analisis personalisasi berbasis AI untuk seorang siswa SMA dan simpan ke database
     */
    public function generateForSiswa(Siswa $siswa): Personalisasi
    {
        $siswa->loadMissing('nilaiSumatifTps.tujuanPembelajaran.lingkupMateri.mataPelajaran');
        $nilaiTps = $siswa->nilaiSumatifTps;

        // Ambil konfigurasi provider dan API key dari Settings DB atau config services
        $provider = Setting::get('ai_provider', config('services.ai.provider', 'gemini'));
        $geminiKey = Setting::get('gemini_api_key', config('services.gemini.api_key'));
        $openaiKey = Setting::get('openai_api_key', config('services.openai.api_key'));

        $aiResult = null;

        if ($provider === 'openai' && !empty($openaiKey)) {
            $aiResult = $this->callOpenAi($siswa, $nilaiTps, $openaiKey);
        } elseif (!empty($geminiKey)) {
            $aiResult = $this->callGemini($siswa, $nilaiTps, $geminiKey);
        }

        // Jika panggilan AI berhasil dan menghasilkan struktur data yang valid
        if ($aiResult && !empty($aiResult['kategori_pemahaman'])) {
            return $this->savePersonalisasi($siswa, $nilaiTps, $aiResult);
        }

        // Fallback cerdas berbasis heuristik diagnostik Kurikulum Merdeka SMA
        return $this->fallbackGenerate($siswa, $nilaiTps);
    }

    /**
     * Generate analisis AI untuk seluruh siswa
     */
    public function generateAll(): int
    {
        $siswas = Siswa::with('nilaiSumatifTps.tujuanPembelajaran.lingkupMateri.mataPelajaran')->get();
        $count = 0;

        foreach ($siswas as $siswa) {
            $this->generateForSiswa($siswa);
            $count++;
        }

        return $count;
    }

    /**
     * Panggilan ke Google Gemini API
     */
    private function callGemini(Siswa $siswa, $nilaiTps, string $apiKey): ?array
    {
        try {
            $model = Setting::get('gemini_model', config('services.gemini.model', 'gemini-1.5-flash'));
            $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";

            $prompt = $this->buildPrompt($siswa, $nilaiTps);

            $response = Http::timeout(25)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post($url, [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => $prompt]
                            ]
                        ]
                    ],
                    'generationConfig' => [
                        'responseMimeType' => 'application/json',
                        'temperature' => 0.4,
                    ]
                ]);

            if ($response->successful()) {
                $data = $response->json();
                $text = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;
                if ($text) {
                    return $this->parseJson($text);
                }
            } else {
                Log::warning('Gemini AI call returned non-200: ' . $response->body());
            }
        } catch (\Throwable $e) {
            Log::error('Error calling Gemini AI: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Panggilan ke OpenAI API
     */
    private function callOpenAi(Siswa $siswa, $nilaiTps, string $apiKey): ?array
    {
        try {
            $model = Setting::get('openai_model', config('services.openai.model', 'gpt-4o-mini'));
            $url = 'https://api.openai.com/v1/chat/completions';

            $prompt = $this->buildPrompt($siswa, $nilaiTps);

            $response = Http::timeout(25)
                ->withToken($apiKey)
                ->post($url, [
                    'model' => $model,
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => 'Anda adalah asisten pakar pedagogi Kurikulum Merdeka SMA (Personalized Learning Assessment - PLA) yang menganalisis ketuntasan Tujuan Pembelajaran (TP) per Lingkup Materi dan menyusun rekomendasi remedial & pengayaan adaptif berbasis gaya belajar VARK dalam format JSON.'
                        ],
                        [
                            'role' => 'user',
                            'content' => $prompt
                        ]
                    ],
                    'response_format' => ['type' => 'json_object'],
                    'temperature' => 0.4,
                ]);

            if ($response->successful()) {
                $data = $response->json();
                $content = $data['choices'][0]['message']['content'] ?? null;
                if ($content) {
                    return $this->parseJson($content);
                }
            } else {
                Log::warning('OpenAI API call returned non-200: ' . $response->body());
            }
        } catch (\Throwable $e) {
            Log::error('Error calling OpenAI API: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Bangun prompt Kurikulum Merdeka SMA untuk LLM
     */
    public function buildPrompt(Siswa $siswa, $nilaiTps): string
    {
        $tuntasList = [];
        $belumTuntasList = [];

        foreach ($nilaiTps as $item) {
            $tp = $item->tujuanPembelajaran;
            $lm = $tp->lingkupMateri ?? null;
            $lmTitle = $lm ? "{$lm->kode_lm} ({$lm->judul_materi})" : 'Materi';
            $entry = "- [{$lmTitle}] {$tp->kode_tp}: {$tp->deskripsi_tp} => Nilai: {$item->nilai} ({$item->status_ketuntasan})";

            if ($item->status_ketuntasan === 'Tuntas') {
                $tuntasList[] = $entry;
            } else {
                $belumTuntasList[] = $entry;
            }
        }

        $tuntasText = count($tuntasList) > 0 ? implode("\n", $tuntasList) : "Belum ada TP tuntas.";
        $belumTuntasText = count($belumTuntasList) > 0 ? implode("\n", $belumTuntasList) : "Semua TP berhasil tuntas.";

        $avgScore = $nilaiTps->count() > 0 ? round($nilaiTps->avg('nilai'), 2) : 0;

        return <<<PROMPT
Analisis data capaian sumatif per Tujuan Pembelajaran (TP) siswa SMA berikut untuk menyusun rekomendasi pembelajaran adaptif (Personalized Learning Assessment - PLA Kurikulum Merdeka):

=== PROFIL SISWA SMA ===
Nama Siswa: {$siswa->nama}
NISN: {$siswa->nisn}
Kelas: {$siswa->kelas}
Gaya Belajar Dominan (VARK): {$siswa->gaya_belajar}
Rata-Rata Nilai Sumatif: {$avgScore}

=== DAFTAR TP BELUM TUNTAS (PERLU REMEDIAL) ===
{$belumTuntasText}

=== DAFTAR TP TUNTAS (KEKUATAN SISWA) ===
{$tuntasText}

=== INSTRUKSI KELUARAN (WAJIB JSON VALID) ===
Hasilkan output HANYA berupa JSON valid dengan skema berikut:
{
  "skor_performa": {$avgScore},
  "kategori_pemahaman": "Tinggi" (jika rata-rata >= 80) ATAU "Sedang" (65-79.99) ATAU "Perlu Pendampingan" (< 65 atau banyak TP belum tuntas),
  "gaya_belajar_rekomendasi": "{$siswa->gaya_belajar}",
  "rekomendasi_materi": "Rekomendasi materi remedial khusus untuk butir TP yang belum tuntas di atas, disajikan dalam bentuk bahan ajar yang sesuai gaya belajar {$siswa->gaya_belajar} siswa SMA.",
  "rekomendasi_aktivitas": "• Aktivitas bimbingan remedial per TP 1\\n• Latihan soal pemantapan konsep TP 2\\n• Proyek pengayaan untuk TP yang sudah tuntas 3",
  "catatan_guru": "Ulasan evaluasi diagnostik untuk guru mata pelajaran dan wali kelas mengenai perkembangan kompetensi siswa dan strategi pendampingan kelas."
}
PROMPT;
    }

    /**
     * Parse response JSON dari AI
     */
    private function parseJson(string $rawJson): ?array
    {
        $clean = preg_replace('/^```(?:json)?\s*/i', '', trim($rawJson));
        $clean = preg_replace('/\s*```$/', '', $clean);

        $decoded = json_decode($clean, true);
        if (is_array($decoded)) {
            return $decoded;
        }

        return null;
    }

    /**
     * Simpan hasil analisis ke database
     */
    private function savePersonalisasi(Siswa $siswa, $nilaiTps, array $data): Personalisasi
    {
        $tpTuntas = $nilaiTps->where('status_ketuntasan', 'Tuntas')->count();
        $tpBelumTuntas = $nilaiTps->where('status_ketuntasan', 'Belum Tuntas')->count();
        
        $belumTuntasCodes = $nilaiTps->where('status_ketuntasan', 'Belum Tuntas')
            ->map(fn($n) => ($n->tujuanPembelajaran->kode_tp ?? 'TP') . ': ' . ($n->tujuanPembelajaran->deskripsi_tp ?? ''))
            ->values()
            ->toArray();

        return Personalisasi::updateOrCreate(
            ['siswa_id' => $siswa->id],
            [
                'skor_performa' => (float) ($data['skor_performa'] ?? $siswa->rata_rata_nilai),
                'kategori_pemahaman' => in_array($data['kategori_pemahaman'] ?? '', ['Tinggi', 'Sedang', 'Perlu Pendampingan']) ? $data['kategori_pemahaman'] : 'Sedang',
                'gaya_belajar_rekomendasi' => $data['gaya_belajar_rekomendasi'] ?? $siswa->gaya_belajar,
                'tp_tuntas_count' => $tpTuntas,
                'tp_belum_tuntas_count' => $tpBelumTuntas,
                'daftar_tp_remedial' => json_encode($belumTuntasCodes),
                'rekomendasi_materi' => $data['rekomendasi_materi'] ?? 'Modul penguatan konsep TP standar.',
                'rekomendasi_aktivitas' => $data['rekomendasi_aktivitas'] ?? '• Jaga konsistensi pengerjaan tugas dan latihan sumatif TP.',
                'catatan_guru' => $data['catatan_guru'] ?? 'Analisis capaian kompetensi TP dihasilkan secara otomatis.',
            ]
        );
    }

    /**
     * Fallback Heuristik Cerdas Berbasis Kurikulum Merdeka SMA
     */
    public function fallbackGenerate(Siswa $siswa, $nilaiTps): Personalisasi
    {
        $rataRata = $nilaiTps->count() > 0 ? (float) $nilaiTps->avg('nilai') : 0;
        $tpTuntas = $nilaiTps->where('status_ketuntasan', 'Tuntas')->count();
        $tpBelumTuntas = $nilaiTps->where('status_ketuntasan', 'Belum Tuntas')->count();

        if ($nilaiTps->count() === 0) {
            $kategori = 'Sedang';
            $catatanGuru = 'Siswa baru terdaftar dan belum memiliki catatan penilaian sumatif TP. Silakan lakukan asesmen awal atau input nilai pada lembar matriks TP.';
            $rekomendasiAktivitas = "• Lakukan asesmen awal diagnostik kompetensi dasar.\n• Input nilai sumatif per Tujuan Pembelajaran (TP) secara berkala.";
        } elseif ($rataRata >= 80 && $tpBelumTuntas === 0) {
            $kategori = 'Tinggi';
            $catatanGuru = "Performa akademik sangat memuaskan (Rata-rata: {$rataRata}). Siswa telah menuntaskan {$tpTuntas} TP dengan penguasaan konsep yang sangat baik dan siap untuk pengayaan.";
        } elseif ($rataRata >= 70 && $tpBelumTuntas <= 2) {
            $kategori = 'Sedang';
            $catatanGuru = "Performa akademik baik (Rata-rata: {$rataRata}). Siswa telah menguasai sebagian besar TP ({$tpTuntas} Tuntas, {$tpBelumTuntas} Belum Tuntas). Diperlukan penguatan pada beberapa sub-materi.";
        } else {
            $kategori = 'Perlu Pendampingan';
            $catatanGuru = "Performa akademik memerlukan perhatian khusus (Rata-rata: {$rataRata}). Terdapat {$tpBelumTuntas} TP yang belum tuntas. Disarankan segera melaksanakan program remedial.";
        }

        $gayaBelajar = $siswa->gaya_belajar ?? 'Visual';

        $materiMap = [
            'Visual' => [
                'Tinggi' => 'Modul infografis tingkat lanjut per TP, peta konsep biologi & sains terintegrasi, dan video animasi simulasi mikroskopik.',
                'Sedang' => 'Diagram alur organ/sistem tubuh terstruktur, slide animasi materi per-TP, dan poster rangkuman rumus & definisi.',
                'Perlu Pendampingan' => 'Video penjelasan beranimasi langkah-demi-langkah fokus pada TP belum tuntas, lembar kerja visual dengan gambar organ/skema jelas.'
            ],
            'Auditori' => [
                'Tinggi' => 'Podcast bedah konsep sains terkini, sesi presentasi studi kasus kelompok, dan diskusi telaah TP pengayaan.',
                'Sedang' => 'Audio penjelasan materi ringkas per TP, diskusi kelompok terarah, dan re-listening materi guru.',
                'Perlu Pendampingan' => 'Bimbingan lisan one-on-one dengan tutor sebaya/guru, rekaman penjelasan audio per-TP yang belum tuntas.'
            ],
            'Kinestetik' => [
                'Tinggi' => 'Proyek praktikum mandiri laboratorium, pembuatan model 3D sistem organ/mekanika, dan tantangan eksperimen ilmiah.',
                'Sedang' => 'Praktikum terbimbing di laboratorium, latihan pengukuran langsung (misal: denyut nadi, gerak), dan lembar kerja eksperimen.',
                'Perlu Pendampingan' => 'Praktikum pengulangan dasar dengan torso/model fisik, eksperimen sederhana terpandu untuk menguji ulang TP remedial.'
            ],
            'Baca/Tulis' => [
                'Tinggi' => 'Modul kompilasi literatur sains mendalam, jurnal ilmiah ringkas, dan penulisan artikel analisis fenomena alam.',
                'Sedang' => 'Rangkuman buku teks bab per-TP, catatan rumus & istilah kunci, dan lembar evaluasi mandiri.',
                'Perlu Pendampingan' => 'Glosarium istilah biologi/sains dasar, modul rangkuman fokus butir TP belum tuntas, dan latihan soal bertingkat.'
            ]
        ];

        $rekomendasiMateri = $materiMap[$gayaBelajar][$kategori] ?? 'Modul pembelajaran terstruktur standar dan materi pendukung.';

        // Tips Aktivitas jika ada nilai
        if ($nilaiTps->count() > 0) {
            $tips = [];
            if ($tpBelumTuntas > 0) {
                $tips[] = "Fokus bimbingan remedial khusus pada {$tpBelumTuntas} Tujuan Pembelajaran (TP) yang belum mencapai kriteria ketuntasan minimal (75).";
                $tips[] = "Lakukan tes formatif ulang khusus pada indikator TP yang belum dikuasai setelah pendampingan.";
            }

            if ($kategori === 'Tinggi') {
                $tips[] = 'Diberdayakan sebagai Tutor Sebaya (Peer Tutor) pada sesi belajar kelompok kelas.';
                $tips[] = 'Diberikan lembar tantangan pengayaan (*Enrichment Task*) berbasis mini riset kontekstual.';
            } elseif ($kategori === 'Sedang') {
                $tips[] = 'Ikuti sesi review materi kelompok mingguan dan perbanyak latihan soal sumatif.';
                $tips[] = 'Konsultasikan kendala pada butir TP tertentu kepada guru mata pelajaran.';
            } else {
                $tips[] = 'Wajib mengikuti program remedial terstruktur dan pendampingan intensif bersama guru.';
                $tips[] = 'Perkuat pemahaman dasar konsep awal sebelum melangkah ke lingkup materi berikutnya.';
            }

            $rekomendasiAktivitas = implode("\n", array_map(fn($t) => '• ' . $t, $tips));
        }

        $belumTuntasCodes = $nilaiTps->where('status_ketuntasan', 'Belum Tuntas')
            ->map(fn($n) => ($n->tujuanPembelajaran->kode_tp ?? 'TP') . ': ' . ($n->tujuanPembelajaran->deskripsi_tp ?? ''))
            ->values()
            ->toArray();

        return Personalisasi::updateOrCreate(
            ['siswa_id' => $siswa->id],
            [
                'skor_performa' => round($rataRata, 2),
                'kategori_pemahaman' => $kategori,
                'gaya_belajar_rekomendasi' => $gayaBelajar,
                'tp_tuntas_count' => $tpTuntas,
                'tp_belum_tuntas_count' => $tpBelumTuntas,
                'daftar_tp_remedial' => json_encode($belumTuntasCodes),
                'rekomendasi_materi' => $rekomendasiMateri,
                'rekomendasi_aktivitas' => $rekomendasiAktivitas,
                'catatan_guru' => $catatanGuru,
            ]
        );
    }
}
