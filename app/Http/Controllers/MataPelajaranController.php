<?php

namespace App\Http\Controllers;

use App\Models\MataPelajaran;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MataPelajaranController extends Controller
{
    public function index(Request $request): View
    {
        $query = MataPelajaran::withCount(['lingkupMateris', 'tujuanPembelajarans']);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('nama_mapel', 'like', "%{$search}%")
                  ->orWhere('kode_mapel', 'like', "%{$search}%");
            });
        }

        if ($request->filled('fase')) {
            $query->where('fase', $request->input('fase'));
        }

        $mataPelajarans = $query->orderBy('kode_mapel')->paginate(10)->withQueryString();

        return view('matapelajaran.index', compact('mataPelajarans'));
    }

    public function create(): View
    {
        return view('matapelajaran.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'kode_mapel' => 'required|string|max:20|unique:mata_pelajarans,kode_mapel',
            'nama_mapel' => 'required|string|max:150',
            'fase' => 'required|string|max:20',
            'tingkat' => 'required|string|max:20',
            'kkm' => 'required|numeric|min:0|max:100',
            'deskripsi' => 'nullable|string',
        ]);

        $mapel = MataPelajaran::create($validated);

        return redirect()->route('matapelajaran.index')
            ->with('success', "Mata Pelajaran {$mapel->nama_mapel} ({$mapel->kode_mapel}) berhasil ditambahkan.");
    }

    public function show(MataPelajaran $matapelajaran): View
    {
        $matapelajaran->load(['lingkupMateris.tujuanPembelajarans']);
        return view('matapelajaran.show', compact('matapelajaran'));
    }

    public function edit(MataPelajaran $matapelajaran): View
    {
        return view('matapelajaran.edit', compact('matapelajaran'));
    }

    public function update(Request $request, MataPelajaran $matapelajaran): RedirectResponse
    {
        $validated = $request->validate([
            'kode_mapel' => "required|string|max:20|unique:mata_pelajarans,kode_mapel,{$matapelajaran->id}",
            'nama_mapel' => 'required|string|max:150',
            'fase' => 'required|string|max:20',
            'tingkat' => 'required|string|max:20',
            'kkm' => 'required|numeric|min:0|max:100',
            'deskripsi' => 'nullable|string',
        ]);

        $matapelajaran->update($validated);

        return redirect()->route('matapelajaran.index')
            ->with('success', "Mata Pelajaran {$matapelajaran->nama_mapel} berhasil diperbarui.");
    }

    public function destroy(MataPelajaran $matapelajaran): RedirectResponse
    {
        $nama = $matapelajaran->nama_mapel;
        $matapelajaran->delete();

        return redirect()->route('matapelajaran.index')
            ->with('success', "Mata Pelajaran {$nama} berhasil dihapus.");
    }
}
