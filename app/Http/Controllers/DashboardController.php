<?php

namespace App\Http\Controllers;

use App\Models\MataPelajaran;
use App\Models\NilaiSumatifTp;
use App\Models\Personalisasi;
use App\Models\Siswa;
use App\Models\TujuanPembelajaran;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $totalSiswa = Siswa::count();
        $totalMapel = MataPelajaran::count();
        $totalTp = TujuanPembelajaran::count();
        
        $totalNilai = NilaiSumatifTp::count();
        $totalTuntas = NilaiSumatifTp::where('status_ketuntasan', 'Tuntas')->count();
        $totalBelumTuntas = NilaiSumatifTp::where('status_ketuntasan', 'Belum Tuntas')->count();
        $persentaseKetuntasan = $totalNilai > 0 ? round(($totalTuntas / $totalNilai) * 100, 1) : 0;

        $rataRataKeseluruhan = NilaiSumatifTp::avg('nilai');
        $rataRataFormatted = $rataRataKeseluruhan ? round((float)$rataRataKeseluruhan, 2) : 0.00;

        // Distribusi Kategori Pemahaman
        $kategoriDistribution = [
            'Tinggi' => Personalisasi::where('kategori_pemahaman', 'Tinggi')->count(),
            'Sedang' => Personalisasi::where('kategori_pemahaman', 'Sedang')->count(),
            'Perlu Pendampingan' => Personalisasi::where('kategori_pemahaman', 'Perlu Pendampingan')->count(),
        ];

        // Distribusi Gaya Belajar VARK Siswa
        $varkDistribution = [
            'Visual' => Siswa::where('gaya_belajar', 'Visual')->count(),
            'Auditori' => Siswa::where('gaya_belajar', 'Auditori')->count(),
            'Kinestetik' => Siswa::where('gaya_belajar', 'Kinestetik')->count(),
            'Baca/Tulis' => Siswa::where('gaya_belajar', 'Baca/Tulis')->count(),
        ];

        // Ringkasan Siswa
        $siswas = Siswa::with(['nilaiSumatifTps', 'personalisasis'])
            ->take(10)
            ->get();

        // Siswa Perlu Pendampingan / Remedial
        $atRiskStudents = Personalisasi::with('siswa')
            ->where('kategori_pemahaman', 'Perlu Pendampingan')
            ->orWhere('tp_belum_tuntas_count', '>', 0)
            ->orderBy('tp_belum_tuntas_count', 'desc')
            ->take(5)
            ->get();

        return view('dashboard', compact(
            'totalSiswa',
            'totalMapel',
            'totalTp',
            'totalTuntas',
            'totalBelumTuntas',
            'persentaseKetuntasan',
            'rataRataFormatted',
            'kategoriDistribution',
            'varkDistribution',
            'siswas',
            'atRiskStudents'
        ));
    }
}
