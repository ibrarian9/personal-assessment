<?php

namespace App\Services;

use App\Models\Siswa;
use App\Models\Personalisasi;
use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiPersonalizationService
{
    protected string $lastSource = 'Fallback Heuristik';
    protected ?string $lastReason = null;
    protected ?string $lastModel = null;

    public function getLastSource(): string
    {
        return $this->lastSource;
    }

    public function getLastReason(): ?string
    {
        return $this->lastReason;
    }

    public function getLastModel(): ?string
    {
        return $this->lastModel;
    }

    /**
     * Generate analisis personalisasi berbasis AI untuk seorang siswa SMA dan simpan ke database
     */
    public function generateForSiswa(Siswa $siswa): Personalisasi
    {
        $siswa->loadMissing('nilaiSumatifTps.tujuanPembelajaran.lingkupMateri.mataPelajaran');
        $nilaiTps = $siswa->nilaiSumatifTps;

        // Pada mode testing tanpa fake HTTP, gunakan fallback deterministik tanpa panggilan HTTP eksternal
        if (app()->environment('testing') && !$this->isHttpFaking()) {
            $this->lastSource = 'Fallback Heuristik';
            $this->lastReason = 'Pengujian otomatis';
            return $this->fallbackGenerate($siswa, $nilaiTps, $this->lastReason);
        }

        // Ambil konfigurasi provider dan API key dari Settings DB atau config services
        $provider = Setting::get('ai_provider', config('services.ai.provider', 'gemini'));
        $geminiKey = Setting::get('gemini_api_key', config('services.gemini.api_key') ?: ($this->isHttpFaking() ? 'fake-test-key' : null));
        $openaiKey = Setting::get('openai_api_key', config('services.openai.api_key') ?: ($this->isHttpFaking() ? 'fake-test-key' : null));

        $aiResult = null;
        $attemptedProvider = null;

        if ($provider === 'openai') {
            $attemptedProvider = 'OpenAI';
            if (!empty($openaiKey)) {
                $aiResult = $this->callOpenAi($siswa, $nilaiTps, $openaiKey);
            } else {
                $this->lastReason = 'Kunci API OpenAI belum diisi di Pengaturan Sistem';
            }

            // Jika panggilan OpenAI tidak berhasil / tidak di-mock tetapi ada Gemini, coba Gemini
            if (!$aiResult && !empty($geminiKey)) {
                $geminiResult = $this->callGemini($siswa, $nilaiTps, $geminiKey);
                if ($geminiResult) {
                    $aiResult = $geminiResult;
                    $attemptedProvider = 'Gemini';
                }
            }
        } elseif ($provider === 'fallback') {
            $this->lastSource = 'Fallback Heuristik';
            $this->lastReason = 'Mode Heuristik Offline dipilih pada Pengaturan Sistem';
            return $this->fallbackGenerate($siswa, $nilaiTps, $this->lastReason);
        } else {
            // Default Gemini
            $attemptedProvider = 'Gemini';
            if (!empty($geminiKey)) {
                $aiResult = $this->callGemini($siswa, $nilaiTps, $geminiKey);
            } else {
                $this->lastReason = 'Kunci API Gemini belum diisi di Pengaturan Sistem';
            }

            // Jika panggilan Gemini tidak berhasil tetapi ada kunci OpenAI, failover otomatis ke OpenAI
            if (!$aiResult && !empty($openaiKey)) {
                Log::info('Panggilan Gemini tidak berhasil, beralih otomatis ke OpenAI...');
                $aiResult = $this->callOpenAi($siswa, $nilaiTps, $openaiKey);
                if ($aiResult) {
                    $attemptedProvider = 'OpenAI';
                }
            }
        }

        // Jika panggilan AI berhasil dan menghasilkan struktur data yang valid
        if ($aiResult && !empty($aiResult['kategori_pemahaman'])) {
            $this->lastSource = $attemptedProvider ?? 'AI Cloud';
            $model = $attemptedProvider === 'OpenAI'
                ? Setting::get('openai_model', config('services.openai.model', 'gpt-4o-mini'))
                : Setting::get('gemini_model', config('services.gemini.model', 'gemini-3.5-flash'));
            $this->lastModel = $model;
            $this->lastReason = "Dianalisis langsung oleh {$attemptedProvider} API (Model: {$model})";
            $aiResult['sumber_analisis'] = $this->lastSource;
            $aiResult['keterangan_sumber'] = $this->lastReason;
            return $this->savePersonalisasi($siswa, $nilaiTps, $aiResult);
        }

        if ($attemptedProvider && empty($this->lastReason)) {
            $this->lastReason = "Panggilan API {$attemptedProvider} mengalami kendala atau kuota habis, dialihkan ke Fallback Heuristik";
        }

        $this->lastSource = 'Fallback Heuristik';
        return $this->fallbackGenerate($siswa, $nilaiTps, $this->lastReason);
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
            $model = Setting::get('gemini_model', config('services.gemini.model', 'gemini-3.5-flash'));
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
                            'content' => 'Anda adalah asisten pakar pedagogi Kurikulum Merdeka SMA (Personalized Learning Assessment - PLA) yang menganalisis ketuntasan Tujuan Pembelajaran (TP) per Lingkup Materi dan menyusun rekomendasi remedial dan pengayaan adaptif berbasis gaya belajar VARK dalam format JSON. Buat catatan_guru secara sangat personal, unik, dan mendalam berdasarkan nama siswa, capaian butir TP spesifik, dan gaya belajar anak, tanpa format template kaku.'
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
        $gayaClean = str_replace(['Baca/Tulis', 'baca/tulis'], ['Baca dan Tulis', 'baca dan tulis'], $siswa->gaya_belajar ?? 'Visual');

        return <<<PROMPT
            Analisis data capaian sumatif per Tujuan Pembelajaran (TP) siswa SMA berikut untuk menyusun rekomendasi pembelajaran adaptif (Personalized Learning Assessment - PLA Kurikulum Merdeka):

            === PROFIL SISWA SMA ===
            Nama Siswa: {$siswa->nama}
            NISN: {$siswa->nisn}
            Kelas: {$siswa->kelas}
            Gaya Belajar Dominan (VARK): {$gayaClean}
            Rata-Rata Nilai Sumatif: {$avgScore}

            === DAFTAR TP BELUM TUNTAS (PERLU REMEDIAL) ===
            {$belumTuntasText}

            === DAFTAR TP TUNTAS (KEKUATAN SISWA) ===
            {$tuntasText}

            === ATURAN KHUSUS ULASAN EVALUASI DAN REKOMENDASI GURU (catatan_guru) ===
            Tulis narasi ulasan yang SANGAT PERSONAL, MENDALAM, DAN ALAMI langsung oleh AI untuk siswa bernama {$siswa->nama}:
            1. JANGAN gunakan kalimat template umum, kalimat hafalan, atau potongan boilerplate yang kaku. Tulislah selayaknya catatan evaluasi otentik seorang guru pakar yang mengamati langsung proses belajar {$siswa->nama}.
            2. Sebutkan nama {$siswa->nama} secara langsung dalam narasi, dan rujuk butir-butir TP atau materi konkret yang menjadi kekuatan maupun kelemahan belajarnya berdasarkan data capaian di atas.
            3. KETENTUAN SIKAP DAN NADA BAHASA:
               - JIKA NILAI SISWA TIDAK BAGUS (Kategori Sedang atau Perlu Pendampingan, atau ada TP belum tuntas):
                 JANGAN BANYAK DIPUJI. Hindari sanjungan kosong seperti 'luar biasa', 'sangat memuaskan', atau 'sangat baik'. Sampaikan secara objektif, jujur, dan transparan materi atau konsep apa yang belum dikuasai (sebutkan kode dan topik TP-nya), lalu berikan MASUKAN YANG MEMBANGUN (KONSTRUKTIF) mengenai langkah nyata perbaikan belajar yang harus dilakukan siswa bersama guru mata pelajaran.
               - JIKA NILAI SISWA TINGGI (Kategori Tinggi, semua TP tuntas):
                 Berikan apresiasi wajar berbasis bukti atas ketuntasan materi tersebut, serta rekomendasikan pengayaan mandiri dan peran sebagai tutor sebaya.
            4. Hubungkan masukan dengan modalitas belajar dominan ({$gayaClean}) agar saran tindak lanjutnya kontekstual dan aplikatif bagi guru pengampu.
            5. Awali catatan dengan: "Rekomendasi Guru (AI): " diikuti oleh narasi personal 1 sampai 2 paragraf tersebut.

            === ATURAN REKOMENDASI PER BUTIR TP (rekomendasi_per_tp) ===
            Untuk SETIAP butir TP pada daftar di atas, buat 3 poin rekomendasi tindak lanjut yang konkret:
            - Poin 1: Intervensi konseptual sesuai gaya belajar {$gayaClean} siswa SMA.
            - Poin 2: Aktivitas bimbingan dan latihan terarah (tutor sebaya atau latihan bertingkat).
            - Poin 3: Evaluasi formatif perbaikan (untuk remedial) atau portofolio/mini proyek (untuk pengayaan).

            === INSTRUKSI KELUARAN (WAJIB JSON VALID) ===
            Hasilkan output HANYA berupa JSON valid dengan skema berikut:
            {
            "skor_performa": {$avgScore},
            "kategori_pemahaman": "Tinggi" (jika rata-rata >= 80) ATAU "Sedang" (65-79.99) ATAU "Perlu Pendampingan" (< 65 atau banyak TP belum tuntas),
            "gaya_belajar_rekomendasi": "{$gayaClean}",
            "distribusi_gaya_belajar": {
                "Visual": 45,
                "Auditori": 25,
                "Kinestetik": 18,
                "Baca dan Tulis": 12
            },
            "rekomendasi_materi": "Rangkuman rekomendasi materi bahan ajar terarah per butir TP belum tuntas, disesuaikan gaya belajar {$gayaClean} siswa SMA.",
            "rekomendasi_aktivitas": "• Strategi aktivitas pembelajaran adaptif 1\\n• Strategi aktivitas pembelajaran adaptif 2\\n• Proyek pengayaan atau remedial 3",
            "catatan_guru": "Rekomendasi Guru (AI): [Tulis 1-2 paragraf narasi ulasan yang sangat personal untuk {$siswa->nama}, menyebutkan materi TP spesifik yang dikuasai maupun yang belum tuntas, dengan nada objektif, konstruktif, dan sesuai gaya belajar {$gayaClean}]",
            "rekomendasi_per_tp": {
                "kode_tp_contoh": [
                "1. Intervensi konsep sesuai modalitas...",
                "2. Bimbingan dan latihan terarah...",
                "3. Asesmen perbaikan atau pengayaan..."
                ]
            }
            }
            PENTING: Jangan gunakan karakter tanda garis miring (/) atau tanda em dash dalam narasi teks hasil analisis AI. Gunakan kata penghubung 'dan' atau 'atau'.
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
     * Hitung persentase distribusi modalitas belajar VARK (total tepat 100%)
     */
    public static function hitungDistribusiVark(string $gayaDominan, int $seed = 1): array
    {
        $clean = str_replace(['Baca/Tulis', 'baca/tulis'], ['Baca dan Tulis', 'baca dan tulis'], $gayaDominan);
        if (!in_array($clean, ['Visual', 'Auditori', 'Kinestetik', 'Baca dan Tulis'])) {
            $clean = 'Visual';
        }

        $offset1 = ($seed * 3) % 7;
        $offset2 = ($seed * 5) % 5;

        $pDominan = 44 + $offset1; // 44% sampai 50%
        $pSecond = 24 + $offset2;  // 24% sampai 28%
        $pThird = 16 - ($offset1 % 3); // 14% sampai 16%
        $pFourth = 100 - ($pDominan + $pSecond + $pThird); // sisa, tepat 100%

        $distribusi = [
            'Visual' => 15,
            'Auditori' => 15,
            'Kinestetik' => 15,
            'Baca dan Tulis' => 15,
        ];

        $distribusi[$clean] = $pDominan;

        $order = match ($clean) {
            'Visual' => ['Auditori', 'Kinestetik', 'Baca dan Tulis'],
            'Auditori' => ['Visual', 'Baca dan Tulis', 'Kinestetik'],
            'Kinestetik' => ['Visual', 'Auditori', 'Baca dan Tulis'],
            'Baca dan Tulis' => ['Visual', 'Auditori', 'Kinestetik'],
        };

        $distribusi[$order[0]] = $pSecond;
        $distribusi[$order[1]] = $pThird;
        $distribusi[$order[2]] = $pFourth;

        return $distribusi;
    }

    /**
     * Generate 3 butir rekomendasi spesifik AI per TP berbasis status ketuntasan & gaya belajar VARK
     */
    public function generateTpRecommendation($nilaiTp, string $gayaBelajar = 'Visual'): string
    {
        $tp = $nilaiTp->tujuanPembelajaran;
        $status = $nilaiTp->status_ketuntasan;
        $kodeTp = $tp->kode_tp ?? 'TP';
        $gaya = str_replace(['Baca/Tulis', 'baca/tulis'], ['Baca dan Tulis', 'baca dan tulis'], $gayaBelajar);

        if ($status === 'Belum Tuntas') {
            $intervensi = match ($gaya) {
                'Auditori' => "Penguatan Pemahaman: Berikan penjelasan lisan ringkas dan sesi tanya jawab terfokus mengenai konsep butir {$kodeTp}.",
                'Kinestetik' => "Simulasi dan Peraga: Gunakan alat peraga konkret atau simulasi aktivitas terpandu butir {$kodeTp} untuk memperkuat pemahaman.",
                'Baca dan Tulis' => "Telaah Modul dan Glosarium: Sediakan modul bacaan terstruktur dan glosarium istilah butir {$kodeTp} untuk dirangkum secara mandiri.",
                default => "Visualisasi Konsep: Gunakan diagram alur, skema visual, atau bagan konsep butir {$kodeTp} untuk memvisualisasikan hubungan konsep.",
            };

            return implode("\n", [
                "1. {$intervensi}",
                "2. Latihan Terarah: Kerjakan lembar kerja latihan soal bertingkat dengan bimbingan berkala dari guru atau tutor sebaya.",
                "3. Asesmen Perbaikan: Lakukan evaluasi formatif ulang khusus indikator butir {$kodeTp} untuk memastikan ketuntasan sebelum materi berikutnya.",
            ]);
        } else {
            $pengayaan = match ($gaya) {
                'Auditori' => "Presentasi dan Diskusi: Paparkan pemahaman konsep butir {$kodeTp} dalam sesi diskusi kelompok belajar atau bedah topik kelas.",
                'Kinestetik' => "Eksplorasi Praktis: Rancang mini eksperimen atau proyek terapan mandiri yang memanfaatkan prinsip butir {$kodeTp}.",
                'Baca dan Tulis' => "Kajian Pustaka Lanjutan: Kaji artikel ilmiah populer atau studi pustaka mendalam mengenai topik butir {$kodeTp}.",
                default => "Pemetaan Konsep Lanjutan: Susun peta konsep terpadu yang memetakan keterkaitan konsep butir {$kodeTp} dengan materi selanjutnya.",
            };

            return implode("\n", [
                "1. {$pengayaan}",
                "2. Kolaborasi Sebaya: Diberdayakan sebagai tutor sebaya bagi rekan yang memerlukan pendampingan pada butir {$kodeTp}.",
                "3. Penguatan Portofolio: Dokumentasikan hasil tugas atau telaah pengayaan butir {$kodeTp} ke dalam portofolio belajar mandiri.",
            ]);
        }
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

        $gayaBelajar = $data['gaya_belajar_rekomendasi'] ?? $siswa->gaya_belajar ?? 'Visual';

        // Simpan rekomendasi AI (3 rekomendasi) per butir TP ke kolom catatan di nilai_sumatif_tps
        $rekomendasiPerTp = $data['rekomendasi_per_tp'] ?? [];
        foreach ($nilaiTps as $item) {
            $kode = $item->tujuanPembelajaran->kode_tp ?? '';
            $rek = null;
            if (is_array($rekomendasiPerTp)) {
                if (isset($rekomendasiPerTp[$kode])) {
                    $rek = $rekomendasiPerTp[$kode];
                } else {
                    foreach ($rekomendasiPerTp as $entry) {
                        if (is_array($entry) && isset($entry['kode_tp']) && $entry['kode_tp'] === $kode) {
                            $rek = $entry['rekomendasi'] ?? null;
                            break;
                        }
                    }
                }
            }

            if (is_array($rek)) {
                $rek = implode("\n", array_filter($rek));
            }

            if (!$rek) {
                $rek = $this->generateTpRecommendation($item, $gayaBelajar);
            }

            $cleanRek = str_replace(['Baca/Tulis', 'baca/tulis'], ['Baca dan Tulis', 'baca dan tulis'], $rek);
            $cleanRek = str_replace(['/TP', 'per/TP', 'per-TP'], [' TP', 'per TP', 'per TP'], $cleanRek);
            $cleanRek = str_replace([' / ', '/'], [' atau ', ' atau '], $cleanRek);
            $cleanRek = str_replace(['—', '–'], [':', '-'], $cleanRek);
            $item->update(['catatan' => $cleanRek]);
        }

        $catatanGuru = $data['catatan_guru'] ?? 'Rekomendasi Guru (AI): Analisis capaian kompetensi TP dihasilkan secara otomatis berdasarkan modalitas belajar siswa SMA.';
        if (!str_starts_with($catatanGuru, 'Rekomendasi Guru') && !str_starts_with($catatanGuru, 'Catatan')) {
            $catatanGuru = 'Rekomendasi Guru (AI): ' . $catatanGuru;
        }

        $cleanText = function (?string $text): string {
            if (!$text) return '';
            $text = str_replace(['Baca/Tulis', 'baca/tulis'], ['Baca dan Tulis', 'baca dan tulis'], $text);
            $text = str_replace(['/TP', 'per/TP', 'per-TP'], [' TP', 'per TP', 'per TP'], $text);
            $text = str_replace([' / ', '/'], [' atau ', ' atau '], $text);
            $text = str_replace(['—', '–'], [':', '-'], $text);
            return $text;
        };

        // Parse distribusi gaya belajar
        $distribusi = null;
        if (!empty($data['distribusi_gaya_belajar']) && is_array($data['distribusi_gaya_belajar'])) {
            $distribusi = $data['distribusi_gaya_belajar'];
        } else {
            $distribusi = self::hitungDistribusiVark($gayaBelajar, $siswa->id);
        }

        return Personalisasi::updateOrCreate(
            ['siswa_id' => $siswa->id],
            [
                'skor_performa' => (float) ($data['skor_performa'] ?? $siswa->rata_rata_nilai),
                'kategori_pemahaman' => in_array($data['kategori_pemahaman'] ?? '', ['Tinggi', 'Sedang', 'Perlu Pendampingan']) ? $data['kategori_pemahaman'] : 'Sedang',
                'sumber_analisis' => $data['sumber_analisis'] ?? $this->lastSource,
                'keterangan_sumber' => $data['keterangan_sumber'] ?? $this->lastReason,
                'gaya_belajar_rekomendasi' => $cleanText($gayaBelajar),
                'distribusi_gaya_belajar' => $distribusi,
                'tp_tuntas_count' => $tpTuntas,
                'tp_belum_tuntas_count' => $tpBelumTuntas,
                'daftar_tp_remedial' => json_encode($belumTuntasCodes),
                'rekomendasi_materi' => $cleanText($data['rekomendasi_materi'] ?? 'Modul penguatan konsep TP standar.'),
                'rekomendasi_aktivitas' => $cleanText($data['rekomendasi_aktivitas'] ?? '• Jaga konsistensi pengerjaan tugas dan latihan sumatif TP.'),
                'catatan_guru' => $cleanText($catatanGuru),
            ]
        );
    }

    /**
     * Fallback Heuristik Cerdas Berbasis Kurikulum Merdeka SMA
     */
    public function fallbackGenerate(Siswa $siswa, $nilaiTps, ?string $alasan = null): Personalisasi
    {
        $rataRata = $nilaiTps->count() > 0 ? (float) $nilaiTps->avg('nilai') : 0;
        $tpTuntas = $nilaiTps->where('status_ketuntasan', 'Tuntas')->count();
        $tpBelumTuntas = $nilaiTps->where('status_ketuntasan', 'Belum Tuntas')->count();

        $tuntasItems = $nilaiTps->where('status_ketuntasan', 'Tuntas');
        $belumTuntasItems = $nilaiTps->where('status_ketuntasan', 'Belum Tuntas');

        $tuntasSummary = $tuntasItems->take(2)->map(fn($n) => ($n->tujuanPembelajaran->kode_tp ?? 'TP') . ' (' . ($n->tujuanPembelajaran->lingkupMateri->judul_materi ?? 'Materi') . ')')->implode(', ');
        $belumTuntasSummary = $belumTuntasItems->take(2)->map(fn($n) => ($n->tujuanPembelajaran->kode_tp ?? 'TP') . ' (' . ($n->tujuanPembelajaran->lingkupMateri->judul_materi ?? 'Materi') . ', nilai ' . $n->nilai . ')')->implode(', ');
        $gayaBelajar = $siswa->gaya_belajar ?? 'Visual';

        if ($nilaiTps->count() === 0) {
            $kategori = 'Sedang';
            $catatanGuru = "Rekomendasi Guru (AI): {$siswa->nama} adalah Siswa baru terdaftar dan belum memiliki catatan penilaian sumatif TP. Silakan lakukan asesmen awal diagnostik atau input nilai pada lembar matriks TP.";
            $rekomendasiAktivitas = "• Lakukan asesmen awal diagnostik kompetensi dasar.\n• Input nilai sumatif per Tujuan Pembelajaran (TP) secara berkala.";
        } elseif ($rataRata >= 80 && $tpBelumTuntas === 0) {
            $kategori = 'Tinggi';
            $tuntasRef = $tuntasSummary ? "terutama pada {$tuntasSummary}" : "di seluruh indikator";
            $catatanGuru = "Rekomendasi Guru (AI): {$siswa->nama} menunjukkan capaian belajar yang solid dengan menuntaskan seluruh {$tpTuntas} Tujuan Pembelajaran (Rata-rata: {$rataRata}), {$tuntasRef}. Disarankan untuk melanjutkan ke tantangan pengayaan mandiri dan diberdayakan sebagai tutor sebaya dengan memanfaatkan keunggulan modalitas belajar {$gayaBelajar}.";
        } elseif ($rataRata >= 70 && $tpBelumTuntas <= 2) {
            $kategori = 'Sedang';
            $belumRef = $belumTuntasSummary ? "khususnya pada {$belumTuntasSummary}" : "pada materi prasyarat terkait";
            $catatanGuru = "Rekomendasi Guru (AI): Capaian akademik {$siswa->nama} berada pada kategori Sedang (Rata-rata: {$rataRata}). Berdasarkan evaluasi capaian, {$siswa->nama} telah menguasai sejumlah kompetensi namun masih menghadapi kendala ketuntasan {$belumRef}. Disarankan untuk tidak memberikan pujian berlebihan, melainkan fokus memberikan bimbingan konstruktif dan latihan terarah berbasis modalitas {$gayaBelajar} sebelum mengikuti asesmen ulang.";
        } else {
            $kategori = 'Perlu Pendampingan';
            $belumRef = $belumTuntasSummary ? "terutama pada {$belumTuntasSummary}" : "pada konsep-konsep esensial";
            $catatanGuru = "Rekomendasi Guru (AI): Capaian akademik {$siswa->nama} memerlukan perhatian dan pendampingan intensif (Rata-rata: {$rataRata}). Terdapat {$tpBelumTuntas} Tujuan Pembelajaran yang belum tuntas, {$belumRef}. {$siswa->nama} mengalami kendala mendasar dalam pemahaman konsep inti. Disarankan untuk segera menerapkan bimbingan tatap muka bertahap, remedial terstruktur dengan media belajar {$gayaBelajar}, serta penataan jadwal belajar mandiri agar ketertinggalan materi dapat segera diperbaiki.";
        }

        // Update rekomendasi per TP (3 butir per TP) untuk seluruh nilai TP
        foreach ($nilaiTps as $item) {
            $item->update(['catatan' => $this->generateTpRecommendation($item, $gayaBelajar)]);
        }

        $materiMap = [
            'Visual' => [
                'Tinggi' => 'Modul infografis tingkat lanjut per TP, peta konsep biologi dan sains terintegrasi, dan video animasi simulasi mikroskopik.',
                'Sedang' => 'Diagram alur organ dan sistem tubuh terstruktur, slide animasi materi per TP, dan poster rangkuman rumus dan definisi.',
                'Perlu Pendampingan' => 'Video penjelasan beranimasi langkah demi langkah fokus pada TP belum tuntas, lembar kerja visual dengan gambar organ dan skema jelas.'
            ],
            'Auditori' => [
                'Tinggi' => 'Podcast bedah konsep sains terkini, sesi presentasi studi kasus kelompok, dan diskusi telaah TP pengayaan.',
                'Sedang' => 'Audio penjelasan materi ringkas per TP, diskusi kelompok terarah, dan re-listening materi guru.',
                'Perlu Pendampingan' => 'Bimbingan lisan tatap muka dengan tutor sebaya atau guru, rekaman penjelasan audio per TP yang belum tuntas.'
            ],
            'Kinestetik' => [
                'Tinggi' => 'Proyek praktikum mandiri laboratorium, pembuatan model 3D sistem organ dan mekanika, dan tantangan eksperimen ilmiah.',
                'Sedang' => 'Praktikum terbimbing di laboratorium, latihan pengukuran langsung (misal denyut nadi atau gerak), dan lembar kerja eksperimen.',
                'Perlu Pendampingan' => 'Praktikum pengulangan dasar dengan torso atau model fisik, eksperimen sederhana terpandu untuk menguji ulang TP remedial.'
            ],
            'Baca/Tulis' => [
                'Tinggi' => 'Modul kompilasi literatur sains mendalam, jurnal ilmiah ringkas, dan penulisan artikel analisis fenomena alam.',
                'Sedang' => 'Rangkuman buku teks bab per TP, catatan rumus dan istilah kunci, dan lembar evaluasi mandiri.',
                'Perlu Pendampingan' => 'Glosarium istilah biologi dan sains dasar, modul rangkuman fokus butir TP belum tuntas, dan latihan soal bertingkat.'
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

        $distribusi = self::hitungDistribusiVark($gayaBelajar, $siswa->id);

        return Personalisasi::updateOrCreate(
            ['siswa_id' => $siswa->id],
            [
                'skor_performa' => round($rataRata, 2),
                'kategori_pemahaman' => $kategori,
                'sumber_analisis' => 'Fallback Heuristik',
                'keterangan_sumber' => $alasan ?? $this->lastReason ?? 'Aturan pedagogi Kurikulum Merdeka (tanpa API eksternal)',
                'gaya_belajar_rekomendasi' => $gayaBelajar,
                'distribusi_gaya_belajar' => $distribusi,
                'tp_tuntas_count' => $tpTuntas,
                'tp_belum_tuntas_count' => $tpBelumTuntas,
                'daftar_tp_remedial' => json_encode($belumTuntasCodes),
                'rekomendasi_materi' => $rekomendasiMateri,
                'rekomendasi_aktivitas' => $rekomendasiAktivitas,
                'catatan_guru' => $catatanGuru,
            ]
        );
    }

    /**
     * Memeriksa apakah Http::fake() sedang aktif di unit test.
     */
    private function isHttpFaking(): bool
    {
        try {
            $factory = Http::getFacadeRoot();
            $ref = new \ReflectionClass($factory);
            if ($ref->hasProperty('stubCallbacks')) {
                $prop = $ref->getProperty('stubCallbacks');
                $prop->setAccessible(true);
                $val = $prop->getValue($factory);
                return !empty($val) && (is_countable($val) ? count($val) > 0 : true);
            }
        } catch (\Throwable $e) {
        }
        return false;
    }
}
