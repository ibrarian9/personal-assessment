<?php

namespace App\Http\Controllers;

use App\Models\Personalisasi;
use App\Models\Siswa;
use App\Services\AiPersonalizationService;
use App\Services\PersonalizationService;
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

        $query = Personalisasi::with(['siswa.nilaiSumatifTps.tujuanPembelajaran.lingkupMateri']);

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

        $personalisasis = $query->orderBy('skor_performa', 'desc')->paginate(10)->withQueryString();

        // Summary statistics
        $stats = [
            'tinggi' => Personalisasi::where('kategori_pemahaman', 'Tinggi')->count(),
            'sedang' => Personalisasi::where('kategori_pemahaman', 'Sedang')->count(),
            'pendampingan' => Personalisasi::where('kategori_pemahaman', 'Perlu Pendampingan')->count(),
        ];

        return view('personalisasi.index', compact('personalisasis', 'kategori', 'gayaBelajar', 'search', 'stats'));
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
    public function aiAnalyze(Siswa $siswa): RedirectResponse
    {
        $personalisasi = $this->aiService->generateForSiswa($siswa);
        $source = $this->aiService->getLastSource();
        $reason = $this->aiService->getLastReason();

        if ($source === 'Gemini') {
            $msg = "Analisis Diagnostik AI untuk {$siswa->nama} berhasil dibuat langsung oleh Google Gemini (gemini-3.5-flash) dan disimpan ke database (Skor: {$personalisasi->skor_performa}, Kategori: {$personalisasi->kategori_pemahaman}).";
        } elseif ($source === 'OpenAI') {
            $msg = "Analisis Diagnostik AI untuk {$siswa->nama} berhasil dibuat langsung oleh OpenAI (gpt-4o-mini) dan disimpan ke database (Skor: {$personalisasi->skor_performa}, Kategori: {$personalisasi->kategori_pemahaman}).";
        } else {
            $keterangan = $reason ? " ({$reason})" : '';
            $msg = "Analisis Diagnostik untuk {$siswa->nama} diproses menggunakan Fallback Heuristik Kurikulum Merdeka{$keterangan} dan disimpan ke database (Skor: {$personalisasi->skor_performa}, Kategori: {$personalisasi->kategori_pemahaman}).";
        }

        return redirect()->route('personalisasi.show', $siswa)
            ->with('success', $msg);
    }

    public function generate(): RedirectResponse
    {
        $count = $this->aiService->generateAll();
        $source = $this->aiService->getLastSource();
        return redirect()->route('personalisasi.index')
            ->with('success', "Engine Personalisasi AI Kurikulum Merdeka berhasil menganalisis & menyimpan {$count} siswa SMA ke database (Sumber: {$source}).");
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
