<?php

namespace App\Http\Controllers;

use App\Models\LingkupMateri;
use App\Models\MataPelajaran;
use App\Models\TujuanPembelajaran;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TujuanPembelajaranController extends Controller
{
    public function index(Request $request): View
    {
        $mapelId = $request->query('mata_pelajaran_id');
        $mapels = MataPelajaran::all();

        $selectedMapel = $mapelId ? MataPelajaran::find($mapelId) : $mapels->first();

        $lingkupMateris = [];
        if ($selectedMapel) {
            $lingkupMateris = LingkupMateri::where('mata_pelajaran_id', $selectedMapel->id)
                ->with('tujuanPembelajarans')
                ->orderBy('urutan')
                ->get();
        }

        return view('tujuan_pembelajaran.index', compact('mapels', 'selectedMapel', 'lingkupMateris'));
    }

    public function create(Request $request): View
    {
        $mapels = MataPelajaran::all();
        $selectedMapelId = $request->query('mata_pelajaran_id', $mapels->first()?->id);
        $lingkupMateris = LingkupMateri::where('mata_pelajaran_id', $selectedMapelId)->orderBy('urutan')->get();

        return view('tujuan_pembelajaran.create', compact('mapels', 'lingkupMateris', 'selectedMapelId'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'lingkup_materi_id' => 'required|exists:lingkup_materis,id',
            'kode_tp' => 'required|string|max:50',
            'deskripsi_tp' => 'required|string',
            'target_ketercapaian' => 'required|numeric|min:0|max:100',
            'urutan' => 'nullable|integer|min:1',
        ]);

        $validated['urutan'] = $validated['urutan'] ?? 1;

        $tp = TujuanPembelajaran::create($validated);
        $mapelId = $tp->lingkupMateri->mata_pelajaran_id;

        return redirect()->route('tujuan-pembelajaran.index', ['mata_pelajaran_id' => $mapelId])
            ->with('success', "Tujuan Pembelajaran {$tp->kode_tp} berhasil ditambahkan.");
    }

    public function edit(TujuanPembelajaran $tujuanPembelajaran): View
    {
        $tujuanPembelajaran->load('lingkupMateri.mataPelajaran');
        $mapels = MataPelajaran::all();
        $lingkupMateris = LingkupMateri::where('mata_pelajaran_id', $tujuanPembelajaran->lingkupMateri->mata_pelajaran_id)->get();

        return view('tujuan_pembelajaran.edit', compact('tujuanPembelajaran', 'mapels', 'lingkupMateris'));
    }

    public function update(Request $request, TujuanPembelajaran $tujuanPembelajaran): RedirectResponse
    {
        $validated = $request->validate([
            'lingkup_materi_id' => 'required|exists:lingkup_materis,id',
            'kode_tp' => 'required|string|max:50',
            'deskripsi_tp' => 'required|string',
            'target_ketercapaian' => 'required|numeric|min:0|max:100',
            'urutan' => 'nullable|integer|min:1',
        ]);

        $tujuanPembelajaran->update($validated);
        $mapelId = $tujuanPembelajaran->lingkupMateri->mata_pelajaran_id;

        return redirect()->route('tujuan-pembelajaran.index', ['mata_pelajaran_id' => $mapelId])
            ->with('success', "Tujuan Pembelajaran {$tujuanPembelajaran->kode_tp} berhasil diperbarui.");
    }

    public function destroy(TujuanPembelajaran $tujuanPembelajaran): RedirectResponse
    {
        $kode = $tujuanPembelajaran->kode_tp;
        $mapelId = $tujuanPembelajaran->lingkupMateri->mata_pelajaran_id;
        $tujuanPembelajaran->delete();

        return redirect()->route('tujuan-pembelajaran.index', ['mata_pelajaran_id' => $mapelId])
            ->with('success', "Tujuan Pembelajaran {$kode} berhasil dihapus.");
    }
}
