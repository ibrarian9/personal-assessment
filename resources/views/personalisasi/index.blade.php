@extends('layouts.app')

@section('title', 'Personalisasi AI Kurikulum Merdeka')

@section('content')
<!-- Page Header -->
<x-page-header title="Engine Personalisasi AI Kurikulum Merdeka" subtitle="Analisis diagnostik capaian TP, pemetaan kebutuhan remedial per butir indikator, dan intervensi modalitas VARK">
    <x-slot:actions>
        <form action="{{ route('personalisasi.generate') }}" method="POST" class="inline">
            @csrf
            <x-button type="submit" variant="primary" onclick="return confirm('Jalankan analisis diagnostik AI untuk seluruh siswa SMA?')">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                </svg>
                <span>Refresh Analisis AI Seluruh Siswa</span>
            </x-button>
        </form>
    </x-slot:actions>
</x-page-header>

<!-- Stats Grid -->
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-6">
    <div class="rounded-2xl bg-white p-6 shadow-xs border border-slate-200/80">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Pemahaman Tinggi</p>
                <h3 class="text-3xl font-extrabold text-slate-900 mt-2">{{ $stats['tinggi'] }} <span class="text-sm font-normal text-slate-400">Siswa</span></h3>
            </div>
            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600 border border-emerald-100">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </div>
        <p class="mt-3 text-xs text-emerald-700 font-medium">Siap untuk tugas pengayaan / Tutor Sebaya</p>
    </div>

    <div class="rounded-2xl bg-white p-6 shadow-xs border border-slate-200/80">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Pemahaman Sedang</p>
                <h3 class="text-3xl font-extrabold text-slate-900 mt-2">{{ $stats['sedang'] }} <span class="text-sm font-normal text-slate-400">Siswa</span></h3>
            </div>
            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-blue-50 text-blue-600 border border-blue-100">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
            </div>
        </div>
        <p class="mt-3 text-xs text-blue-700 font-medium">Sebagian besar TP tuntas, penguatan sub-materi</p>
    </div>

    <div class="rounded-2xl bg-white p-6 shadow-xs border border-slate-200/80">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Perlu Pendampingan</p>
                <h3 class="text-3xl font-extrabold text-rose-600 mt-2">{{ $stats['pendampingan'] }} <span class="text-sm font-normal text-slate-400">Siswa</span></h3>
            </div>
            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-rose-50 text-rose-600 border border-rose-100">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
        </div>
        <p class="mt-3 text-xs text-rose-700 font-medium">Memerlukan program remedial per butir TP</p>
    </div>
</div>

<!-- Card Table -->
<x-card>
    <!-- Filter & Search Bar -->
    <form method="GET" action="{{ route('personalisasi.index') }}" class="flex flex-wrap items-center gap-3">
        <x-search-input name="search" placeholder="Cari nama / NISN..." :value="$search" />

        <x-filter-select name="kategori">
            <option value="">Semua Kategori Pemahaman</option>
            <option value="Tinggi" {{ $kategori == 'Tinggi' ? 'selected' : '' }}>Tinggi (Pengayaan)</option>
            <option value="Sedang" {{ $kategori == 'Sedang' ? 'selected' : '' }}>Sedang (Optimal)</option>
            <option value="Perlu Pendampingan" {{ $kategori == 'Perlu Pendampingan' ? 'selected' : '' }}>Perlu Pendampingan (Remedial)</option>
        </x-filter-select>

        <x-filter-select name="gaya_belajar">
            <option value="">Semua Gaya Belajar</option>
            <option value="Visual" {{ $gayaBelajar == 'Visual' ? 'selected' : '' }}>Visual</option>
            <option value="Auditori" {{ $gayaBelajar == 'Auditori' ? 'selected' : '' }}>Auditori</option>
            <option value="Kinestetik" {{ $gayaBelajar == 'Kinestetik' ? 'selected' : '' }}>Kinestetik</option>
            <option value="Baca/Tulis" {{ $gayaBelajar == 'Baca/Tulis' ? 'selected' : '' }}>Baca/Tulis</option>
        </x-filter-select>

        <x-button type="submit" variant="secondary">
            Filter
        </x-button>

        @if($search || $kategori || $gayaBelajar)
            <x-button variant="outline" href="{{ route('personalisasi.index') }}">
                Reset
            </x-button>
        @endif
    </form>

    <!-- Table Responsive -->
    <x-table>
        <x-thead>
            <x-th>Siswa SMA</x-th>
            <x-th align="center">Gaya Belajar</x-th>
            <x-th align="center">Rata-Rata Sumatif</x-th>
            <x-th align="center">Ketuntasan TP</x-th>
            <x-th align="center">Kategori Diagnostik</x-th>
            <x-th>Rekomendasi Remedial & Pembelajaran</x-th>
            <x-th align="center">Aksi</x-th>
        </x-thead>
        <tbody class="divide-y divide-slate-100">
            @forelse($personalisasis as $item)
                <tr class="hover:bg-slate-50/80 transition-colors">
                    <x-td>
                        <strong class="font-semibold text-slate-900 block">{{ $item->siswa->nama ?? '-' }}</strong>
                        <small class="text-slate-400 font-mono text-xs">{{ $item->siswa->nisn ?? '-' }} &bull; {{ $item->siswa->kelas ?? '-' }}</small>
                    </x-td>
                    <x-td align="center">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold bg-purple-100 text-purple-800">
                            {{ $item->gaya_belajar_rekomendasi ?? $item->siswa->gaya_belajar }}
                        </span>
                    </x-td>
                    <x-td align="center" class="font-mono font-extrabold text-teal-700 text-base">
                        {{ $item->skor_performa }}
                    </x-td>
                    <x-td align="center">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold {{ $item->tp_belum_tuntas_count == 0 ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                            {{ $item->tp_tuntas_count }} Tuntas / {{ $item->tp_belum_tuntas_count }} Remedial
                        </span>
                    </x-td>
                    <x-td align="center">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold {{ $item->kategori_pemahaman === 'Tinggi' ? 'bg-emerald-100 text-emerald-800' : ($item->kategori_pemahaman === 'Sedang' ? 'bg-blue-100 text-blue-800' : 'bg-rose-100 text-rose-800') }}">
                            {{ $item->kategori_pemahaman }}
                        </span>
                    </x-td>
                    <x-td class="max-w-xs text-xs text-slate-600 leading-relaxed">
                        {{ \Illuminate\Support\Str::limit($item->rekomendasi_materi, 90) }}
                    </x-td>
                    <x-td align="center">
                        <x-button variant="primary" href="{{ route('personalisasi.show', $item->siswa_id) }}" class="px-3 py-1.5 text-xs">
                            <span>Diagnostik AI</span>
                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" /></svg>
                        </x-button>
                    </x-td>
                </tr>
            @empty
                <x-empty-state colspan="7" message="Belum ada data personalisasi. Silakan klik tombol Refresh Analisis AI di atas." />
            @endforelse
        </tbody>
    </x-table>

    <!-- Pagination -->
    <div class="pt-2">
        {{ $personalisasis->links() }}
    </div>
</x-card>
@endsection
