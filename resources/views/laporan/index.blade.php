@extends('layouts.app')

@section('title', 'Laporan & Raport Kurikulum Merdeka')

@section('content')
<!-- Page Header -->
<x-page-header title="Laporan & Rekapitulasi Capaian TP" subtitle="Rekapitulasi ketercapaian Tujuan Pembelajaran, distribusi ketuntasan KKM, dan ringkasan nilai sumatif akhir">
    <x-slot:actions>
        <x-button variant="primary" href="{{ route('laporan.cetak', request()->query()) }}" target="_blank">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0l.229 2.523a1.125 1.125 0 01-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0021 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 00-1.913-.247M6.34 18H5.25A2.25 2.25 0 013 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 011.913-.247m10.5 0a48.536 48.536 0 00-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.656h10.5z" />
            </svg>
            <span>Cetak dan Unduh Raport Sumatif</span>
        </x-button>
    </x-slot:actions>
</x-page-header>

<!-- Stats Summary Grid -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
    <div class="rounded-2xl bg-white p-6 shadow-xs border border-slate-200/80">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Total Siswa Kelas</p>
                <h3 class="text-3xl font-extrabold text-slate-900 mt-2">{{ $totalSiswa }} <span class="text-sm font-normal text-slate-400">Siswa</span></h3>
            </div>
            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-teal-50 text-teal-600 border border-teal-100">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
            </div>
        </div>
        <p class="mt-2 text-xs text-slate-400">{{ $selectedKelas }}</p>
    </div>

    <div class="rounded-2xl bg-white p-6 shadow-xs border border-slate-200/80">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Ketuntasan Butir TP</p>
                <h3 class="text-3xl font-extrabold text-emerald-600 mt-2">{{ $totalTuntas }} <span class="text-sm font-normal text-slate-400">Tuntas</span></h3>
            </div>
            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600 border border-emerald-100">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </div>
        <p class="mt-2 text-xs text-rose-600 font-medium">{{ $totalBelumTuntas }} Perlu Remedial</p>
    </div>

    <div class="rounded-2xl bg-white p-6 shadow-xs border border-slate-200/80">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Rata-Rata Kelas</p>
                <h3 class="text-3xl font-extrabold text-teal-700 mt-2 font-mono">{{ $rataRataKelas }}</h3>
            </div>
            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-purple-50 text-purple-600 border border-purple-100">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
            </div>
        </div>
        <p class="mt-2 text-xs text-slate-400">Skala 0 - 100 &bull; KKM 75</p>
    </div>

    <div class="rounded-2xl bg-white p-6 shadow-xs border border-slate-200/80">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Rentang Nilai</p>
                <h3 class="text-2xl font-extrabold text-slate-900 mt-2 font-mono">{{ $nilaiTerendah }} - {{ $nilaiTertinggi }}</h3>
            </div>
            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-amber-50 text-amber-600 border border-amber-100">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z" />
                </svg>
            </div>
        </div>
        <p class="mt-2 text-xs text-slate-400">Min - Max Skor TP</p>
    </div>
</div>

<!-- Card Table -->
<x-card>
    <!-- Filter Bar -->
    <form method="GET" action="{{ route('laporan.index') }}" class="flex flex-wrap items-center gap-3">
        <x-filter-select name="mata_pelajaran_id">
            @forelse($mapels as $mk)
                <option value="{{ $mk->id }}" {{ $selectedMapelId == $mk->id ? 'selected' : '' }}>{{ $mk->kode_mapel }} - {{ $mk->nama_mapel }}</option>
            @empty
                <option value="">-- Belum Ada Mata Pelajaran --</option>
            @endforelse
        </x-filter-select>

        <x-filter-select name="kelas">
            @php
                $availableKelas = $kelasList->isNotEmpty() ? $kelasList : collect(['X-A', 'X-B', 'XI-A', 'XII-A']);
            @endphp
            @foreach($availableKelas as $k)
                <option value="{{ $k }}" {{ $selectedKelas == $k ? 'selected' : '' }}>Kelas {{ $k }}</option>
            @endforeach
        </x-filter-select>

        <x-button type="submit" variant="secondary">
            Terapkan Filter
        </x-button>
    </form>

    <!-- Table Responsive -->
    <x-table>
        <x-thead>
            <x-th align="center" class="w-12">No</x-th>
            <x-th class="font-mono">NISN</x-th>
            <x-th>Nama Siswa</x-th>
            <x-th align="center">Kelas</x-th>
            <x-th align="center">Gaya Belajar</x-th>
            <x-th align="center">Total TP Dinilai</x-th>
            <x-th align="center">TP Tuntas</x-th>
            <x-th align="center">TP Remedial</x-th>
            <x-th align="center">Rata-Rata Akhir</x-th>
            <x-th align="center">Predikat</x-th>
            <x-th align="center">Status</x-th>
        </x-thead>
        <tbody class="divide-y divide-slate-100">
            @forelse($siswas as $index => $s)
                @php
                    $scores = $s->nilaiSumatifTps;
                    $cnt = $scores->count();
                    $tuntas = $scores->where('status_ketuntasan', 'Tuntas')->count();
                    $belum = $scores->where('status_ketuntasan', 'Belum Tuntas')->count();
                    $avg = $cnt > 0 ? round($scores->avg('nilai'), 2) : 0;
                    $predikat = \App\Models\NilaiSumatifTp::tentukanPredikat($avg);
                @endphp
                <tr class="hover:bg-slate-50/80 transition-colors">
                    <x-td align="center" class="text-slate-400 font-mono">{{ $index + 1 }}</x-td>
                    <x-td class="font-mono font-bold text-slate-800">{{ $s->nisn }}</x-td>
                    <x-td class="font-semibold text-slate-900">{{ $s->nama }}</x-td>
                    <x-td align="center">{{ $s->kelas }}</x-td>
                    <x-td align="center">
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-purple-100 text-purple-800">
                            {{ str_replace(['Baca/Tulis', '/'], ['Baca dan Tulis', ' dan '], $s->gaya_belajar) }}
                        </span>
                    </x-td>
                    <x-td align="center" class="font-mono">{{ $cnt }} TP</x-td>
                    <x-td align="center" class="font-mono font-bold text-emerald-600">{{ $tuntas }}</x-td>
                    <x-td align="center" class="font-mono font-bold text-rose-600">{{ $belum }}</x-td>
                    <x-td align="center" class="font-mono font-extrabold text-teal-700">{{ $avg }}</x-td>
                    <x-td align="center" class="font-semibold text-xs text-slate-800">
                        {{ $predikat }}
                    </x-td>
                    <x-td align="center">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $belum === 0 ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                            {{ $belum === 0 ? 'Tuntas' : 'Perlu Remedial' }}
                        </span>
                    </x-td>
                </tr>
            @empty
                <x-empty-state colspan="11" message="Tidak ada data laporan di kelas ini." />
            @endforelse
        </tbody>
    </x-table>
</x-card>
@endsection
