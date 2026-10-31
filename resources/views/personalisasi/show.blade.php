@extends('layouts.app')

@section('title', 'Diagnostik Personalisasi - ' . $siswa->nama)

@section('content')
@php
    // Helper pembersih tanda garis miring (slash) dan em dash
    $cleanText = function($text) {
        if (!$text) return '';
        $text = str_replace(['Baca/Tulis', 'baca/tulis'], ['Baca dan Tulis', 'baca dan tulis'], $text);
        $text = str_replace(['/TP', 'per/TP', 'per-TP'], [' TP', 'per TP', 'per TP'], $text);
        $text = str_replace([' / ', '/'], [' atau ', ' atau '], $text);
        $text = str_replace(['—', '–'], [':', '-'], $text);
        return $text;
    };
    $gayaBelajarBersih = $cleanText($siswa->gaya_belajar);

    $badgeSumber = $personalisasi->sumber_analisis_badge ?? [
        'nama' => 'Fallback Heuristik',
        'tipe' => 'fallback',
        'bg' => 'bg-amber-50 text-amber-900 border-amber-200',
        'badge_header' => 'bg-amber-100 text-amber-900 border-amber-300',
        'dot' => 'bg-amber-500',
        'model' => 'PLA Merdeka Engine',
    ];
@endphp

<!-- Page Header (Standardized Header) -->
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-2 border-b border-slate-200/80">
    <div>
        <h2 class="text-2xl font-extrabold tracking-tight text-slate-900">Profil Personalisasi & Diagnostik TP</h2>
        <p class="text-sm text-slate-500 mt-1">Rekomendasi pembelajaran adaptif berbasis capaian Tujuan Pembelajaran dan modalitas belajar untuk {{ $siswa->nama }} &bull; Kelas {{ $siswa->kelas }}, NISN {{ $siswa->nisn }}</p>
    </div>
    <div class="flex items-center gap-3">
        <form action="{{ route('personalisasi.ai-analyze', $siswa) }}" method="POST" class="inline" onsubmit="window.submitWithAiLoading(event, this, 'Menganalisis: {{ addslashes($siswa->nama) }}');">
            @csrf
            <button type="submit" id="ai-analyze-btn" class="inline-flex items-center justify-center gap-1.5 px-4 py-2 text-sm font-semibold rounded-xl bg-teal-600 text-white hover:bg-teal-700 shadow-xs transition-all focus:ring-2 focus:ring-teal-500/30">
                <svg id="ai-analyze-spinner" class="animate-spin h-4 w-4 text-white hidden" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                <svg id="ai-analyze-icon" class="h-4 w-4" aria-hidden="true" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 00-2.456 2.456zM16.894 20.567L16.5 21.75l-.394-1.183a2.25 2.25 0 00-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 001.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 001.423 1.423l1.183.394-1.183.394a2.25 2.25 0 00-1.423 1.423z" />
                </svg>
                <span id="ai-analyze-text">Analisis Ulang dengan AI</span>
            </button>
        </form>
        <a href="{{ route('personalisasi.index') }}" class="inline-flex items-center justify-center gap-1.5 px-4 py-2 text-sm font-semibold rounded-xl border border-slate-300 text-slate-700 bg-white hover:bg-slate-50 shadow-xs transition-colors focus:ring-2 focus:ring-slate-300">
            <svg class="h-4 w-4" aria-hidden="true" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" /></svg>
            <span>Kembali</span>
        </a>
    </div>
</div>

