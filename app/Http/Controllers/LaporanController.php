<?php

namespace App\Http\Controllers;

use App\Models\MataPelajaran;
use App\Models\NilaiSumatifTp;
use App\Models\Setting;
use App\Models\Siswa;
use App\Models\TujuanPembelajaran;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LaporanController extends Controller
{
    public function index(Request $request): View
    {
        $mapels = MataPelajaran::all();
        $selectedMapelId = $request->query('mata_pelajaran_id', $mapels->first()?->id);
        $selectedKelas = $request->query('kelas', 'X-A');

        $kelasList = Siswa::select('kelas')->distinct()->pluck('kelas');
        $selectedMapel = $selectedMapelId ? MataPelajaran::find($selectedMapelId) : null;

        $siswas = Siswa::where('kelas', $selectedKelas)
            ->with(['nilaiSumatifTps' => function ($q) use ($selectedMapelId) {
                if ($selectedMapelId) {
                    $q->whereHas('tujuanPembelajaran.lingkupMateri', function ($sq) use ($selectedMapelId) {
                        $sq->where('mata_pelajaran_id', $selectedMapelId);
                    });
                }
            }])
            ->orderBy('nama')
            ->get();

        $totalSiswa = $siswas->count();
        $allScores = NilaiSumatifTp::whereHas('siswa', function ($q) use ($selectedKelas) {
            $q->where('kelas', $selectedKelas);
        });

        if ($selectedMapelId) {
            $allScores->whereHas('tujuanPembelajaran.lingkupMateri', function ($q) use ($selectedMapelId) {
                $q->where('mata_pelajaran_id', $selectedMapelId);
            });
        }

        $stats = (clone $allScores)->selectRaw('
            COUNT(CASE WHEN status_ketuntasan = "Tuntas" THEN 1 END) as total_tuntas,
            COUNT(CASE WHEN status_ketuntasan = "Belum Tuntas" THEN 1 END) as total_belum_tuntas,
            AVG(nilai) as rata_rata,
            MAX(nilai) as nilai_tertinggi,
            MIN(nilai) as nilai_terendah
        ')->first();

        $totalTuntas = (int) ($stats->total_tuntas ?? 0);
        $totalBelumTuntas = (int) ($stats->total_belum_tuntas ?? 0);
        $rataRataKelas = $stats->rata_rata !== null ? round((float) $stats->rata_rata, 2) : 0.00;
        $nilaiTertinggi = (float) ($stats->nilai_tertinggi ?? 0);
        $nilaiTerendah = (float) ($stats->nilai_terendah ?? 0);

        return view('laporan.index', compact(
            'mapels',
            'selectedMapel',
            'selectedMapelId',
            'selectedKelas',
            'kelasList',
            'siswas',
            'totalSiswa',
            'totalTuntas',
            'totalBelumTuntas',
            'rataRataKelas',
            'nilaiTertinggi',
            'nilaiTerendah'
        ));
    }

    public function cetak(Request $request): View
    {
        $mapels = MataPelajaran::all();
        $selectedMapelId = $request->query('mata_pelajaran_id', $mapels->first()?->id);
        $selectedKelas = $request->query('kelas', 'X-A');

        $selectedMapel = $selectedMapelId ? MataPelajaran::find($selectedMapelId) : null;

        $siswas = Siswa::where('kelas', $selectedKelas)
            ->with(['nilaiSumatifTps' => function ($q) use ($selectedMapelId) {
                if ($selectedMapelId) {
                    $q->whereHas('tujuanPembelajaran.lingkupMateri', function ($sq) use ($selectedMapelId) {
                        $sq->where('mata_pelajaran_id', $selectedMapelId);
                    });
                }
            }, 'nilaiSumatifTps.tujuanPembelajaran.lingkupMateri'])
            ->orderBy('nama')
            ->get();

        $tujuanPembelajarans = [];
        if ($selectedMapel) {
            $tujuanPembelajarans = TujuanPembelajaran::whereHas('lingkupMateri', function ($q) use ($selectedMapel) {
                $q->where('mata_pelajaran_id', $selectedMapel->id);
            })->with('lingkupMateri')->orderBy('kode_tp')->get();
        }

        $namaSekolah = Setting::get('nama_institusi', 'SMA Negeri 1 Merdeka');
        $tahunAjaran = str_replace('/', '-', Setting::get('tahun_akademik', '2025-2026 Ganjil'));
        $kepalaSekolah = Setting::get('kepala_sekolah', 'Dr. H. Bambang Sutrisno, M.Pd.');
        $nipKepalaSekolah = Setting::get('nip_kepala_sekolah', '196805121994031005');

        return view('laporan.cetak', compact(
            'selectedMapel',
            'selectedKelas',
            'siswas',
            'tujuanPembelajarans',
            'namaSekolah',
            'tahunAjaran',
            'kepalaSekolah',
            'nipKepalaSekolah'
        ));
    }
}
