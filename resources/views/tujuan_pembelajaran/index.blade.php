@extends('layouts.app')

@section('title', 'Lingkup Materi & Tujuan Pembelajaran (TP)')

@section('content')
<!-- Page Header -->
<x-page-header title="Lingkup Materi & Tujuan Pembelajaran (TP)" subtitle="Struktur hirarki Capaian Elemen, Lingkup Materi (Materi 1..10), dan butir indikator Tujuan Pembelajaran SMA">
    <x-slot:actions>
        <x-button variant="primary" href="{{ route('tujuan-pembelajaran.create', ['mata_pelajaran_id' => $selectedMapel?->id]) }}">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
            </svg>
            <span>Tambah TP Baru</span>
        </x-button>
    </x-slot:actions>
</x-page-header>

<!-- Mapel Dropdown Filter -->
<x-card class="p-5">
    <form method="GET" action="{{ route('tujuan-pembelajaran.index') }}" class="flex flex-wrap items-center gap-4">
        <div class="flex items-center gap-2.5">
            <label class="text-xs font-bold uppercase text-slate-500" for="mata_pelajaran_id">Pilih Mata Pelajaran:</label>
            <x-filter-select name="mata_pelajaran_id" id="mata_pelajaran_id" onchange="this.form.submit()">
                @forelse($mapels as $m)
                    <option value="{{ $m->id }}" {{ ($selectedMapel && $selectedMapel->id == $m->id) ? 'selected' : '' }}>
                        {{ $m->kode_mapel }} - {{ $m->nama_mapel }} ({{ $m->fase }} &bull; {{ $m->tingkat }})
                    </option>
                @empty
                    <option value="">-- Belum Ada Mata Pelajaran --</option>
                @endforelse
            </x-filter-select>
        </div>
        <x-button type="submit" variant="secondary">
            Tampilkan
        </x-button>
    </form>
</x-card>

@if($selectedMapel)
<!-- Subject Header Banner (Matching Image 2) -->
<div class="rounded-2xl bg-gradient-to-r from-teal-800 via-teal-900 to-slate-900 p-6 text-white shadow-xs border border-teal-700/50 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
        <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-bold bg-teal-500/30 text-teal-200 border border-teal-400/30">
            {{ $selectedMapel->fase }} &bull; {{ $selectedMapel->tingkat }}
        </span>
        <h3 class="text-2xl font-black tracking-tight mt-2 text-white uppercase">{{ $selectedMapel->nama_mapel }}</h3>
        <p class="text-xs text-slate-300 mt-1 max-w-2xl">{{ $selectedMapel->deskripsi ?? 'Kurikulum Merdeka SMA' }}</p>
    </div>
    <div class="text-right flex sm:flex-col items-center sm:items-end justify-between gap-2 border-t sm:border-t-0 border-teal-700/50 pt-3 sm:pt-0">
        <span class="text-xs text-teal-300 uppercase tracking-wider font-semibold">Standar KKM TP</span>
        <span class="text-3xl font-black font-mono text-white">{{ $selectedMapel->kkm }}</span>
    </div>
</div>

<!-- Add Lingkup Materi Form (Inline Quick Add) -->
<x-card class="p-5">
    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-3 flex items-center gap-1.5">
        <svg class="h-4 w-4 text-teal-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
        Tambah Lingkup Materi Baru
    </h4>
    <form action="{{ route('lingkup-materi.store') }}" method="POST" class="flex flex-wrap items-center gap-3">
        @csrf
        <input type="hidden" name="mata_pelajaran_id" value="{{ $selectedMapel->id }}">
        <input type="text" name="kode_lm" class="px-3.5 py-2 text-sm rounded-xl border border-slate-300 bg-white placeholder-slate-400 focus:border-teal-500 focus:outline-none focus:ring-2 focus:ring-teal-500/20 transition-all w-36 font-semibold" placeholder="Materi 1" required>
        <input type="text" name="judul_materi" class="flex-1 min-w-[220px] px-3.5 py-2 text-sm rounded-xl border border-slate-300 bg-white placeholder-slate-400 focus:border-teal-500 focus:outline-none focus:ring-2 focus:ring-teal-500/20 transition-all" placeholder="Judul Lingkup Materi (Contoh: Sistem Gerak dan Syaraf)" required>
        <input type="number" name="urutan" class="w-24 px-3.5 py-2 text-sm rounded-xl border border-slate-300 bg-white placeholder-slate-400 focus:border-teal-500 focus:outline-none focus:ring-2 focus:ring-teal-500/20 transition-all" placeholder="Urutan" value="{{ count($lingkupMateris) + 1 }}">
        <x-button type="submit" variant="secondary">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
            <span>Tambah Materi</span>
        </x-button>
    </form>
</x-card>