<!-- Status Sumber Analisis Banner (Clean & Harmonious) -->
<div class="rounded-2xl bg-white p-4 sm:p-5 shadow-xs border border-slate-200/80">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div class="flex items-start sm:items-center gap-3">
            <div class="p-2 rounded-xl {{ ($isFromDatabase ?? true) ? 'bg-slate-100 text-slate-700 border border-slate-200' : 'bg-teal-100 text-teal-700 border border-teal-200' }} shrink-0 shadow-2xs">
                @if(($isFromDatabase ?? true))
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4" />
                    </svg>
                @else
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z" />
                    </svg>
                @endif
            </div>
            <div>
                <div class="flex flex-wrap items-center gap-2">
                    <span class="text-xs font-semibold uppercase tracking-wider text-slate-500">Status Sumber Analisis:</span>
                    @if(($isFromDatabase ?? true))
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-md text-xs font-bold bg-slate-100 text-slate-800 border border-slate-200">
                            <span class="h-1.5 w-1.5 rounded-full bg-slate-500"></span>
                            Dimuat dari Database
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-md text-xs font-bold bg-teal-50 text-teal-800 border border-teal-200">
                            <span class="h-1.5 w-1.5 rounded-full bg-teal-600"></span>
                            Baru Saja Dihasilkan
                        </span>
                    @endif

                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-md text-xs font-bold {{ $badgeSumber['bg'] }} border">
                        <span class="h-1.5 w-1.5 rounded-full {{ $badgeSumber['dot'] }}"></span>
                        {{ $badgeSumber['nama'] }}
                    </span>

                    @if(!empty($badgeSumber['model']))
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-md text-xs font-mono font-semibold bg-white text-slate-800 border border-slate-300 shadow-2xs">
                            <svg class="w-3.5 h-3.5 text-teal-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="10" rx="2"/><circle cx="12" cy="5" r="2"/><path d="M12 7v4"/></svg>
                            Model: {{ $badgeSumber['model'] }}
                        </span>
                    @endif
                </div>
                <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                    @if(($isFromDatabase ?? true))
                        Data rekomendasi ini dimuat dari arsip database (tersimpan pada {{ $personalisasi->updated_at ? $personalisasi->updated_at->isoFormat('D MMMM Y, HH:mm') : 'Baru' }}).
                    @else
                        Data rekomendasi ini baru saja diproses dan tersimpan otomatis ke database.
                    @endif
                    @if($personalisasi->keterangan_sumber)
                        Keterangan: {{ $personalisasi->keterangan_sumber }}.
                    @endif
                </p>
            </div>
        </div>
        <div class="shrink-0 text-xs text-slate-500 flex items-center gap-2 sm:self-center">
            <span class="font-mono bg-slate-50 px-2.5 py-1 rounded-md border border-slate-200">ID #{{ $personalisasi->id }}</span>
        </div>
    </div>
</div>

<!-- Ringkasan Profil & Status Capaian Siswa -->
<div class="rounded-2xl bg-white p-6 shadow-xs border border-slate-200/80 space-y-6">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6 pb-4 border-b border-slate-100">
        <!-- Siswa Identity Details -->
        <div class="space-y-2">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Identitas Siswa</span>
            <div class="flex flex-wrap items-baseline gap-3">
                <h3 class="text-2xl font-bold text-slate-900">{{ $siswa->nama }}</h3>
                <span class="text-sm font-semibold text-slate-600">Kelas {{ $siswa->kelas }}</span>
                <span class="text-xs font-mono bg-slate-100 text-slate-600 px-2.5 py-0.5 rounded-md border border-slate-200">NISN: {{ $siswa->nisn }}</span>
            </div>
            <div class="flex flex-wrap items-center gap-2 pt-1">
                <span class="text-xs text-slate-500 font-medium">Gaya Belajar (VARK):</span>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-bold bg-purple-100 text-purple-900 border border-purple-200">
                    {{ $gayaBelajarBersih }}
                </span>
                <span class="text-slate-300 mx-1">&bull;</span>
                <span class="text-xs text-slate-500 font-medium">Status Ketuntasan:</span>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-bold {{ $siswa->jumlah_tp_belum_tuntas == 0 ? 'bg-emerald-100 text-emerald-900 border border-emerald-200' : 'bg-rose-100 text-rose-900 border border-rose-200' }}">
                    {{ $siswa->jumlah_tp_tuntas }} Tuntas, {{ $siswa->jumlah_tp_belum_tuntas }} Perlu Remedial
                </span>
            </div>
        </div>

        <!-- Status Diagnostik Badge -->
        <div class="shrink-0 flex items-center gap-2">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold bg-teal-50 text-teal-800 border border-teal-200">
                <span class="h-2 w-2 rounded-full bg-teal-600"></span>
                <span>Status Diagnostik AI Aktif</span>
            </span>
        </div>
    </div>

    <!-- 3 Stat Tiles (Consistent with Dashboard & Index) -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-6">
        <div class="p-5 rounded-xl bg-slate-50/80 border border-slate-200/80 space-y-1">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-500 block">Rata-Rata Capaian Sumatif</span>
            <div class="text-3xl font-extrabold text-slate-900 font-mono pt-1">
                {{ $personalisasi->skor_performa ?? $siswa->rata_rata_nilai }}
            </div>
            <p class="text-xs text-slate-500 pt-0.5">Skor performa gabungan 20 butir TP</p>
        </div>

        <div class="p-5 rounded-xl bg-slate-50/80 border border-slate-200/80 space-y-1 flex flex-col justify-between">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500 block mb-1">Kategori Capaian</span>
                <div class="pt-1">
                    <span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-bold {{ ($personalisasi->kategori_pemahaman ?? '') === 'Tinggi' ? 'bg-emerald-100 text-emerald-900 border border-emerald-300' : (($personalisasi->kategori_pemahaman ?? '') === 'Sedang' ? 'bg-blue-100 text-blue-900 border border-blue-300' : 'bg-rose-100 text-rose-900 border border-rose-300') }}">
                        Kategori: {{ $personalisasi->kategori_pemahaman ?? 'Sedang' }}
                    </span>
                </div>
            </div>
            <p class="text-xs text-slate-500 pt-2">Berdasarkan pedoman Kurikulum Merdeka</p>
        </div>

        <div class="p-5 rounded-xl bg-slate-50/80 border border-slate-200/80 space-y-1 flex flex-col justify-between">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500 block mb-1">Pencapaian KKM (75)</span>
                <div class="text-2xl font-bold font-mono text-slate-900 pt-1">
                    {{ $siswa->jumlah_tp_tuntas }} <span class="text-sm font-normal text-slate-500">dari 20 TP tuntas</span>
                </div>
            </div>
            <p class="text-xs text-slate-500 pt-2">
                {{ $siswa->jumlah_tp_belum_tuntas > 0 ? $siswa->jumlah_tp_belum_tuntas . ' butir memerlukan remedial' : 'Seluruh butir mencapai target KKM' }}
            </p>
        </div>
    </div>
