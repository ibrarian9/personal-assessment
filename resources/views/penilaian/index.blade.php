@extends('layouts.app')

@section('title', 'Data Penilaian Sumatif TP')

@section('content')
<!-- Page Header -->
<x-page-header title="Data Penilaian Sumatif TP" subtitle="Rekapitulasi capaian sumatif per siswa pada mata pelajaran dan kelas terpilih">
    <x-slot:actions>
        @if($selectedMapel)
            <a href="{{ route('penilaian.template-excel', ['mata_pelajaran_id' => $selectedMapel->id, 'kelas' => $selectedKelas]) }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 text-sm font-semibold rounded-xl bg-emerald-50 text-emerald-700 hover:bg-emerald-100 border border-emerald-200 transition-colors shadow-xs">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                </svg>
                <span>Download Template Excel</span>
            </a>
        @endif
        <x-button variant="primary" href="{{ route('penilaian.matrix', ['mata_pelajaran_id' => $selectedMapel?->id, 'kelas' => $selectedKelas]) }}">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
            </svg>
            <span>Buka Input Matriks Grid</span>
            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" /></svg>
        </x-button>
    </x-slot:actions>
</x-page-header>

<!-- Filter Selection Bar Card -->
<x-card class="space-y-4">
    <form method="GET" action="{{ route('penilaian.index') }}" class="flex flex-wrap items-center gap-4">
        <div class="flex items-center gap-2">
            <label class="text-xs font-bold uppercase text-slate-500" for="mata_pelajaran_id">Mata Pelajaran:</label>
            <x-filter-select name="mata_pelajaran_id" id="mata_pelajaran_id" onchange="this.form.submit()">
                @forelse($mapels as $m)
                    <option value="{{ $m->id }}" {{ ($selectedMapel && $selectedMapel->id == $m->id) ? 'selected' : '' }}>
                        {{ $m->kode_mapel }} - {{ $m->nama_mapel }}
                    </option>
                @empty
                    <option value="">-- Belum Ada Mata Pelajaran --</option>
                @endforelse
            </x-filter-select>
        </div>

        <div class="flex items-center gap-2">
            <label class="text-xs font-bold uppercase text-slate-500" for="kelas">Kelas SMA:</label>
            <x-filter-select name="kelas" id="kelas" onchange="this.form.submit()">
                @php
                    $availableKelas = $kelasList->isNotEmpty() ? $kelasList : collect(['X-A', 'X-B', 'XI-A', 'XII-A']);
                @endphp
                @foreach($availableKelas as $k)
                    <option value="{{ $k }}" {{ $selectedKelas == $k ? 'selected' : '' }}>Kelas {{ $k }}</option>
                @endforeach
            </x-filter-select>
        </div>

        <x-button type="submit" variant="secondary">
            Filter
        </x-button>
    </form>
</x-card>

<!-- Table Card -->
<x-card>
    <x-table>
        <x-thead>
            <x-th>NISN</x-th>
            <x-th>Nama Siswa</x-th>
            <x-th align="center">Gaya Belajar</x-th>
            <x-th align="center">Jumlah TP Dinilai</x-th>
            <x-th align="center">TP Tuntas (&ge; 75)</x-th>
            <x-th align="center">TP Remedial (< 75)</x-th>
            <x-th align="center">Rata-Rata Sumatif</x-th>
            <x-th align="center">Aksi</x-th>
        </x-thead>
        <tbody class="divide-y divide-slate-100">
            @forelse($siswas as $s)
                @php
                    $tpScores = $s->nilaiSumatifTps;
                    $cnt = $tpScores->count();
                    $tuntasCnt = $tpScores->where('status_ketuntasan', 'Tuntas')->count();
                    $belumCnt = $tpScores->where('status_ketuntasan', 'Belum Tuntas')->count();
                    $avg = $cnt > 0 ? round($tpScores->avg('nilai'), 2) : 0;
                @endphp
                <tr class="hover:bg-slate-50/80 transition-colors">
                    <x-td class="font-mono font-bold text-slate-900">{{ $s->nisn }}</x-td>
                    <x-td class="font-semibold text-slate-900">{{ $s->nama }}</x-td>
                    <x-td align="center">
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-purple-100 text-purple-800">
                            {{ $s->gaya_belajar }}
                        </span>
                    </x-td>
                    <x-td align="center" class="font-mono font-medium">{{ $cnt }} TP</x-td>
                    <x-td align="center">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
                            {{ $tuntasCnt }} Tuntas
                        </span>
                    </x-td>
                    <x-td align="center">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold {{ $belumCnt > 0 ? 'bg-rose-100 text-rose-800' : 'bg-slate-100 text-slate-500' }}">
                            {{ $belumCnt }} Remedial
                        </span>
                    </x-td>
                    <x-td align="center" class="font-mono font-extrabold text-teal-700 text-base">
                        {{ $avg }}
                    </x-td>
                    <x-td align="center">
                        <div class="inline-flex items-center gap-1.5">
                            <x-button variant="subtle" href="{{ route('personalisasi.show', $s) }}" class="px-2.5 py-1 text-xs">
                                <span>Diagnostik AI</span>
                                <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" /></svg>
                            </x-button>
                        </div>
                    </x-td>
                </tr>
            @empty
                <x-empty-state colspan="8" message="Belum ada data nilai siswa di kelas ini. Silakan buka Input Matriks Grid atau gunakan fitur Import Excel." />
            @endforelse
        </tbody>
    </x-table>
</x-card>
@endsection
