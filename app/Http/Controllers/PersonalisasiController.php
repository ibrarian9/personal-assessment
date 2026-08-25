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
        $personalisasi = $siswa->personalisasis->first();

        if (!$personalisasi) {
            $personalisasi = $this->aiService->generateForSiswa($siswa);
        }

        // Parse list of remedial TPs
        $remedialTps = [];
        if ($personalisasi && $personalisasi->daftar_tp_remedial) {
            $remedialTps = json_decode($personalisasi->daftar_tp_remedial, true) ?? [];
        }

        return view('personalisasi.show', compact('siswa', 'personalisasi', 'remedialTps'));
    }

    /**
     * Jalankan analisis AI khusus untuk seorang siswa SMA
     */
    public function aiAnalyze(Siswa $siswa): RedirectResponse
    {
        $personalisasi = $this->aiService->generateForSiswa($siswa);

        return redirect()->route('personalisasi.show', $siswa)
            ->with('success', "Analisis Diagnostik AI untuk {$siswa->nama} berhasil diproses dan disimpan ke database (Skor: {$personalisasi->skor_performa}, Kategori: {$personalisasi->kategori_pemahaman}).");
    }

    public function generate(): RedirectResponse
    {
        $count = $this->aiService->generateAll();
        return redirect()->route('personalisasi.index')
            ->with('success', "Engine Personalisasi AI Kurikulum Merdeka berhasil menganalisis & menyimpan {$count} siswa SMA ke database.");
    }

    public function updateCatatan(Request $request, Siswa $siswa): RedirectResponse
    {
        $request->validate([
            'catatan_guru' => 'nullable|string',
        ]);

        Personalisasi::where('siswa_id', $siswa->id)->update([
            'catatan_guru' => $request->input('catatan_guru'),
        ]);

        return redirect()->route('personalisasi.show', $siswa)
            ->with('success', 'Catatan & Rencana Intervensi Guru berhasil disimpan.');
    }
}