</div>

<!-- DEDICATED CARD: Ulasan Evaluasi & Rekomendasi Guru (AI) -->
<div class="rounded-2xl bg-white border border-slate-200/80 shadow-xs p-6 space-y-4">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-4 border-b border-slate-100">
        <div class="flex items-center gap-3">
            <div class="p-2 rounded-xl bg-teal-100 text-teal-700 border border-teal-200 shadow-2xs">
                <svg class="h-5 w-5" aria-hidden="true" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </div>
            <div>
                <div class="flex flex-wrap items-center gap-2">
                    <h3 class="text-base font-bold text-slate-900">
                        Ulasan Evaluasi & Rekomendasi Guru (AI):
                    </h3>
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md text-xs font-semibold {{ $badgeSumber['badge_header'] }} border">
                        {{ $personalisasi->sumber_analisis ?? 'Fallback Heuristik' }}
                    </span>
                    @if(!empty($badgeSumber['model']))
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-xs font-mono font-medium bg-slate-100 text-slate-700 border border-slate-200">
                            Model: {{ $badgeSumber['model'] }}
                        </span>
                    @endif
                </div>
                <p class="text-xs text-slate-500 mt-0.5">
                    Evaluasi diagnostik capaian kompetensi siswa dan panduan instruksional bagi guru
                </p>
            </div>
        </div>
        <a href="#catatan_guru_section" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold text-teal-700 bg-teal-50 hover:bg-teal-100 border border-teal-200 transition-colors shrink-0">
            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
            </svg>
            <span>Sesuaikan Catatan</span>
        </a>
    </div>

    @php
        $rawCatatanGuru = $cleanText($personalisasi->catatan_guru ?? 'Rekomendasi Guru (AI): Analisis performa dihasilkan secara otomatis berdasarkan ketercapaian Tujuan Pembelajaran dan karakteristik modalitas belajar siswa SMA.');
        $catatanParagraphs = array_filter(array_map('trim', explode("\n", $rawCatatanGuru)));
    @endphp

    <div class="p-5 rounded-xl bg-slate-50/80 border border-slate-200/80 space-y-3 text-slate-700 text-sm leading-relaxed">
        @foreach($catatanParagraphs as $para)
            <p class="{{ $loop->first ? 'font-medium text-slate-900' : '' }} leading-relaxed">
                {{ $para }}
            </p>
        @endforeach
    </div>
</div>

