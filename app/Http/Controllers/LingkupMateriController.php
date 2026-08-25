<?php

namespace App\Http\Controllers;

use App\Models\LingkupMateri;
use App\Models\MataPelajaran;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LingkupMateriController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'mata_pelajaran_id' => 'required|exists:mata_pelajarans,id',
            'kode_lm' => 'required|string|max:50',
            'judul_materi' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'urutan' => 'nullable|integer|min:1',
        ]);

        $validated['urutan'] = $validated['urutan'] ?? 1;

        $lm = LingkupMateri::create($validated);

        return redirect()->route('tujuan-pembelajaran.index', ['mata_pelajaran_id' => $lm->mata_pelajaran_id])
            ->with('success', "Lingkup Materi {$lm->kode_lm} ({$lm->judul_materi}) berhasil ditambahkan.");
    }

    public function update(Request $request, LingkupMateri $lingkupMateri): RedirectResponse
    {
        $validated = $request->validate([
            'kode_lm' => 'required|string|max:50',
            'judul_materi' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'urutan' => 'nullable|integer|min:1',
        ]);

        $lingkupMateri->update($validated);

        return redirect()->route('tujuan-pembelajaran.index', ['mata_pelajaran_id' => $lingkupMateri->mata_pelajaran_id])
            ->with('success', "Lingkup Materi {$lingkupMateri->kode_lm} berhasil diperbarui.");
    }

    public function destroy(LingkupMateri $lingkupMateri): RedirectResponse
    {
        $mapelId = $lingkupMateri->mata_pelajaran_id;
        $kode = $lingkupMateri->kode_lm;
        $lingkupMateri->delete();

        return redirect()->route('tujuan-pembelajaran.index', ['mata_pelajaran_id' => $mapelId])
            ->with('success', "Lingkup Materi {$kode} beserta TP di dalamnya berhasil dihapus.");
    }
}
