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
        
        $statsNilai = NilaiSumatifTp::selectRaw('
            COUNT(*) as total_nilai,
            COUNT(CASE WHEN status_ketuntasan = "Tuntas" THEN 1 END) as total_tuntas,
            COUNT(CASE WHEN status_ketuntasan = "Belum Tuntas" THEN 1 END) as total_belum_tuntas,
            AVG(nilai) as avg_nilai
        ')->first();

        $totalNilai = (int) ($statsNilai->total_nilai ?? 0);
        $totalTuntas = (int) ($statsNilai->total_tuntas ?? 0);
        $totalBelumTuntas = (int) ($statsNilai->total_belum_tuntas ?? 0);
        $persentaseKetuntasan = $totalNilai > 0 ? round(($totalTuntas / $totalNilai) * 100, 1) : 0;
        $rataRataFormatted = $statsNilai->avg_nilai !== null ? round((float) $statsNilai->avg_nilai, 2) : 0.00;

        // Distribusi Kategori Pemahaman
        $kategoriDistribution = array_merge([
            'Tinggi' => 0,
            'Sedang' => 0,
            'Perlu Pendampingan' => 0,
        ], Personalisasi::selectRaw('kategori_pemahaman, count(*) as total')
            ->groupBy('kategori_pemahaman')
            ->pluck('total', 'kategori_pemahaman')
            ->toArray()
        );

        // Distribusi Gaya Belajar VARK Siswa
        $varkDistribution = array_merge([
            'Visual' => 0,
            'Auditori' => 0,
            'Kinestetik' => 0,
            'Baca/Tulis' => 0,
        ], Siswa::selectRaw('gaya_belajar, count(*) as total')
            ->groupBy('gaya_belajar')
            ->pluck('total', 'gaya_belajar')
            ->toArray()
        );

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