<!-- Lingkup Materi & TP Table Display (Matching Image 2 Style) -->
<x-card>
    <div class="pb-3 border-b border-slate-100 flex items-center justify-between">
        <div>
            <h3 class="text-base font-bold text-slate-900">Struktur Lingkup Materi & Tujuan Pembelajaran</h3>
            <p class="text-xs text-slate-500">Daftar TP yang dinilai pada asesmen sumatif Kurikulum Merdeka</p>
        </div>
        <x-button variant="subtle" href="{{ route('penilaian.matrix', ['mata_pelajaran_id' => $selectedMapel->id]) }}" class="px-3 py-1.5 text-xs">
            Buka Matriks Penilaian
        </x-button>
    </div>

    <!-- Table structured like Image 2 -->
    <div class="overflow-x-auto rounded-xl border border-slate-300">
        <table class="w-full text-left text-sm border-collapse">
            <thead class="bg-teal-800 text-white text-xs uppercase font-extrabold tracking-wider">
                <tr>
                    <th class="py-3 px-4 w-44 border-r border-teal-700">LINGKUP MATERI</th>
                    <th class="py-3 px-4 w-32 border-r border-teal-700">KODE TP</th>
                    <th class="py-3 px-4">TUJUAN PEMBELAJARAN (TP)</th>
                    <th class="py-3 px-4 text-center w-28 border-l border-teal-700">TARGET KKM</th>
                    <th class="py-3 px-4 text-center w-36 border-l border-teal-700">AKSI</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                @forelse($lingkupMateris as $lm)
                    @php $tpCount = $lm->tujuanPembelajarans->count(); @endphp
                    @if($tpCount > 0)
                        @foreach($lm->tujuanPembelajarans as $index => $tp)
                            <tr class="hover:bg-teal-50/30 transition-colors">
                                @if($index === 0)
                                    <td class="py-3.5 px-4 font-bold text-slate-900 bg-slate-50/80 align-top border-r border-slate-200" rowspan="{{ $tpCount }}">
                                        <div class="space-y-1">
                                            <span class="inline-flex px-2 py-0.5 rounded text-xs font-extrabold bg-teal-100 text-teal-800">
                                                {{ $lm->kode_lm }}
                                            </span>
                                            <p class="text-xs text-slate-700 font-semibold">{{ $lm->judul_materi }}</p>
                                            <div class="pt-2">
                                                <form action="{{ route('lingkup-materi.destroy', $lm) }}" method="POST" class="inline" data-confirm="Hapus {{ $lm->kode_lm }} ({{ $lm->judul_materi }}) beserta seluruh TP di dalamnya?" data-confirm-title="Hapus Lingkup Materi">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-[11px] text-rose-600 hover:underline font-semibold">Hapus Materi</button>
                                                </form>
                                            </div>
                                        </div>
                                    </td>
                                @endif
                                <td class="py-3.5 px-4 font-mono font-bold text-teal-800 align-top border-r border-slate-200">
                                    {{ $tp->kode_tp }}
                                </td>
                                <td class="py-3.5 px-4 text-xs text-slate-800 leading-relaxed align-top">
                                    {{ $tp->deskripsi_tp }}
                                </td>
                                <td class="py-3.5 px-4 text-center font-mono font-bold text-slate-700 align-top border-l border-slate-200">
                                    {{ $tp->target_ketercapaian }}
                                </td>
                                <td class="py-3.5 px-4 text-center align-top border-l border-slate-200">
                                    <div class="inline-flex items-center gap-1.5">
                                        <x-button variant="outline" href="{{ route('tujuan-pembelajaran.edit', $tp) }}" class="px-2.5 py-1 text-xs">
                                             Edit
                                        </x-button>
                                        <form action="{{ route('tujuan-pembelajaran.destroy', $tp) }}" method="POST" class="inline" data-confirm="Hapus tujuan pembelajaran {{ $tp->kode_tp }}?" data-confirm-title="Hapus Tujuan Pembelajaran">
                                            @csrf
                                            @method('DELETE')
                                            <x-button type="submit" variant="danger" class="px-2 py-1 text-xs">
                                                Hapus
                                            </x-button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr class="hover:bg-slate-50">
                            <td class="py-3.5 px-4 font-bold text-slate-900 bg-slate-50/80 border-r border-slate-200">
                                <span class="inline-flex px-2 py-0.5 rounded text-xs font-extrabold bg-teal-100 text-teal-800">
                                    {{ $lm->kode_lm }}
                                </span>
                                <p class="text-xs text-slate-700 font-semibold">{{ $lm->judul_materi }}</p>
                            </td>
                            <td colspan="4" class="py-3.5 px-4 text-center text-xs text-slate-400">
                                Belum ada Tujuan Pembelajaran (TP) pada lingkup materi ini.
                                <a href="{{ route('tujuan-pembelajaran.create', ['mata_pelajaran_id' => $selectedMapel->id, 'lingkup_materi_id' => $lm->id]) }}" class="text-teal-600 font-semibold underline ml-1">Tambah TP</a>
                            </td>
                        </tr>
                    @endif
                @empty
                    <x-empty-state colspan="5" message="Belum ada Lingkup Materi untuk mata pelajaran ini. Silakan tambahkan materi baru di atas." />
                @endforelse
            </tbody>
        </table>
    </div>
</x-card>
@else
<!-- Empty State when No Subject Exists -->
<div class="rounded-2xl bg-white p-12 text-center shadow-xs border border-slate-200/80 max-w-2xl mx-auto space-y-4 my-6">
    <div class="h-16 w-16 mx-auto rounded-2xl bg-teal-50 text-teal-600 flex items-center justify-center border border-teal-100 shadow-2xs">
        <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
        </svg>
    </div>
    <div class="space-y-1">
        <h3 class="text-lg font-bold text-slate-900">Belum Ada Mata Pelajaran</h3>
        <p class="text-xs sm:text-sm text-slate-500 max-w-md mx-auto">
            Silakan buat mata pelajaran terlebih dahulu untuk menyusun struktur hirarki Lingkup Materi dan butir indikator Tujuan Pembelajaran.
        </p>
    </div>
    <div class="pt-2 flex flex-wrap items-center justify-center gap-3">
        <a href="{{ route('matapelajaran.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-teal-600 hover:bg-teal-700 text-white font-semibold text-sm shadow-xs transition-colors">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
            <span>Tambah Mata Pelajaran Baru</span>
        </a>
        <a href="{{ route('matapelajaran.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 font-semibold text-sm shadow-xs transition-colors">
            <span>Daftar Mata Pelajaran</span>
        </a>
    </div>
</div>
@endif
@endsection