<!-- Analisis Diagnostik Modalitas Gaya Belajar (VARK) -->
@php
    $distribusiVark = $personalisasi->distribusi_gaya_belajar ?? \App\Services\AiPersonalizationService::hitungDistribusiVark($siswa->gaya_belajar, $siswa->id);
    $gayaStyles = [
        'Visual' => [
            'deskripsi' => 'Optimal melalui peta konsep, diagram alur, skema visual, dan infografis terstruktur.'
        ],
        'Auditori' => [
            'deskripsi' => 'Optimal melalui penjelasan lisan, sesi tanya jawab, rekaman audio, dan diskusi kelompok.'
        ],
        'Kinestetik' => [
            'deskripsi' => 'Optimal melalui eksperimen konkret, simulasi praktis laboratorium, dan peraga nyata.'
        ],
        'Baca dan Tulis' => [
            'deskripsi' => 'Optimal melalui modul materi teks mendalam, penulisan rangkuman, dan glosarium istilah.'
        ],
    ];
@endphp
<div class="rounded-2xl bg-white p-6 shadow-xs border border-slate-200/80 space-y-4">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-3 border-b border-slate-100">
        <div>
            <h3 class="text-base font-bold text-slate-900">Analisis Diagnostik Gaya Belajar Siswa (Modalitas VARK)</h3>
            <p class="text-xs text-slate-500 mt-0.5">Pemetaan persentase modalitas belajar non-kognitif untuk dasar diferensiasi pembelajaran</p>
        </div>
        <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-bold bg-purple-100 text-purple-900 border border-purple-200 shrink-0">
            Dominan: {{ $gayaBelajarBersih }}
        </span>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach(['Visual', 'Auditori', 'Kinestetik', 'Baca dan Tulis'] as $gaya)
            @php
                $persen = (int) ($distribusiVark[$gaya] ?? 25);
                $isDominan = ($gaya === $gayaBelajarBersih);
                $conf = $gayaStyles[$gaya] ?? $gayaStyles['Visual'];
            @endphp
            <div class="rounded-xl border {{ $isDominan ? 'border-purple-300 ring-2 ring-purple-100 bg-purple-50/30' : 'border-slate-200 bg-slate-50/60' }} p-4 space-y-3 flex flex-col justify-between transition-all">
                <div>
                    <div class="flex items-center justify-between gap-2">
                        <span class="text-sm font-bold text-slate-900">{{ $gaya }}</span>
                        @if($isDominan)
                            <span class="text-xs font-bold px-2 py-0.5 rounded-full bg-purple-100 text-purple-900 border border-purple-200">
                                Dominan
                            </span>
                        @endif
                    </div>
                    <div class="mt-2 flex items-baseline gap-1.5">
                        <span class="text-2xl font-bold font-mono {{ $isDominan ? 'text-purple-700' : 'text-slate-800' }}">{{ $persen }}%</span>
                        <span class="text-xs text-slate-500">proporsi</span>
                    </div>

                    <!-- Progress Bar -->
                    <div class="w-full bg-slate-200 rounded-full h-2 mt-2 overflow-hidden">
                        <div class="{{ $isDominan ? 'bg-purple-600' : 'bg-slate-600' }} h-2 rounded-full transition-all duration-500" style="width: {{ $persen }}%"></div>
                    </div>
                </div>

                <p class="text-xs text-slate-500 leading-relaxed pt-2.5 border-t border-slate-200/60">
                    {{ $conf['deskripsi'] }}
                </p>
            </div>
        @endforeach
    </div>
</div>

<!-- Remedial TP Alert Box (If any) -->
@if(count($remedialTps) > 0)
<div class="rounded-2xl bg-rose-50 border border-rose-200 p-5 space-y-3">
    <div class="flex items-center gap-2 text-rose-800 font-bold text-sm">
        <svg class="h-5 w-5 text-rose-600" aria-hidden="true" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
        </svg>
        <span>Daftar Tujuan Pembelajaran (TP) yang Memerlukan Bimbingan Remedial:</span>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 pt-1">
        @foreach($remedialTps as $rem)
            <div class="rounded-xl bg-white p-3.5 border border-rose-200 text-xs sm:text-sm font-semibold text-rose-900 flex items-start gap-2.5 shadow-2xs">
                <span class="h-2 w-2 rounded-full bg-rose-500 mt-1.5 shrink-0"></span>
                <span>{{ $cleanText($rem) }}</span>
            </div>
        @endforeach
    </div>
