<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use App\Services\PersonalizationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SiswaController extends Controller
{
    protected PersonalizationService $personalizationService;

    public function __construct(PersonalizationService $personalizationService)
    {
        $this->personalizationService = $personalizationService;
    }

    public function index(Request $request): View
    {
        $query = Siswa::with(['nilaiSumatifTps', 'personalisasis']);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nisn', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('kelas')) {
            $query->where('kelas', $request->input('kelas'));
        }

        if ($request->filled('gaya_belajar')) {
            $query->where('gaya_belajar', $request->input('gaya_belajar'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $siswas = $query->orderBy('nama')->paginate(10)->withQueryString();
        $kelasList = Siswa::select('kelas')->distinct()->pluck('kelas');

        return view('siswa.index', compact('siswas', 'kelasList'));
    }

    public function create(): View
    {
        return view('siswa.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nisn' => 'required|string|max:20|unique:siswas,nisn',
            'nama' => 'required|string|max:150',
            'kelas' => 'required|string|max:50',
            'email' => 'nullable|email|max:100',
            'gaya_belajar' => 'required|in:Visual,Auditori,Kinestetik,Baca/Tulis',
            'status' => 'required|in:Aktif,Non-Aktif,Mutasi,Lulus',
        ]);

        $siswa = Siswa::create($validated);
        $this->personalizationService->generateForSiswa($siswa);

        return redirect()->route('siswa.index')->with('success', "Data siswa {$siswa->nama} (NISN: {$siswa->nisn}) berhasil ditambahkan.");
    }

    public function show(Siswa $siswa): View
    {
        $siswa->load(['nilaiSumatifTps.tujuanPembelajaran.lingkupMateri.mataPelajaran', 'personalisasis']);
        return view('siswa.show', compact('siswa'));
    }

    public function edit(Siswa $siswa): View
    {
        return view('siswa.edit', compact('siswa'));
    }

    public function update(Request $request, Siswa $siswa): RedirectResponse
    {
        $validated = $request->validate([
            'nisn' => "required|string|max:20|unique:siswas,nisn,{$siswa->id}",
            'nama' => 'required|string|max:150',
            'kelas' => 'required|string|max:50',
            'email' => 'nullable|email|max:100',
            'gaya_belajar' => 'required|in:Visual,Auditori,Kinestetik,Baca/Tulis',
            'status' => 'required|in:Aktif,Non-Aktif,Mutasi,Lulus',
        ]);

        $siswa->update($validated);
        $this->personalizationService->generateForSiswa($siswa);

        return redirect()->route('siswa.index')->with('success', "Data siswa {$siswa->nama} berhasil diperbarui.");
    }

    public function destroy(Siswa $siswa): RedirectResponse
    {
        $nama = $siswa->nama;
        $siswa->delete();

        return redirect()->route('siswa.index')->with('success', "Data siswa {$nama} berhasil dihapus.");
    }
}
