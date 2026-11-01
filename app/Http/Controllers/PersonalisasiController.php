<?php

namespace App\Http\Controllers;

use App\Models\Personalisasi;
use App\Models\Siswa;
use App\Services\AiPersonalizationService;
use App\Services\PersonalizationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PersonalisasiController extends Controller
{
    protected PersonalizationService $personalizationService;
    protected AiPersonalizationService $aiService;

    public function __construct(PersonalizationService $personalizationService, AiPersonalizationService $aiService)
    {
        $this->personalizationService = $personalizationService;
        $this->aiService = $aiService;
    }

    public function index(Request $request): View
    {
        $kategori = $request->query('kategori');
        $gayaBelajar = $request->query('gaya_belajar');
        $search = $request->query('search');

        $query = Personalisasi::with('siswa');

        if ($kategori) {
            $query->where('kategori_pemahaman', $kategori);
        }

        if ($gayaBelajar) {
            $query->where('gaya_belajar_rekomendasi', $gayaBelajar);
        }

        if ($search) {
            $query->whereHas('siswa', function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nisn', 'like', "%{$search}%");
            });
        }

        $perPageRaw = $request->query('per_page', '25');
        $perPage = ($perPageRaw === 'all' || (int)$perPageRaw <= 0) ? 1000 : (int)$perPageRaw;

        // Ambil daftar seluruh siswa (mencakup semua slide / halaman) yang sesuai dengan filter
        $allQueue = (clone $query)->orderBy('skor_performa', 'desc')->get(['id', 'siswa_id']);
        $allStudentsQueue = $allQueue->map(function ($p) {
            return [
                'siswa_id' => $p->siswa_id,
                'nama' => $p->siswa->nama ?? 'Siswa',
                'analyze_url' => route('personalisasi.ai-analyze', $p->siswa_id),
            ];
        })->values();

        $personalisasis = $query->orderBy('skor_performa', 'desc')->paginate($perPage)->withQueryString();

        // Summary statistics
        $stats = array_merge([
            'tinggi' => 0,
            'sedang' => 0,
            'pendampingan' => 0,
        ], Personalisasi::selectRaw('
            COUNT(CASE WHEN kategori_pemahaman = "Tinggi" THEN 1 END) as tinggi,
            COUNT(CASE WHEN kategori_pemahaman = "Sedang" THEN 1 END) as sedang,
            COUNT(CASE WHEN kategori_pemahaman = "Perlu Pendampingan" THEN 1 END) as pendampingan
        ')->first()?->toArray() ?? []);

        return view('personalisasi.index', compact(
            'personalisasis',
            'kategori',
            'gayaBelajar',
            'search',
            'stats',
            'allStudentsQueue',
            'perPageRaw'
        ));
    }

    public function show(Siswa $siswa): View
    {
        $siswa->load(['nilaiSumatifTps.tujuanPembelajaran.lingkupMateri.mataPelajaran', 'personalisasis']);
        $existingPersonalisasi = $siswa->personalisasis->first();
        $isFromDatabase = $existingPersonalisasi !== null;

        if (!$existingPersonalisasi) {
            $personalisasi = $this->aiService->generateForSiswa($siswa);
            $siswa->load(['nilaiSumatifTps.tujuanPembelajaran.lingkupMateri.mataPelajaran']);
            $sumberTampilan = 'baru_dibuat';
        } else {
            $personalisasi = $existingPersonalisasi;
            $sumberTampilan = 'database';

            // Pastikan setiap butir TP memiliki 3 catatan rekomendasi tindak lanjut AI
            $needsReload = false;
            foreach ($siswa->nilaiSumatifTps as $nst) {
                if (empty($nst->catatan) || !str_contains($nst->catatan, "\n")) {
                    $nst->update([
                        'catatan' => $this->aiService->generateTpRecommendation($nst, $siswa->gaya_belajar ?? 'Visual')
                    ]);
                    $needsReload = true;
                }
            }

            // Pastikan distribusi gaya belajar VARK tersimpan
            if (empty($personalisasi->getRawOriginal('distribusi_gaya_belajar'))) {
                $personalisasi->update([
                    'distribusi_gaya_belajar' => AiPersonalizationService::hitungDistribusiVark($siswa->gaya_belajar ?? 'Visual', $siswa->id)
                ]);
            }

            if ($needsReload) {
                $siswa->load(['nilaiSumatifTps.tujuanPembelajaran.lingkupMateri.mataPelajaran']);
            }
        }

        // Parse list of remedial TPs
        $remedialTps = [];
        if ($personalisasi && $personalisasi->daftar_tp_remedial) {
            $remedialTps = json_decode($personalisasi->daftar_tp_remedial, true) ?? [];
        }

        return view('personalisasi.show', compact('siswa', 'personalisasi', 'remedialTps', 'isFromDatabase', 'sumberTampilan'));
    }

    /**
     * Jalankan analisis AI khusus untuk seorang siswa SMA
     */
    public function aiAnalyze(Siswa $siswa): JsonResponse|RedirectResponse
    {
        $personalisasi = $this->aiService->generateForSiswa($siswa);
        $source = $this->aiService->getLastSource();
        $reason = $this->aiService->getLastReason();

        if ($source === 'Router9') {
            $model = $this->aiService->getLastModel() ?? 'ag/gemini-2.5-flash';
            $msg = "Analisis Diagnostik AI untuk {$siswa->nama} berhasil dibuat via Router9: {$model} dan disimpan ke database dengan Skor {$personalisasi->skor_performa}, Kategori {$personalisasi->kategori_pemahaman}.";
        } elseif ($source === 'Gemini') {
            $msg = "Analisis Diagnostik AI untuk {$siswa->nama} berhasil dibuat langsung oleh Google Gemini: gemini-3.5-flash dan disimpan ke database dengan Skor {$personalisasi->skor_performa}, Kategori {$personalisasi->kategori_pemahaman}.";
        } elseif ($source === 'OpenAI') {
            $msg = "Analisis Diagnostik AI untuk {$siswa->nama} berhasil dibuat langsung oleh OpenAI: gpt-4o-mini dan disimpan ke database dengan Skor {$personalisasi->skor_performa}, Kategori {$personalisasi->kategori_pemahaman}.";
        } else {
            $keterangan = $reason ? " - {$reason}" : '';
            $msg = "Analisis Diagnostik untuk {$siswa->nama} diproses menggunakan Fallback Heuristik Kurikulum Merdeka{$keterangan} dan disimpan ke database dengan Skor {$personalisasi->skor_performa}, Kategori {$personalisasi->kategori_pemahaman}.";
        }

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $msg,
                'redirect' => route('personalisasi.show', $siswa),
                'data' => [
                    'siswa_id' => $siswa->id,
                    'nama' => $siswa->nama,
                    'nisn' => $siswa->nisn ?? '-',
                    'kelas' => $siswa->kelas ?? '-',
                    'skor_performa' => (float) $personalisasi->skor_performa,
                    'kategori_pemahaman' => $personalisasi->kategori_pemahaman,
                    'tp_tuntas_count' => (int) $personalisasi->tp_tuntas_count,
                    'tp_belum_tuntas_count' => (int) $personalisasi->tp_belum_tuntas_count,
                    'gaya_belajar' => str_replace(['Baca/Tulis', '/'], ['Baca dan Tulis', ' dan '], $personalisasi->gaya_belajar_rekomendasi ?? $siswa->gaya_belajar ?? 'Visual'),
                    'rekomendasi_materi' => \Illuminate\Support\Str::limit(str_replace(['Baca/Tulis', '/TP', 'per/TP', 'per-TP', ' / ', '/'], ['Baca dan Tulis', ' TP', 'per TP', 'per TP', ' atau ', ' atau '], $personalisasi->rekomendasi_materi ?? ''), 90),
                    'rekomendasi_materi_full' => str_replace(['Baca/Tulis', '/'], ['Baca dan Tulis', ' atau '], $personalisasi->rekomendasi_materi ?? '-'),
                    'rekomendasi_aktivitas' => str_replace(['Baca/Tulis', '/'], ['Baca dan Tulis', ' atau '], $personalisasi->rekomendasi_aktivitas ?? '-'),
                    'catatan_guru' => str_replace(['Baca/Tulis', '/'], ['Baca dan Tulis', ' atau '], $personalisasi->catatan_guru ?? 'Belum ada catatan diagnostik guru.'),
                    'sumber_analisis' => $source,
                    'keterangan_sumber' => $personalisasi->keterangan_sumber ?? "Dianalisis via {$source}",
                    'updated_at' => $personalisasi->updated_at ? $personalisasi->updated_at->timezone('Asia/Jakarta')->format('d M Y, H:i') : now()->timezone('Asia/Jakarta')->format('d M Y, H:i'),
                    'detail_url' => route('personalisasi.show', $siswa),
                ]
            ]);
        }

        return redirect()->route('personalisasi.show', $siswa)
            ->with('success', $msg);
    }

    public function generate(): JsonResponse|RedirectResponse
    {
        $count = $this->aiService->generateAll();
        $source = $this->aiService->getLastSource();
        $msg = "Engine Personalisasi AI Kurikulum Merdeka berhasil menganalisis & menyimpan {$count} siswa SMA ke database: Sumber {$source}.";

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $msg,
                'redirect' => route('personalisasi.index'),
            ]);
        }

        return redirect()->route('personalisasi.index')
            ->with('success', $msg);
    }

    public function updateCatatan(Request $request, Siswa $siswa): RedirectResponse
    {
        $request->validate([
            'catatan_guru' => 'nullable|string',
        ]);

        Personalisasi::updateOrCreate(
            ['siswa_id' => $siswa->id],
            ['catatan_guru' => $request->input('catatan_guru')]
        );

        return redirect()->route('personalisasi.show', $siswa)
            ->with('success', 'Catatan & Rencana Intervensi Guru berhasil disimpan dan diperbarui pada Status Diagnostik.');
    }
}