</div>
@endif

<!-- Recommendations Section: Materials & Adaptive Learning Activities -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Rekomendasi Bahan Ajar & Modul Terarah -->
    <div class="rounded-2xl bg-white p-6 shadow-xs border border-slate-200/80 space-y-4 flex flex-col">
        <div class="pb-3 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="text-base font-bold text-slate-900">Rekomendasi Bahan Ajar & Modul Terarah</h3>
                <p class="text-xs text-slate-500 mt-0.5">Rangkuman bahan ajar terarah per butir TP dan modalitas belajar</p>
            </div>
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-bold bg-purple-100 text-purple-900 border border-purple-200 shrink-0">
                Gaya {{ $gayaBelajarBersih }}
            </span>
        </div>
        <div class="flex-1 p-4 rounded-xl bg-slate-50/80 border border-slate-200/80 text-sm text-slate-700 leading-relaxed">
            {{ $cleanText($personalisasi->rekomendasi_materi ?? 'Belum ada rekomendasi materi yang tergenerate.') }}
        </div>
    </div>

    <!-- Strategi Aktivitas Pembelajaran Adaptif -->
    <div class="rounded-2xl bg-white p-6 shadow-xs border border-slate-200/80 space-y-4 flex flex-col">
        <div class="pb-3 border-b border-slate-100">
            <h3 class="text-base font-bold text-slate-900">Strategi Aktivitas Pembelajaran Adaptif</h3>
            <p class="text-xs text-slate-500 mt-0.5">Rangkuman analisis strategi aktivasi pembelajaran adaptif Kurikulum Merdeka</p>
        </div>
        
        @php
            $rawAktivitas = $cleanText($personalisasi->rekomendasi_aktivitas ?? 'Jaga konsistensi pengerjaan tugas dan latihan sumatif TP.');
            $aktivitasItems = array_filter(array_map('trim', explode("\n", $rawAktivitas)));
        @endphp

        <div class="flex-1 space-y-2.5">
            @forelse($aktivitasItems as $item)
                @php
                    $cleanItem = ltrim($item, '•*- \t\n\r\0\x0B');
                @endphp
                @if(!empty($cleanItem))
                    <div class="flex items-start gap-3 p-3.5 rounded-xl bg-slate-50/80 border border-slate-200/80 text-sm text-slate-700 leading-relaxed shadow-2xs">
                        <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-teal-600 text-[11px] font-bold text-white shadow-2xs mt-0.5">
                            {{ $loop->iteration }}
                        </span>
                        <div class="flex-1">
                            {{ $cleanItem }}
                        </div>
                    </div>
                @endif
            @empty
                <p class="text-sm text-slate-500">Belum ada strategi aktivitas yang tercatat.</p>
            @endforelse
        </div>
    </div>
</div>

