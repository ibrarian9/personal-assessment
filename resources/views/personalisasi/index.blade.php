@extends('layouts.app')

@section('title', 'Personalisasi AI Kurikulum Merdeka')

@section('content')
<!-- Page Header -->
<x-page-header title="Engine Personalisasi AI Kurikulum Merdeka" subtitle="Analisis diagnostik capaian TP, pemetaan kebutuhan remedial per butir indikator, dan intervensi modalitas VARK">
    <x-slot:actions>
        <button type="button" onclick="openBatchAiConfirmModal()" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold rounded-xl bg-teal-600 hover:bg-teal-700 text-white shadow-xs transition-colors cursor-pointer" id="btn-batch-ai">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
            </svg>
            <span id="btn-batch-ai-text">Refresh Analisis AI Seluruh Siswa</span>
        </button>
    </x-slot:actions>
</x-page-header>

<!-- Stats Grid -->
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-6">
    <div class="rounded-2xl bg-white p-6 shadow-xs border border-slate-200/80">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Pemahaman Tinggi</p>
                <h3 class="text-3xl font-extrabold text-slate-900 mt-2" id="stat-tinggi">{{ $stats['tinggi'] }} <span class="text-sm font-normal text-slate-400">Siswa</span></h3>
            </div>
            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600 border border-emerald-100">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </div>
        <p class="mt-3 text-xs text-emerald-700 font-medium">Siap untuk tugas pengayaan dan tutor sebaya</p>
    </div>

    <div class="rounded-2xl bg-white p-6 shadow-xs border border-slate-200/80">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Pemahaman Sedang</p>
                <h3 class="text-3xl font-extrabold text-slate-900 mt-2" id="stat-sedang">{{ $stats['sedang'] }} <span class="text-sm font-normal text-slate-400">Siswa</span></h3>
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
                <h3 class="text-3xl font-extrabold text-rose-600 mt-2" id="stat-pendampingan">{{ $stats['pendampingan'] }} <span class="text-sm font-normal text-slate-400">Siswa</span></h3>
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
        <x-search-input name="search" placeholder="Cari nama atau NISN..." :value="$search" />

        <x-filter-select name="kategori">
            <option value="">Semua Kategori Pemahaman</option>
            <option value="Tinggi" {{ $kategori == 'Tinggi' ? 'selected' : '' }}>Kategori Tinggi: Pengayaan</option>
            <option value="Sedang" {{ $kategori == 'Sedang' ? 'selected' : '' }}>Kategori Sedang: Optimal</option>
            <option value="Perlu Pendampingan" {{ $kategori == 'Perlu Pendampingan' ? 'selected' : '' }}>Kategori Perlu Pendampingan: Remedial</option>
        </x-filter-select>

        <x-filter-select name="gaya_belajar">
            <option value="">Semua Gaya Belajar</option>
            <option value="Visual" {{ $gayaBelajar == 'Visual' ? 'selected' : '' }}>Visual</option>
            <option value="Auditori" {{ $gayaBelajar == 'Auditori' ? 'selected' : '' }}>Auditori</option>
            <option value="Kinestetik" {{ $gayaBelajar == 'Kinestetik' ? 'selected' : '' }}>Kinestetik</option>
            <option value="Baca/Tulis" {{ $gayaBelajar == 'Baca/Tulis' ? 'selected' : '' }}>Baca dan Tulis</option>
        </x-filter-select>

        <x-filter-select name="per_page">
            <option value="10" {{ ($perPageRaw ?? '25') == '10' ? 'selected' : '' }}>10 Siswa per Slide</option>
            <option value="25" {{ ($perPageRaw ?? '25') == '25' ? 'selected' : '' }}>25 Siswa per Slide</option>
            <option value="50" {{ ($perPageRaw ?? '25') == '50' ? 'selected' : '' }}>50 Siswa per Slide</option>
            <option value="all" {{ ($perPageRaw ?? '25') == 'all' ? 'selected' : '' }}>Tampilkan Semua Siswa</option>
        </x-filter-select>

        <x-button type="submit" variant="secondary">
            Filter
        </x-button>

        @if($search || $kategori || $gayaBelajar || ($perPageRaw ?? '25') != '25')
            <x-button variant="outline" href="{{ route('personalisasi.index') }}">
                Reset
            </x-button>
        @endif
    </form>

    <!-- Banner Progress Analisis Paralel -->
    <div id="parallel-progress-banner" class="hidden my-4 p-4 rounded-2xl bg-indigo-50 border border-indigo-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3 transition-all duration-300">
        <div class="flex items-center gap-3">
            <div id="parallel-progress-icon-container" class="h-10 w-10 rounded-xl bg-indigo-600 text-white flex items-center justify-center shrink-0 shadow-xs">
                <svg id="parallel-progress-spinner" class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>
            <div>
                <h4 id="parallel-progress-title" class="text-sm font-bold text-indigo-950">Analisis AI Paralel Sedang Berjalan</h4>
                <p class="text-xs text-indigo-700 mt-0.5" id="parallel-progress-text">Memproses 0 dari {{ count($allStudentsQueue) }} siswa (0%)...</p>
            </div>
        </div>
        <div class="w-full sm:w-56 bg-indigo-200/70 rounded-full h-2.5 overflow-hidden">
            <div id="parallel-progress-bar" class="bg-indigo-600 h-2.5 rounded-full transition-all duration-300" style="width: 0%"></div>
        </div>
    </div>

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
        <tbody class="divide-y divide-slate-100" id="personalisasi-table-body">
            @forelse($personalisasis as $item)
                @php
                    $studentDataJson = [
                        'siswa_id' => $item->siswa_id,
                        'nama' => $item->siswa->nama ?? '-',
                        'nisn' => $item->siswa->nisn ?? '-',
                        'kelas' => $item->siswa->kelas ?? '-',
                        'skor_performa' => (float)$item->skor_performa,
                        'kategori_pemahaman' => $item->kategori_pemahaman,
                        'tp_tuntas_count' => (int)$item->tp_tuntas_count,
                        'tp_belum_tuntas_count' => (int)$item->tp_belum_tuntas_count,
                        'gaya_belajar' => str_replace(['Baca/Tulis', '/'], ['Baca dan Tulis', ' dan '], $item->gaya_belajar_rekomendasi ?? $item->siswa->gaya_belajar),
                        'catatan_guru' => str_replace(['Baca/Tulis', '/'], ['Baca dan Tulis', ' atau '], $item->catatan_guru ?? 'Belum ada catatan diagnostik guru.'),
                        'rekomendasi_materi' => str_replace(['Baca/Tulis', '/'], ['Baca dan Tulis', ' atau '], $item->rekomendasi_materi ?? '-'),
                        'rekomendasi_materi_full' => str_replace(['Baca/Tulis', '/'], ['Baca dan Tulis', ' atau '], $item->rekomendasi_materi ?? '-'),
                        'rekomendasi_aktivitas' => str_replace(['Baca/Tulis', '/'], ['Baca dan Tulis', ' atau '], $item->rekomendasi_aktivitas ?? '-'),
                        'sumber_analisis' => $item->sumber_analisis ?? 'Heuristik',
                        'keterangan_sumber' => $item->keterangan_sumber ?? 'Diagnostik Hasil Asesmen',
                        'updated_at' => $item->updated_at ? $item->updated_at->timezone('Asia/Jakarta')->format('d M Y, H:i') : 'Belum pernah',
                        'detail_url' => route('personalisasi.show', $item->siswa_id),
                    ];
                @endphp
                <tr id="row-siswa-{{ $item->siswa_id }}" data-siswa-id="{{ $item->siswa_id }}" data-analyze-url="{{ route('personalisasi.ai-analyze', $item->siswa_id) }}" data-student='@json($studentDataJson)' class="hover:bg-slate-50/80 transition-all duration-300">
                    <x-td>
                        <strong class="font-semibold text-slate-900 block">{{ $item->siswa->nama ?? '-' }}</strong>
                        <small class="text-slate-500 font-mono text-xs">{{ $item->siswa->nisn ?? '-' }} &bull; {{ $item->siswa->kelas ?? '-' }}</small>
                    </x-td>
                    <x-td align="center">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold bg-purple-100 text-purple-800" id="badge-gaya-{{ $item->siswa_id }}">
                            {{ str_replace(['Baca/Tulis', '/'], ['Baca dan Tulis', ' dan '], $item->gaya_belajar_rekomendasi ?? $item->siswa->gaya_belajar) }}
                        </span>
                    </x-td>
                    <x-td align="center" class="font-mono font-extrabold text-teal-700 text-base" id="cell-skor-{{ $item->siswa_id }}">
                        {{ $item->skor_performa }}
                    </x-td>
                    <x-td align="center" id="cell-ketuntasan-{{ $item->siswa_id }}">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold {{ $item->tp_belum_tuntas_count == 0 ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                            {{ $item->tp_tuntas_count }} Tuntas, {{ $item->tp_belum_tuntas_count }} Remedial
                        </span>
                    </x-td>
                    <x-td align="center" id="cell-kategori-{{ $item->siswa_id }}">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold {{ $item->kategori_pemahaman === 'Tinggi' ? 'bg-emerald-100 text-emerald-800' : ($item->kategori_pemahaman === 'Sedang' ? 'bg-blue-100 text-blue-800' : 'bg-rose-100 text-rose-800') }}">
                            {{ $item->kategori_pemahaman }}
                        </span>
                    </x-td>
                    <x-td class="max-w-xs text-xs text-slate-600 leading-relaxed" id="cell-rekomendasi-{{ $item->siswa_id }}">
                        {{ \Illuminate\Support\Str::limit(str_replace(['Baca/Tulis', '/TP', 'per/TP', 'per-TP', ' / ', '/'], ['Baca dan Tulis', ' TP', 'per TP', 'per TP', ' atau ', ' atau '], $item->rekomendasi_materi), 90) }}
                    </x-td>
                    <x-td align="center" id="cell-aksi-{{ $item->siswa_id }}">
                        <div class="flex flex-col items-center justify-center gap-1.5">
                            <div class="flex items-center justify-center gap-1">
                                <button type="button" onclick="openStudentDetailModal({{ $item->siswa_id }})" class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold rounded-xl bg-teal-600 hover:bg-teal-700 text-white shadow-xs transition-colors cursor-pointer" title="Lihat Ringkasan Hasil AI (Tanpa Meninggalkan Halaman)">
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    <span>Lihat Detail</span>
                                </button>
                                <a href="{{ route('personalisasi.show', $item->siswa_id) }}" target="_blank" title="Buka Profil Lengkap di Tab Baru (Analisis Lain Tetap Berjalan)" class="p-1.5 rounded-xl text-slate-400 hover:text-teal-600 hover:bg-teal-50 transition-colors">
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                </a>
                            </div>
                            <div id="updated-at-{{ $item->siswa_id }}" class="flex items-center justify-center gap-1 text-[11px] font-semibold text-slate-700 font-mono tracking-tight" title="Waktu Analisis Terakhir">
                                <span class="inline-block h-1.5 w-1.5 rounded-full {{ $item->updated_at ? 'bg-emerald-500' : 'bg-slate-300' }}"></span>
                                <span>{{ $item->updated_at ? $item->updated_at->timezone('Asia/Jakarta')->format('d M H:i') : 'Belum ada' }}</span>
                            </div>
                        </div>
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

<!-- Modal Konfirmasi Refresh Analisis AI (MicroModal) -->
<x-modal id="modal-confirm-batch-ai" title="Konfirmasi Analisis AI Seluruh Siswa" size="md">
    <x-slot:icon>
        <svg class="h-6 w-6 text-teal-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 00-2.456 2.456z" />
        </svg>
    </x-slot:icon>
    <x-slot:subtitle>
        Evaluasi diagnostik diferensiasi Kurikulum Merdeka berbasis LLM
    </x-slot:subtitle>

    <div class="space-y-3">
        <p class="text-sm text-slate-600 leading-relaxed">
            Sistem akan menjalankan analisis diagnostik kecerdasan buatan (AI) untuk seluruh <strong>{{ count($allStudentsQueue) }} siswa</strong> (mencakup semua slide/halaman) secara <strong>paralel</strong>.
        </p>
        <div class="rounded-2xl bg-teal-50/70 border border-teal-100 p-3.5 text-xs text-teal-800 space-y-1.5">
            <p class="font-semibold flex items-center gap-1.5 text-teal-900">
                <svg class="h-4 w-4 text-teal-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Proses Terdistribusi & Non-Blocking:
            </p>
            <p>1. Seluruh siswa pada database akan diproses tuntas oleh antrean paralel.</p>
            <p>2. Anda dapat langsung membuka tombol <strong>Lihat Detail</strong> pada siswa yang telah selesai tanpa menghentikan atau mengganggu antrean loading siswa lain.</p>
        </div>
    </div>

    <x-slot:footer>
        <button type="button" class="px-4 py-2 text-sm font-semibold rounded-xl text-slate-600 hover:bg-slate-100 transition-colors" data-micromodal-close>
            Batal
        </button>
        <button type="button" onclick="startParallelBatchAnalysis()" class="inline-flex items-center gap-1.5 px-5 py-2 text-sm font-semibold rounded-xl bg-teal-600 hover:bg-teal-700 text-white shadow-xs transition-colors cursor-pointer">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z" />
            </svg>
            <span>Mulai Analisis Seluruh Siswa ({{ count($allStudentsQueue) }})</span>
        </button>
    </x-slot:footer>
</x-modal>

<!-- Modal Quick View Detail Diagnostik Siswa -->
<x-modal id="modal-detail-siswa" title="Detail Diagnostik Personalisasi Siswa" size="lg">
    <x-slot:icon>
        <svg class="h-6 w-6 text-teal-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
        </svg>
    </x-slot:icon>
    <x-slot:subtitle>
        <span id="md-siswa-nama" class="font-bold text-slate-800">Nama Siswa</span> &bull; <span id="md-siswa-meta" class="text-slate-500 font-mono">NISN: - | Kelas: -</span>
    </x-slot:subtitle>

    <div class="space-y-4">
        <!-- 4 Badges Ringkasan -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5">
            <div class="p-3 rounded-2xl bg-slate-50 border border-slate-100 text-center">
                <span class="text-[10px] font-semibold uppercase tracking-wider text-slate-400 block">Skor Performa</span>
                <span id="md-skor" class="text-xl font-extrabold text-teal-700 font-mono mt-0.5 block">0</span>
            </div>
            <div class="p-3 rounded-2xl bg-slate-50 border border-slate-100 text-center">
                <span class="text-[10px] font-semibold uppercase tracking-wider text-slate-400 block">Kategori</span>
                <span id="md-kategori" class="text-xs font-bold text-slate-800 mt-1 inline-block px-2.5 py-0.5 rounded-md">-</span>
            </div>
            <div class="p-3 rounded-2xl bg-slate-50 border border-slate-100 text-center">
                <span class="text-[10px] font-semibold uppercase tracking-wider text-slate-400 block">Ketuntasan TP</span>
                <span id="md-ketuntasan" class="text-xs font-bold text-slate-800 mt-1 inline-block px-2.5 py-0.5 rounded-md">-</span>
            </div>
            <div class="p-3 rounded-2xl bg-slate-50 border border-slate-100 text-center">
                <span class="text-[10px] font-semibold uppercase tracking-wider text-slate-400 block">Gaya Belajar</span>
                <span id="md-gaya" class="text-xs font-bold text-purple-700 bg-purple-50 border border-purple-100 mt-1 inline-block px-2.5 py-0.5 rounded-md">-</span>
            </div>
        </div>

        <!-- Rekomendasi & Catatan Guru (AI) -->
        <div class="rounded-2xl bg-teal-50/60 border border-teal-100/90 p-4 space-y-1.5">
            <h4 class="text-xs font-bold text-teal-950 uppercase tracking-wider flex items-center gap-1.5">
                <svg class="h-4 w-4 text-teal-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 00-2.456 2.456z"/></svg>
                Catatan Guru dan Rekomendasi Diagnostik (AI)
            </h4>
            <p id="md-catatan-guru" class="text-xs text-slate-700 leading-relaxed whitespace-pre-line"></p>
        </div>

        <!-- Rekomendasi Materi & Aktivitas -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
            <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-1">
                <h5 class="font-bold text-slate-800 flex items-center gap-1">
                    <svg class="h-3.5 w-3.5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    Fokus Materi Remedial dan Pengayaan
                </h5>
                <p id="md-rekomendasi-materi" class="text-slate-600 leading-relaxed"></p>
            </div>
            <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-1">
                <h5 class="font-bold text-slate-800 flex items-center gap-1">
                    <svg class="h-3.5 w-3.5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    Aktivitas Pembelajaran Diferensiasi
                </h5>
                <p id="md-rekomendasi-aktivitas" class="text-slate-600 leading-relaxed whitespace-pre-line"></p>
            </div>
        </div>

        <!-- Meta Waktu -->
        <div class="flex items-center justify-between text-[11px] text-slate-400 font-mono pt-1 border-t border-slate-100">
            <span id="md-sumber">Sumber: -</span>
            <span id="md-updated-at">Terakhir Diperbarui: -</span>
        </div>
    </div>

    <x-slot:footer>
        <button type="button" class="px-4 py-2 text-sm font-semibold rounded-xl text-slate-600 hover:bg-slate-100 transition-colors" data-micromodal-close>
            Tutup
        </button>
        <a id="md-btn-full" href="#" target="_blank" class="inline-flex items-center gap-1.5 px-5 py-2 text-sm font-semibold rounded-xl bg-teal-600 hover:bg-teal-700 text-white shadow-xs transition-colors">
            <span>Buka Profil Lengkap di Tab Baru</span>
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
        </a>
    </x-slot:footer>
</x-modal>

<script>
window.studentResultsCache = {};
const allStudentsQueue = @json($allStudentsQueue);

function openBatchAiConfirmModal() {
    if (window.MicroModal) {
        window.MicroModal.show('modal-confirm-batch-ai');
    }
}

function openStudentDetailModal(siswaId) {
    let data = window.studentResultsCache[siswaId];
    if (!data) {
        const row = document.getElementById('row-siswa-' + siswaId);
        if (row && row.dataset.student) {
            try {
                data = JSON.parse(row.dataset.student);
            } catch (e) {
                console.error(e);
            }
        }
    }

    if (!data) {
        if (window.showAlertModal) {
            window.showAlertModal('Informasi', 'Data diagnostik siswa ini belum tersedia di cache. Silakan buka halaman penuh.', 'info');
        }
        return;
    }

    document.getElementById('md-siswa-nama').textContent = data.nama || 'Siswa';
    document.getElementById('md-siswa-meta').textContent = `NISN: ${data.nisn || '-'} | Kelas: ${data.kelas || '-'}`;
    document.getElementById('md-skor').textContent = data.skor_performa || '0';

    const katEl = document.getElementById('md-kategori');
    if (katEl) {
        katEl.textContent = data.kategori_pemahaman || '-';
        katEl.className = 'text-xs font-bold mt-1 inline-block px-2.5 py-0.5 rounded-md ' + (
            data.kategori_pemahaman === 'Tinggi' ? 'bg-emerald-100 text-emerald-800' :
            (data.kategori_pemahaman === 'Sedang' ? 'bg-blue-100 text-blue-800' : 'bg-rose-100 text-rose-800')
        );
    }

    const ketEl = document.getElementById('md-ketuntasan');
    if (ketEl) {
        ketEl.textContent = `${data.tp_tuntas_count || 0} Tuntas, ${data.tp_belum_tuntas_count || 0} Remedial`;
        ketEl.className = 'text-xs font-bold mt-1 inline-block px-2.5 py-0.5 rounded-md ' + (
            (data.tp_belum_tuntas_count || 0) === 0 ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800'
        );
    }

    const gayaEl = document.getElementById('md-gaya');
    if (gayaEl) {
        gayaEl.textContent = data.gaya_belajar || 'Visual';
    }

    const catGuruEl = document.getElementById('md-catatan-guru');
    if (catGuruEl) {
        catGuruEl.textContent = data.catatan_guru || 'Belum ada catatan guru.';
    }

    const rekMateriEl = document.getElementById('md-rekomendasi-materi');
    if (rekMateriEl) {
        rekMateriEl.textContent = data.rekomendasi_materi_full || data.rekomendasi_materi || '-';
    }

    const rekAktEl = document.getElementById('md-rekomendasi-aktivitas');
    if (rekAktEl) {
        rekAktEl.textContent = data.rekomendasi_aktivitas || '-';
    }

    const sumberEl = document.getElementById('md-sumber');
    if (sumberEl) {
        sumberEl.textContent = `Sumber: ${data.sumber_analisis || 'AI Cloud'} (${data.keterangan_sumber || 'Diagnostik'})`;
    }

    const updatedEl = document.getElementById('md-updated-at');
    if (updatedEl) {
        updatedEl.textContent = `Terakhir Diperbarui: ${data.updated_at || '-'}`;
    }

    const btnFull = document.getElementById('md-btn-full');
    if (btnFull) {
        btnFull.href = data.detail_url || `/personalisasi/${siswaId}`;
    }

    if (window.MicroModal) {
        window.MicroModal.show('modal-detail-siswa');
    }
}

async function startParallelBatchAnalysis() {
    if (window.MicroModal) {
        window.MicroModal.close('modal-confirm-batch-ai');
    }

    const queue = allStudentsQueue;
    if (!queue || queue.length === 0) {
        if (window.showAlertModal) {
            window.showAlertModal('Informasi', 'Tidak ada data siswa untuk dianalisis.', 'info');
        }
        return;
    }

    const btnBatch = document.getElementById('btn-batch-ai');
    const btnBatchText = document.getElementById('btn-batch-ai-text');
    const banner = document.getElementById('parallel-progress-banner');
    const title = document.getElementById('parallel-progress-title');
    const progressText = document.getElementById('parallel-progress-text');
    const progressBar = document.getElementById('parallel-progress-bar');
    const iconContainer = document.getElementById('parallel-progress-icon-container');

    // Aktifkan progress banner
    if (banner) {
        banner.className = 'my-4 p-4 rounded-2xl bg-indigo-50 border border-indigo-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3 transition-all duration-300';
    }
    if (iconContainer) {
        iconContainer.className = 'h-10 w-10 rounded-xl bg-indigo-600 text-white flex items-center justify-center shrink-0 shadow-xs';
        iconContainer.innerHTML = `
            <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        `;
    }
    if (title) title.textContent = 'Analisis AI Paralel Sedang Berjalan';
    if (btnBatch) {
        btnBatch.disabled = true;
        btnBatch.classList.add('opacity-75', 'cursor-not-allowed');
    }
    if (btnBatchText) {
        btnBatchText.textContent = 'Memproses Seluruh Siswa...';
    }

    // Set indikator antrean loading pada baris yang terlihat di slide saat ini
    queue.forEach(item => {
        const siswaId = item.siswa_id;
        const row = document.getElementById(`row-siswa-${siswaId}`);
        const cellAksi = document.getElementById(`cell-aksi-${siswaId}`);

        if (row) {
            row.classList.add('bg-indigo-50/30');
        }

        if (cellAksi) {
            cellAksi.innerHTML = `
                <div class="flex flex-col items-center justify-center gap-1">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl text-xs font-semibold bg-indigo-100 text-indigo-700 border border-indigo-200">
                        <svg class="animate-spin h-3.5 w-3.5 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span>Menganalisis...</span>
                    </span>
                    <span class="text-[10px] font-medium text-indigo-600 font-mono">Sedang proses</span>
                </div>
            `;
        }
    });

    const total = queue.length;
    let completedCount = 0;
    const concurrency = 3; // Menjalankan maksimal 3 siswa bersamaan agar seimbang dan tidak membebani kuota API
    let currentIndex = 0;

    function updateProgressBar() {
        const pct = Math.round((completedCount / total) * 100);
        if (progressText) {
            progressText.textContent = `Memproses ${completedCount} dari ${total} siswa (${pct}%)...`;
        }
        if (progressBar) {
            progressBar.style.width = `${pct}%`;
        }
    }

    updateProgressBar();

    async function analyzeStudent(item) {
        const siswaId = item.siswa_id;
        const url = item.analyze_url;

        const row = document.getElementById(`row-siswa-${siswaId}`);
        const cellSkor = document.getElementById(`cell-skor-${siswaId}`);
        const cellKetuntasan = document.getElementById(`cell-ketuntasan-${siswaId}`);
        const cellKategori = document.getElementById(`cell-kategori-${siswaId}`);
        const cellRekomendasi = document.getElementById(`cell-rekomendasi-${siswaId}`);
        const cellAksi = document.getElementById(`cell-aksi-${siswaId}`);
        const badgeGaya = document.getElementById(`badge-gaya-${siswaId}`);

        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
            const res = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (!res.ok) {
                throw new Error(`HTTP ${res.status}`);
            }

            const json = await res.json();
            const d = json.data;

            if (d) {
                // Simpan ke cache agar modal detail dapat dibuka langsung kapan saja
                window.studentResultsCache[siswaId] = d;

                // Perbarui tampilan DOM jika siswa berada di slide/halaman saat ini
                if (row) {
                    row.dataset.student = JSON.stringify(d);

                    if (cellSkor) cellSkor.textContent = d.skor_performa;

                    if (cellKetuntasan) {
                        const isAllTuntas = d.tp_belum_tuntas_count === 0;
                        const badgeClass = isAllTuntas ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800';
                        cellKetuntasan.innerHTML = `<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold ${badgeClass}">${d.tp_tuntas_count} Tuntas, ${d.tp_belum_tuntas_count} Remedial</span>`;
                    }

                    if (cellKategori) {
                        const catClass = d.kategori_pemahaman === 'Tinggi' 
                            ? 'bg-emerald-100 text-emerald-800' 
                            : (d.kategori_pemahaman === 'Sedang' ? 'bg-blue-100 text-blue-800' : 'bg-rose-100 text-rose-800');
                        cellKategori.innerHTML = `<span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold ${catClass}">${d.kategori_pemahaman}</span>`;
                    }

                    if (cellRekomendasi) cellRekomendasi.textContent = d.rekomendasi_materi;

                    if (badgeGaya && d.gaya_belajar) badgeGaya.textContent = d.gaya_belajar;

                    if (cellAksi) {
                        cellAksi.innerHTML = `
                            <div class="flex flex-col items-center justify-center gap-1.5">
                                <div class="flex items-center justify-center gap-1">
                                    <button type="button" onclick="openStudentDetailModal(${siswaId})" class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold rounded-xl bg-teal-600 hover:bg-teal-700 text-white shadow-xs transition-colors cursor-pointer" title="Lihat Ringkasan Hasil AI (Tanpa Meninggalkan Halaman)">
                                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        <span>Lihat Detail</span>
                                    </button>
                                    <a href="${d.detail_url}" target="_blank" title="Buka Profil Lengkap di Tab Baru (Analisis Lain Tetap Berjalan)" class="p-1.5 rounded-xl text-slate-400 hover:text-teal-600 hover:bg-teal-50 transition-colors">
                                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                    </a>
                                </div>
                                <div id="updated-at-${siswaId}" class="flex items-center justify-center gap-1 text-[11px] font-semibold text-emerald-700 font-mono tracking-tight" title="Waktu Analisis Terakhir">
                                    <span class="inline-block h-1.5 w-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                    <span>Baru saja</span>
                                </div>
                            </div>
                        `;
                    }

                    row.classList.remove('bg-indigo-50/30');
                    row.classList.add('bg-emerald-50/70');
                    setTimeout(() => {
                        row.classList.remove('bg-emerald-50/70');
                    }, 2500);
                }
            }
        } catch (error) {
            console.error('Analisis siswa ID ' + siswaId + ' gagal:', error);
            if (cellAksi) {
                cellAksi.innerHTML = `
                    <div class="flex flex-col items-center justify-center gap-1.5">
                        <div class="flex items-center justify-center gap-1">
                            <button type="button" onclick="openStudentDetailModal(${siswaId})" class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold rounded-xl bg-slate-600 hover:bg-slate-700 text-white shadow-xs transition-colors">
                                Lihat Data
                            </button>
                            <a href="/personalisasi/${siswaId}" target="_blank" class="p-1.5 rounded-xl text-slate-400 hover:text-rose-600">
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                            </a>
                        </div>
                        <div id="updated-at-${siswaId}" class="flex items-center justify-center gap-1 text-[11px] font-semibold text-rose-600 font-mono">
                            <span class="inline-block h-1.5 w-1.5 rounded-full bg-rose-500"></span>
                            <span>Gagal</span>
                        </div>
                    </div>
                `;
            }
            if (row) row.classList.remove('bg-indigo-50/30');
        } finally {
            completedCount++;
            updateProgressBar();
        }
    }

    // Pool pekerja paralel dengan konkurensi terkontrol
    async function worker() {
        while (currentIndex < total) {
            const item = queue[currentIndex++];
            await analyzeStudent(item);
        }
    }

    const workers = [];
    const activeWorkers = Math.min(concurrency, total);
    for (let i = 0; i < activeWorkers; i++) {
        workers.push(worker());
    }

    await Promise.all(workers);

    // Semua siswa (seluruh slide) selesai dianalisis
    if (banner) {
        banner.className = 'my-4 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3 transition-all duration-300';
    }
    if (iconContainer) {
        iconContainer.className = 'h-10 w-10 rounded-xl bg-emerald-600 text-white flex items-center justify-center shrink-0 shadow-xs';
        iconContainer.innerHTML = '<svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>';
    }
    if (title) title.textContent = 'Analisis AI Seluruh Siswa Selesai!';
    if (progressText) progressText.textContent = `Seluruh ${total} siswa pada semua slide berhasil dianalisis dengan AI dan data telah disinkronkan.`;
    if (progressBar) {
        progressBar.className = 'bg-emerald-600 h-2.5 rounded-full transition-all duration-300';
        progressBar.style.width = '100%';
    }

    if (btnBatch) {
        btnBatch.disabled = false;
        btnBatch.classList.remove('opacity-75', 'cursor-not-allowed');
    }
    if (btnBatchText) {
        btnBatchText.textContent = 'Refresh Analisis AI Seluruh Siswa';
    }

    if (window.showAlertModal) {
        window.showAlertModal('Analisis AI Selesai', `Seluruh ${total} siswa pada sistem (mencakup semua slide/halaman) berhasil dievaluasi dengan AI. Data diagnostik terbaru telah tersimpan.`, 'success');
    }
}
</script>
@endsection
