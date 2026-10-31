<!DOCTYPE html>
<html lang="id" class="bg-white">
<head>
    <meta charset="UTF-8">
    <title>Rekapitulasi Nilai Sumatif TP - {{ $namaSekolah }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @media print {
            .no-print { display: none !important; }
            body { margin: 0; padding: 12px; }
            @page { size: landscape; margin: 10mm; }
        }
    </style>
</head>
<body class="p-8 text-slate-800 font-sans text-xs antialiased max-w-6xl mx-auto">
    <!-- Print Button -->
    <div class="no-print mb-6 flex justify-end">
        <button onclick="window.print()" class="inline-flex items-center gap-2 px-5 py-2.5 bg-teal-800 text-white rounded-xl font-bold shadow-md hover:bg-teal-900 transition-colors">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0l.229 2.523a1.125 1.125 0 01-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0021 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 00-1.913-.247M6.34 18H5.25A2.25 2.25 0 013 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 011.913-.247m10.5 0a48.536 48.536 0 00-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.656h10.5z" />
            </svg>
            Cetak Dokumen Raport
        </button>
    </div>

    <!-- School Header -->
    <div class="text-center border-b-2 border-teal-900 pb-4 mb-6">
        <h2 class="text-lg font-black text-slate-900 tracking-wider uppercase">{{ $namaSekolah }}</h2>
        <h1 class="text-base font-extrabold text-teal-900 tracking-tight uppercase mt-0.5">REKAPITULASI NILAI SUMATIF PER TUJUAN PEMBELAJARAN (TP)</h1>
        <p class="text-xs text-slate-600 font-semibold tracking-wide uppercase mt-1">KURIKULUM MERDEKA - TAHUN AJARAN {{ $tahunAjaran }}</p>
        @if($selectedMapel)
            <p class="text-xs text-slate-800 mt-1 font-medium"><strong>Mata Pelajaran:</strong> {{ $selectedMapel->nama_mapel }} ({{ $selectedMapel->kode_mapel }}) &bull; <strong>Kelas:</strong> {{ $selectedKelas }} &bull; <strong>KKM TP:</strong> {{ $selectedMapel->kkm }}</p>
        @endif
    </div>

    <!-- Recap Table -->
    <div class="overflow-x-auto mb-8">
        <table class="w-full text-left border-collapse border border-slate-400">
            <thead class="bg-slate-100 text-slate-900 font-bold">
                <tr>
                    <th class="border border-slate-400 py-2 px-2 text-center w-8">No</th>
                    <th class="border border-slate-400 py-2 px-2 font-mono w-28">NISN</th>
                    <th class="border border-slate-400 py-2 px-3">Nama Siswa</th>
                    <th class="border border-slate-400 py-2 px-2 text-center">Kelas</th>
                    <th class="border border-slate-400 py-2 px-2 text-center">Gaya Belajar</th>
                    <th class="border border-slate-400 py-2 px-2 text-center">TP Tuntas</th>
                    <th class="border border-slate-400 py-2 px-2 text-center">TP Remedial</th>
                    <th class="border border-slate-400 py-2 px-2 text-center font-extrabold">Rata-Rata Sumatif</th>
                    <th class="border border-slate-400 py-2 px-2 text-center">Predikat</th>
                    <th class="border border-slate-400 py-2 px-2 text-center">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-300">
                @forelse($siswas as $index => $s)
                    @php
                        $scores = $s->nilaiSumatifTps;
                        $cnt = $scores->count();
                        $tuntas = $scores->where('status_ketuntasan', 'Tuntas')->count();
                        $belum = $scores->where('status_ketuntasan', 'Belum Tuntas')->count();
                        $avg = $cnt > 0 ? round($scores->avg('nilai'), 2) : 0;
                        $predikat = \App\Models\NilaiSumatifTp::tentukanPredikat($avg);
                    @endphp
                    <tr>
                        <td class="border border-slate-400 py-1.5 px-2 text-center font-mono">{{ $index + 1 }}</td>
                        <td class="border border-slate-400 py-1.5 px-2 font-mono font-bold">{{ $s->nisn }}</td>
                        <td class="border border-slate-400 py-1.5 px-3 font-semibold">{{ $s->nama }}</td>
                        <td class="border border-slate-400 py-1.5 px-2 text-center">{{ $s->kelas }}</td>
                        <td class="border border-slate-400 py-1.5 px-2 text-center">{{ str_replace(['Baca/Tulis', '/'], ['Baca dan Tulis', ' dan '], $s->gaya_belajar) }}</td>
                        <td class="border border-slate-400 py-1.5 px-2 text-center font-mono font-bold text-emerald-700">{{ $tuntas }}</td>
                        <td class="border border-slate-400 py-1.5 px-2 text-center font-mono font-bold text-rose-700">{{ $belum }}</td>
                        <td class="border border-slate-400 py-1.5 px-2 text-center font-mono font-extrabold text-teal-900">{{ $avg }}</td>
                        <td class="border border-slate-400 py-1.5 px-2 text-center font-medium">{{ $predikat }}</td>
                        <td class="border border-slate-400 py-1.5 px-2 text-center font-bold {{ $belum === 0 ? 'text-emerald-800' : 'text-rose-800' }}">
                            {{ $belum === 0 ? 'TUNTAS' : 'REMEDIAL' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="border border-slate-400 py-6 text-center text-slate-400">Tidak ada data untuk dicetak.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Signatures -->
    <div class="grid grid-cols-2 mt-12 text-xs text-slate-800">
        <div>
            <p>Mengetahui,<br>Kepala Sekolah {{ $namaSekolah }}</p>
            <div class="mt-16">
                <p class="font-bold underline">{{ $kepalaSekolah }}</p>
                <p class="text-slate-500 mt-0.5">NIP. {{ $nipKepalaSekolah }}</p>
            </div>
        </div>
        <div class="text-right">
            <p>Guru Mata Pelajaran dan Wali Kelas</p>
            <div class="mt-16">
                <p class="font-bold underline">( Dra. Sri Wahyuni, M.Pd. )</p>
                <p class="text-slate-500 mt-0.5">NIP. 197508192002122003</p>
            </div>
        </div>
    </div>
</body>
</html>