<!-- Riwayat Nilai per Tujuan Pembelajaran (TP) -->
<div class="rounded-2xl bg-white p-6 shadow-xs border border-slate-200/80 space-y-4">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-3 border-b border-slate-100">
        <div>
            <h3 class="text-base font-bold text-slate-900">Riwayat Nilai per Tujuan Pembelajaran (TP)</h3>
            <p class="text-xs text-slate-500 mt-0.5">Seluruh butir TP yang dinilai pada Kurikulum Merdeka beserta diagnostik rekomendasi AI</p>
        </div>
        <div class="flex flex-wrap items-center gap-2.5">
            <button type="button" onclick="toggleAllRecommendations()" id="toggle-all-rek-btn" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-xl border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 shadow-2xs transition-colors">
                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 9l4-4 4 4m0 6l-4 4-4-4" />
                </svg>
                <span id="toggle-all-rek-text">Ringkaskan Tampilan</span>
            </button>
            <a href="{{ route('penilaian.matrix') }}" class="inline-flex items-center px-3.5 py-1.5 text-xs font-semibold rounded-xl bg-teal-600 text-white hover:bg-teal-700 transition-colors shadow-xs">
                Edit Nilai Matriks
            </a>
        </div>
    </div>

    <div class="overflow-x-auto rounded-xl border border-slate-200">
        <table class="w-full text-left text-sm text-slate-600">
            <thead class="bg-slate-50 text-xs uppercase font-semibold text-slate-500 border-b border-slate-200">
                <tr>
                    <th class="py-3 px-4 w-28">Lingkup Materi</th>
                    <th class="py-3 px-3 w-24">Kode TP</th>
                    <th class="py-3 px-4">Deskripsi TP & Rekomendasi AI</th>
                    <th class="py-3 px-3 text-center w-24">Target KKM</th>
                    <th class="py-3 px-3 text-center w-24">Nilai</th>
                    <th class="py-3 px-4 text-center w-28">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($siswa->nilaiSumatifTps as $p)
                    <tr class="hover:bg-slate-50/50 transition-colors align-top">
                        <td class="py-3.5 px-4 font-semibold text-slate-900 text-sm">
                            {{ $p->tujuanPembelajaran->lingkupMateri->kode_lm ?? '-' }}
                        </td>
                        <td class="py-3.5 px-3 font-mono font-bold text-teal-700 text-sm">{{ $p->tujuanPembelajaran->kode_tp ?? '-' }}</td>
                        <td class="py-3.5 px-4 space-y-2.5">
                            <div class="text-slate-800 font-medium text-sm leading-relaxed">
                                {{ $cleanText($p->tujuanPembelajaran->deskripsi_tp ?? '-') }}
                            </div>
                            
                            <!-- Rekomendasi AI untuk butir TP ini -->
                            <details class="tp-rec-accordion group mt-2" open>
                                <summary class="flex items-center justify-between cursor-pointer list-none select-none py-1.5 px-3 rounded-lg border {{ $p->status_ketuntasan === 'Tuntas' ? 'bg-emerald-50/70 border-emerald-200 text-emerald-900' : 'bg-rose-50/70 border-rose-200 text-rose-900' }} text-xs font-medium transition-all">
                                    <div class="flex items-center gap-2">
                                        <svg class="h-3.5 w-3.5 {{ $p->status_ketuntasan === 'Tuntas' ? 'text-emerald-700' : 'text-rose-700' }} shrink-0" aria-hidden="true" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <span>Rekomendasi AI (3 Rekomendasi Tindak Lanjut {{ $p->status_ketuntasan === 'Tuntas' ? 'Pengayaan' : 'Remedial' }}):</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ $p->status_ketuntasan === 'Tuntas' ? 'bg-emerald-100 text-emerald-900 border border-emerald-200' : 'bg-rose-100 text-rose-900 border border-rose-200' }}">
                                            3 Butir
                                        </span>
                                        <svg class="h-3.5 w-3.5 text-slate-400 group-open:rotate-180 transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </div>
                                </summary>

                                @php
                                    $kodeTpItem = $p->tujuanPembelajaran->kode_tp ?? 'TP';
                                    $rawCatatan = $p->catatan ?: ($p->status_ketuntasan === 'Tuntas' ? "1. Pendalaman Materi: Berikan materi pengayaan konsep butir {$kodeTpItem}.\n2. Kolaborasi Sebaya: Diberdayakan sebagai tutor sebaya bagi rekan sekelas.\n3. Penguatan Portofolio: Dokumentasikan hasil belajar ke lembar portofolio." : "1. Intervensi Konsep: Bimbingan ulang konsep butir {$kodeTpItem} sesuai gaya belajar {$gayaBelajarBersih}.\n2. Latihan Terarah: Pengerjaan latihan bertingkat dengan tutor atau guru.\n3. Asesmen Perbaikan: Evaluasi formatif perbaikan khusus butir ini.");
                                    $rekList = array_filter(array_map('trim', explode("\n", $rawCatatan)));
                                @endphp

                                <div class="space-y-2 pt-2">
                                    @foreach($rekList as $rItem)
                                        @php
                                            $cleanRItem = preg_replace('/^[0-9]+[\.\)]\s*/', '', $rItem);
                                        @endphp
                                        <div class="flex items-start gap-2.5 p-2.5 rounded-lg bg-slate-50 border {{ $p->status_ketuntasan === 'Tuntas' ? 'border-emerald-100 text-emerald-950' : 'border-rose-100 text-rose-950' }} shadow-2xs">
                                            <span class="flex h-4 w-4 shrink-0 items-center justify-center rounded-full {{ $p->status_ketuntasan === 'Tuntas' ? 'bg-emerald-600' : 'bg-rose-600' }} text-[10px] font-bold text-white mt-0.5 shadow-2xs">
                                                {{ $loop->iteration }}
                                            </span>
                                            <span class="text-xs text-slate-700 leading-relaxed font-normal flex-1">
                                                {{ $cleanText($cleanRItem) }}
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="hidden" data-raw-catatan>{{ $p->catatan }}</div>
                            </details>
                        </td>
                        <td class="py-3.5 px-3 text-center font-mono text-slate-700 text-sm font-medium">{{ $p->tujuanPembelajaran->target_ketercapaian ?? 75 }}</td>
                        <td class="py-3.5 px-3 text-center font-mono font-bold text-sm {{ $p->status_ketuntasan === 'Tuntas' ? 'text-teal-700' : 'text-rose-600' }}">
                            {{ $p->nilai }}
                        </td>
                        <td class="py-3.5 px-4 text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-bold {{ $p->status_ketuntasan === 'Tuntas' ? 'bg-emerald-100 text-emerald-900 border border-emerald-200' : 'bg-rose-100 text-rose-900 border border-rose-200' }}">
                                {{ $p->status_ketuntasan }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-8 text-center text-slate-400 text-sm">Belum ada nilai TP yang tercatat.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Form Catatan Intervensi Guru -->
<div id="catatan_guru_section" class="rounded-2xl bg-white p-6 shadow-xs border border-slate-200/80 space-y-4">
    <div class="pb-3 border-b border-slate-100 flex items-center justify-between">
        <div>
            <h3 class="text-base font-bold text-slate-900">Catatan Diagnostik & Rencana Intervensi Guru</h3>
            <p class="text-xs text-slate-500 mt-0.5">Guru dapat menyesuaikan atau menambahkan catatan rekomendasi AI sebelum disimpan</p>
        </div>
        <span class="text-xs font-semibold px-2.5 py-0.5 rounded-md bg-amber-50 text-amber-800 border border-amber-200">
            Dapat Diedit Guru
        </span>
    </div>
    <form action="{{ route('personalisasi.catatan', $siswa) }}" method="POST" class="space-y-4">
        @csrf
        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-1.5" for="catatan_guru">
                Catatan Bimbingan atau Rekomendasi Tindak Lanjut Guru:
            </label>
            <textarea name="catatan_guru" id="catatan_guru" class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-slate-300 bg-white text-slate-800 placeholder-slate-400 focus:border-teal-500 focus:outline-none focus:ring-2 focus:ring-teal-500/20 transition-all leading-relaxed" rows="5" placeholder="Tuliskan arahan bimbingan akademik, penugasan remedial per TP, atau materi pengayaan...">{{ old('catatan_guru', $cleanText($personalisasi->catatan_guru)) }}</textarea>
            <p class="text-xs text-slate-500 mt-1.5 flex items-start gap-1.5 leading-relaxed">
                <svg class="h-4 w-4 text-teal-600 shrink-0 mt-0.5" aria-hidden="true" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>Catatan ini otomatis terisi dari rekomendasi AI. Klik <strong>Simpan Catatan Guru</strong> untuk memperbarui kartu Ulasan Evaluasi di atas.</span>
            </p>
        </div>
        <div class="flex items-center gap-3 pt-1">
            <button type="submit" class="inline-flex items-center justify-center gap-1.5 px-4 py-2 text-sm font-semibold rounded-xl bg-teal-600 text-white hover:bg-teal-700 shadow-xs transition-colors focus:ring-2 focus:ring-teal-500/30">
                <svg class="h-4 w-4" aria-hidden="true" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                </svg>
                <span>Simpan Catatan Guru</span>
            </button>
        </div>
    </form>
</div>

<script>
    function toggleAllRecommendations() {
        const accordions = document.querySelectorAll('.tp-rec-accordion');
        const textSpan = document.getElementById('toggle-all-rek-text');
        if (!accordions.length) return;

        let anyOpen = false;
        accordions.forEach(el => {
            if (el.hasAttribute('open')) anyOpen = true;
        });

        if (anyOpen) {
            accordions.forEach(el => el.removeAttribute('open'));
            if (textSpan) textSpan.textContent = 'Buka Semua Rekomendasi';
        } else {
            accordions.forEach(el => el.setAttribute('open', ''));
            if (textSpan) textSpan.textContent = 'Ringkaskan Tampilan';
        }
    }
</script>
@endsection
