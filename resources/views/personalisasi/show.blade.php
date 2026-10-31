@extends('layouts.app')

@section('title', 'Diagnostik Personalisasi - ' . $siswa->nama)

@section('content')
@php
    // Helper pembersih tanda garis miring (slash)
    $cleanText = function($text) {
        if (!$text) return '';
        $text = str_replace(['Baca/Tulis', 'baca/tulis'], ['Baca dan Tulis', 'baca dan tulis'], $text);
        $text = str_replace(['/TP', 'per/TP', 'per-TP'], [' TP', 'per TP', 'per TP'], $text);
        $text = str_replace([' / ', '/'], [' atau ', ' atau '], $text);
        return $text;
    };
    $gayaBelajarBersih = $cleanText($siswa->gaya_belajar);
@endphp

<!-- Page Header -->
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-slate-200">
    <div>
        <h2 class="text-2xl font-extrabold tracking-tight text-slate-900">Profil Personalisasi & Diagnostik TP</h2>
        <p class="text-sm text-slate-600 mt-1 leading-relaxed">
            Rekomendasi pembelajaran adaptif berbasis ketercapaian Tujuan Pembelajaran dan modalitas belajar untuk <strong class="text-slate-900">{{ $siswa->nama }}</strong> ({{ $siswa->nisn }})
        </p>
    </div>
    <div class="flex items-center gap-3 shrink-0">
        <form action="{{ route('personalisasi.ai-analyze', $siswa) }}" method="POST" class="inline" onsubmit="const btn = document.getElementById('ai-analyze-btn'); const text = document.getElementById('ai-analyze-text'); const spinner = document.getElementById('ai-analyze-spinner'); const icon = document.getElementById('ai-analyze-icon'); if(btn){ btn.disabled = true; btn.classList.add('opacity-80', 'cursor-not-allowed'); if(icon) icon.classList.add('hidden'); if(spinner) spinner.classList.remove('hidden'); if(text) text.textContent = 'Menganalisis Ketercapaian TP...'; }">
            @csrf
            <button type="submit" id="ai-analyze-btn" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-teal-700 text-white text-sm font-semibold shadow-xs hover:bg-teal-800 transition-all focus:ring-2 focus:ring-teal-500 focus:ring-offset-2">
                <svg id="ai-analyze-spinner" class="animate-spin h-4 w-4 text-white hidden" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                <svg id="ai-analyze-icon" class="h-4 w-4" aria-hidden="true" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z" />
                </svg>
                <span id="ai-analyze-text">Analisis Ulang dengan AI</span>
            </button>
        </form>
        <a href="{{ route('personalisasi.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-semibold rounded-xl border border-slate-300 text-slate-700 bg-white hover:bg-slate-50 transition-colors shadow-xs">
            <svg class="h-4 w-4" aria-hidden="true" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" /></svg>
            <span>Kembali ke Daftar</span>
        </a>
    </div>
</div>

<!-- Sumber Data & Indikator Asal Analisis Notifikasi Card -->
@php
    $badgeSumber = $personalisasi->sumber_analisis_badge ?? [
        'nama' => 'Fallback Heuristik (Aturan Pedagogi Internal)',
        'tipe' => 'fallback',
        'bg' => 'bg-amber-50 text-amber-900 border-amber-200',
        'badge_header' => 'bg-amber-100 text-amber-900 border-amber-300',
        'dot' => 'bg-amber-500',
        'model' => 'PLA Merdeka Engine',
    ];
@endphp
<div class="rounded-2xl border p-4 sm:p-5 shadow-2xs transition-all {{ ($isFromDatabase ?? true) ? 'bg-slate-50/90 border-slate-200' : 'bg-teal-50/70 border-teal-200' }}">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div class="flex items-start sm:items-center gap-3">
            <div class="p-2.5 rounded-xl {{ ($isFromDatabase ?? true) ? 'bg-white text-slate-700 border border-slate-200' : 'bg-teal-600 text-white' }} shrink-0 shadow-2xs">
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
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Status Sumber Analisis:</span>
                    @if(($isFromDatabase ?? true))
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-md text-xs font-bold bg-slate-200 text-slate-800 border border-slate-300">
                            <span class="h-1.5 w-1.5 rounded-full bg-slate-600"></span>
                            Dimuat dari Database
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-md text-xs font-bold bg-teal-100 text-teal-800 border border-teal-300">
                            <span class="h-1.5 w-1.5 rounded-full bg-teal-600"></span>
                            Baru Saja Dihasilkan
                        </span>
                    @endif

                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-md text-xs font-bold {{ $badgeSumber['bg'] }} border">
                        <span class="h-1.5 w-1.5 rounded-full {{ $badgeSumber['dot'] }}"></span>
                        {{ $badgeSumber['nama'] }}
                    </span>
                </div>
                <p class="text-xs text-slate-600 mt-1 leading-relaxed">
                    @if(($isFromDatabase ?? true))
                        Data rekomendasi ini dimuat dari arsip database (tersimpan pada <strong>{{ $personalisasi->updated_at ? $personalisasi->updated_at->isoFormat('D MMMM Y, HH:mm') : 'Baru' }}</strong>).
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
            <span class="font-mono bg-white px-2.5 py-1 rounded-md border border-slate-200">ID #{{ $personalisasi->id }}</span>
        </div>
    </div>
</div>

<!-- Overview Row -->
<div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
    <!-- Siswa Profile (5 cols) -->
    <div class="lg:col-span-5 rounded-2xl bg-white p-6 sm:p-7 shadow-xs border border-slate-200/90 space-y-4">
        <div class="pb-3 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-base font-bold text-slate-900">Identitas Siswa</h3>
            <span class="text-xs text-slate-500 font-mono bg-slate-100 px-2.5 py-0.5 rounded">{{ $siswa->nisn }}</span>
        </div>
        <table class="w-full text-left text-sm text-slate-700">
            <tbody class="divide-y divide-slate-100">
                <tr>
                    <td class="py-3 px-1 font-medium text-slate-500 w-1/3">NISN</td>
                    <td class="py-3 px-2 font-mono font-bold text-slate-900">{{ $siswa->nisn }}</td>
                </tr>
                <tr>
                    <td class="py-3 px-1 font-medium text-slate-500">Nama Siswa</td>
                    <td class="py-3 px-2 font-semibold text-slate-900">{{ $siswa->nama }}</td>
                </tr>
                <tr>
                    <td class="py-3 px-1 font-medium text-slate-500">Kelas SMA</td>
                    <td class="py-3 px-2 font-medium text-slate-800">{{ $siswa->kelas }}</td>
                </tr>
                <tr>
                    <td class="py-3 px-1 font-medium text-slate-500">Gaya Belajar (VARK)</td>
                    <td class="py-3 px-2">
                        <span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-bold bg-purple-100 text-purple-900 border border-purple-200">
                            {{ $gayaBelajarBersih }}
                        </span>
                    </td>
                </tr>
                <tr>
                    <td class="py-3 px-1 font-medium text-slate-500">Status Ketuntasan</td>
                    <td class="py-3 px-2">
                        <span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-bold {{ $siswa->jumlah_tp_belum_tuntas == 0 ? 'bg-emerald-100 text-emerald-900 border border-emerald-200' : 'bg-rose-100 text-rose-900 border border-rose-200' }}">
                            {{ $siswa->jumlah_tp_tuntas }} Tuntas, {{ $siswa->jumlah_tp_belum_tuntas }} Perlu Remedial
                        </span>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Status Engine AI Diagnostik & Catatan Evaluasi Guru (7 cols) -->
    <div class="lg:col-span-7 rounded-2xl bg-white p-6 sm:p-7 shadow-xs border border-slate-200/90 space-y-5 flex flex-col justify-between">
        <div>
            <div class="pb-3 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="text-base font-bold text-slate-900">Status Diagnostik AI Kurikulum Merdeka</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Analisis ketercapaian kompetensi dan profil modalitas adaptif</p>
                </div>
                <span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-bold bg-teal-100 text-teal-900 border border-teal-200">
                    Status Diagnostik AI Aktif
                </span>
            </div>

            <!-- Score Summary Cards -->
            <div class="grid grid-cols-2 gap-4 py-4">
                <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/70">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500 block mb-1">Rata-Rata Capaian Sumatif</span>
                    <div class="text-3xl sm:text-4xl font-extrabold text-teal-800 font-mono">
                        {{ $personalisasi->skor_performa ?? $siswa->rata_rata_nilai }}
                    </div>
                </div>
                <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/70 flex flex-col justify-center">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500 block mb-1">Kategori Capaian</span>
                    <div>
                        <span class="inline-flex items-center px-3 py-1 rounded-lg text-sm font-bold shadow-2xs {{ ($personalisasi->kategori_pemahaman ?? '') === 'Tinggi' ? 'bg-emerald-100 text-emerald-900 border border-emerald-300' : (($personalisasi->kategori_pemahaman ?? '') === 'Sedang' ? 'bg-blue-100 text-blue-900 border border-blue-300' : 'bg-rose-100 text-rose-900 border border-rose-300') }}">
                            Kategori: {{ $personalisasi->kategori_pemahaman ?? 'Sedang' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Ulasan Evaluasi Guru (AI) Card - Objektif & Memberi Masukan Membangun -->
        <div class="rounded-xl border border-teal-200 bg-teal-50/40 p-5 space-y-3">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                <div class="flex flex-wrap items-center gap-2">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-teal-900 flex items-center gap-1.5">
                        <svg class="h-4 w-4 text-teal-700" aria-hidden="true" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <span>Ulasan Evaluasi & Rekomendasi Guru (AI):</span>
                    </h4>
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-semibold {{ $badgeSumber['badge_header'] }} border">
                        {{ $personalisasi->sumber_analisis ?? 'Fallback Heuristik' }}
                    </span>
                </div>
                <a href="#catatan_guru_section" class="text-xs font-semibold text-teal-800 hover:text-teal-950 hover:underline">
                    Sesuaikan Catatan
                </a>
            </div>
            <div class="text-sm sm:text-base text-slate-800 leading-relaxed font-normal">
                {{ $cleanText($personalisasi->catatan_guru ?? 'Rekomendasi Guru (AI): Analisis performa dihasilkan secara otomatis berdasarkan ketercapaian Tujuan Pembelajaran dan karakteristik modalitas belajar siswa SMA.') }}
            </div>
        </div>
    </div>
</div>

<!-- Analisis Diagnostik Modalitas Gaya Belajar (VARK) & Persentase -->
@php
    $distribusiVark = $personalisasi->distribusi_gaya_belajar ?? \App\Services\AiPersonalizationService::hitungDistribusiVark($siswa->gaya_belajar, $siswa->id);
    $gayaStyles = [
        'Visual' => [
            'color' => 'indigo',
            'border' => 'border-indigo-200',
            'bg' => 'bg-indigo-50/40',
            'bar' => 'bg-indigo-600',
            'deskripsi' => 'Optimal melalui peta konsep, diagram alur, skema visual, dan infografis terstruktur.'
        ],
        'Auditori' => [
            'color' => 'sky',
            'border' => 'border-sky-200',
            'bg' => 'bg-sky-50/40',
            'bar' => 'bg-sky-600',
            'deskripsi' => 'Optimal melalui penjelasan lisan, sesi tanya jawab, rekaman audio, dan diskusi kelompok.'
        ],
        'Kinestetik' => [
            'color' => 'amber',
            'border' => 'border-amber-200',
            'bg' => 'bg-amber-50/40',
            'bar' => 'bg-amber-600',
            'deskripsi' => 'Optimal melalui eksperimen konkret, simulasi praktis laboratorium, dan peraga nyata.'
        ],
        'Baca dan Tulis' => [
            'color' => 'emerald',
            'border' => 'border-emerald-200',
            'bg' => 'bg-emerald-50/40',
            'bar' => 'bg-emerald-600',
            'deskripsi' => 'Optimal melalui modul materi teks mendalam, penulisan rangkuman, dan glosarium istilah.'
        ],
    ];
@endphp
<div class="rounded-2xl bg-white p-6 sm:p-7 shadow-xs border border-slate-200/90 space-y-5">
    <div class="pb-3 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
        <div>
            <h3 class="text-base sm:text-lg font-bold text-slate-900">Analisis Diagnostik Gaya Belajar Siswa (Modalitas VARK)</h3>
            <p class="text-xs text-slate-500 mt-0.5">Pemetaan persentase modalitas belajar non-kognitif untuk dasar diferensiasi pembelajaran</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-bold bg-purple-100 text-purple-900 border border-purple-200">
                Dominan: {{ $gayaBelajarBersih }}
            </span>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach(['Visual', 'Auditori', 'Kinestetik', 'Baca dan Tulis'] as $gaya)
            @php
                $persen = (int) ($distribusiVark[$gaya] ?? 25);
                $isDominan = ($gaya === $gayaBelajarBersih);
                $conf = $gayaStyles[$gaya] ?? $gayaStyles['Visual'];
            @endphp
            <div class="rounded-xl border {{ $isDominan ? 'border-purple-300 ring-2 ring-purple-100 bg-purple-50/20' : 'border-slate-200 bg-slate-50/50' }} p-4 space-y-3 flex flex-col justify-between transition-all">
                <div>
                    <div class="flex items-center justify-between gap-2">
                        <span class="text-sm font-bold text-slate-900">{{ $gaya }}</span>
                        @if($isDominan)
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-purple-100 text-purple-900 border border-purple-200">
                                Dominan
                            </span>
                        @endif
                    </div>
                    <div class="mt-2 flex items-baseline gap-1">
                        <span class="text-2xl font-extrabold font-mono {{ $isDominan ? 'text-purple-700' : 'text-slate-800' }}">{{ $persen }}%</span>
                        <span class="text-xs text-slate-500">proporsi</span>
                    </div>

                    <!-- Progress Bar -->
                    <div class="w-full bg-slate-200/80 rounded-full h-2.5 mt-2 overflow-hidden">
                        <div class="{{ $isDominan ? 'bg-purple-600' : 'bg-slate-600' }} h-2.5 rounded-full transition-all duration-500" style="width: {{ $persen }}%"></div>
                    </div>
                </div>

                <p class="text-xs text-slate-600 leading-relaxed pt-2 border-t border-slate-200/60">
                    {{ $conf['deskripsi'] }}
                </p>
            </div>
        @endforeach
    </div>
</div>

<!-- Remedial TP Alert Box (If any) -->
@if(count($remedialTps) > 0)
<div class="rounded-2xl bg-rose-50 border border-rose-200 p-6 space-y-3">
    <div class="flex items-center gap-2 text-rose-800 font-bold text-base">
        <svg class="h-5 w-5 text-rose-600" aria-hidden="true" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
        </svg>
        <span>Daftar Tujuan Pembelajaran (TP) yang Memerlukan Bimbingan Remedial:</span>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-2.5">
        @foreach($remedialTps as $rem)
            <div class="rounded-xl bg-white p-3.5 border border-rose-200 text-xs sm:text-sm font-semibold text-rose-900 flex items-start gap-2.5 shadow-2xs">
                <span class="h-2 w-2 rounded-full bg-rose-500 mt-1.5 shrink-0"></span>
                <span>{{ $cleanText($rem) }}</span>
            </div>
        @endforeach
    </div>
</div>
@endif

<!-- Recommendations Section (Targeted Materials & Adaptive Strategy) -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Rekomendasi Bahan Ajar & Modul Terarah - Nyaman Dibaca -->
    <div class="rounded-2xl bg-white p-6 sm:p-7 shadow-xs border border-slate-200/90 space-y-4 flex flex-col">
        <div class="pb-3 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="text-base sm:text-lg font-bold text-slate-900">Rekomendasi Bahan Ajar & Modul Terarah</h3>
                <p class="text-xs text-slate-500 mt-0.5">Rangkuman bahan ajar terarah per butir TP dan modalitas belajar</p>
            </div>
            <span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-bold bg-purple-100 text-purple-900 border border-purple-200">
                Gaya {{ $gayaBelajarBersih }}
            </span>
        </div>
        <div class="flex-1 p-5 rounded-xl bg-slate-50/80 border border-slate-200/80 text-sm sm:text-base text-slate-800 leading-relaxed">
            {{ $cleanText($personalisasi->rekomendasi_materi ?? 'Belum ada rekomendasi materi yang tergenerate.') }}
        </div>
    </div>

    <!-- Strategi Aktivitas Pembelajaran Adaptif - Dibuat Poin Kartu Nyaman Dibaca -->
    <div class="rounded-2xl bg-white p-6 sm:p-7 shadow-xs border border-slate-200/90 space-y-4 flex flex-col">
        <div class="pb-3 border-b border-slate-100">
            <h3 class="text-base sm:text-lg font-bold text-slate-900">Strategi Aktivitas Pembelajaran Adaptif</h3>
            <p class="text-xs text-slate-500 mt-0.5">Rangkuman analisis strategi aktivasi pembelajaran adaptif Kurikulum Merdeka</p>
        </div>
        
        @php
            $rawAktivitas = $cleanText($personalisasi->rekomendasi_aktivitas ?? 'Jaga konsistensi pengerjaan tugas dan latihan sumatif TP.');
            $aktivitasItems = array_filter(array_map('trim', explode("\n", $rawAktivitas)));
        @endphp

        <div class="flex-1 space-y-3">
            @forelse($aktivitasItems as $item)
                @php
                    $cleanItem = ltrim($item, '•*- \t\n\r\0\x0B');
                @endphp
                @if(!empty($cleanItem))
                    <div class="flex items-start gap-3.5 p-4 rounded-xl bg-slate-50 border border-slate-200/80 text-sm sm:text-base text-slate-800 leading-relaxed shadow-2xs">
                        <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-teal-700 text-xs font-bold text-white shadow-2xs mt-0.5">
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

<!-- Assessment History Table -->
<div class="rounded-2xl bg-white p-6 sm:p-7 shadow-xs border border-slate-200/90 space-y-4">
    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
        <div>
            <h3 class="text-base sm:text-lg font-bold text-slate-900">Riwayat Nilai per Tujuan Pembelajaran (TP)</h3>
            <p class="text-xs text-slate-500 mt-0.5">Seluruh butir TP yang dinilai pada Kurikulum Merdeka beserta diagnostik rekomendasi AI</p>
        </div>
        <a href="{{ route('penilaian.matrix') }}" class="inline-flex items-center px-4 py-2 text-xs font-semibold rounded-xl bg-teal-700 text-white hover:bg-teal-800 transition-colors shadow-xs">
            Edit Nilai Matriks
        </a>
    </div>
    <div class="overflow-x-auto rounded-xl border border-slate-200">
        <table class="w-full text-left text-sm text-slate-600">
            <thead class="bg-slate-50 text-xs uppercase font-semibold text-slate-600 border-b border-slate-200">
                <tr>
                    <th class="py-3.5 px-3 w-28">Lingkup Materi</th>
                    <th class="py-3.5 px-3 w-24">Kode TP</th>
                    <th class="py-3.5 px-4">Deskripsi TP & Rekomendasi AI</th>
                    <th class="py-3.5 px-2 text-center w-24">Target KKM</th>
                    <th class="py-3.5 px-3 text-center w-24">Nilai</th>
                    <th class="py-3.5 px-3 text-center w-28">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($siswa->nilaiSumatifTps as $p)
                    <tr class="hover:bg-slate-50/80 transition-colors align-top">
                        <td class="py-4 px-3 font-semibold text-slate-900">
                            {{ $p->tujuanPembelajaran->lingkupMateri->kode_lm ?? '-' }}
                        </td>
                        <td class="py-4 px-3 font-mono font-bold text-teal-800">{{ $p->tujuanPembelajaran->kode_tp ?? '-' }}</td>
                        <td class="py-4 px-4 space-y-2.5">
                            <div class="text-slate-900 font-semibold text-sm leading-relaxed">
                                {{ $cleanText($p->tujuanPembelajaran->deskripsi_tp ?? '-') }}
                            </div>
                            
                            <!-- Rekomendasi AI untuk butir TP ini (3 Rekomendasi Tindak Lanjut) -->
                            <div class="p-4 rounded-xl border {{ $p->status_ketuntasan === 'Tuntas' ? 'bg-emerald-50/60 border-emerald-200 text-emerald-950' : 'bg-rose-50/70 border-rose-200 text-rose-950' }} text-xs sm:text-sm leading-relaxed shadow-2xs space-y-2.5">
                                <div class="flex items-center justify-between font-bold {{ $p->status_ketuntasan === 'Tuntas' ? 'text-emerald-900' : 'text-rose-900' }} border-b {{ $p->status_ketuntasan === 'Tuntas' ? 'border-emerald-200/70' : 'border-rose-200/70' }} pb-2">
                                    <div class="flex items-center gap-1.5">
                                        <svg class="h-4 w-4 {{ $p->status_ketuntasan === 'Tuntas' ? 'text-emerald-700' : 'text-rose-700' }} shrink-0" aria-hidden="true" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <span>Rekomendasi AI (3 Rekomendasi Tindak Lanjut {{ $p->status_ketuntasan === 'Tuntas' ? 'Pengayaan' : 'Remedial' }} &bull; Gaya {{ $gayaBelajarBersih }}):</span>
                                    </div>
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ $p->status_ketuntasan === 'Tuntas' ? 'bg-emerald-100 text-emerald-900 border border-emerald-200' : 'bg-rose-100 text-rose-900 border border-rose-200' }}">
                                        3 Rekomendasi
                                    </span>
                                </div>

                                @php
                                    $kodeTpItem = $p->tujuanPembelajaran->kode_tp ?? 'TP';
                                    $rawCatatan = $p->catatan ?: ($p->status_ketuntasan === 'Tuntas' ? "1. Pendalaman Materi: Berikan materi pengayaan konsep butir {$kodeTpItem}.\n2. Kolaborasi Sebaya: Diberdayakan sebagai tutor sebaya bagi rekan sekelas.\n3. Penguatan Portofolio: Dokumentasikan hasil belajar ke lembar portofolio." : "1. Intervensi Konsep: Bimbingan ulang konsep butir {$kodeTpItem} sesuai gaya belajar {$gayaBelajarBersih}.\n2. Latihan Terarah: Pengerjaan latihan bertingkat dengan tutor atau guru.\n3. Asesmen Perbaikan: Evaluasi formatif perbaikan khusus butir ini.");
                                    $rekList = array_filter(array_map('trim', explode("\n", $rawCatatan)));
                                @endphp

                                <div class="space-y-2 pt-0.5">
                                    @foreach($rekList as $rItem)
                                        @php
                                            $cleanRItem = preg_replace('/^[0-9]+[\.\)]\s*/', '', $rItem);
                                        @endphp
                                        <div class="flex items-start gap-2.5 p-2.5 rounded-lg bg-white/90 border {{ $p->status_ketuntasan === 'Tuntas' ? 'border-emerald-200/80 text-emerald-950' : 'border-rose-200/80 text-rose-950' }} shadow-2xs">
                                            <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full {{ $p->status_ketuntasan === 'Tuntas' ? 'bg-emerald-700' : 'bg-rose-700' }} text-[10px] font-bold text-white mt-0.5 shadow-2xs">
                                                {{ $loop->iteration }}
                                            </span>
                                            <span class="text-xs sm:text-sm text-slate-800 leading-relaxed font-normal flex-1">
                                                {{ $cleanText($cleanRItem) }}
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="hidden" data-raw-catatan>{{ $p->catatan }}</div>
                            </div>
                        </td>
                        <td class="py-4 px-2 text-center font-mono text-slate-700 font-medium">{{ $p->tujuanPembelajaran->target_ketercapaian ?? 75 }}</td>
                        <td class="py-4 px-3 text-center font-mono font-extrabold text-base {{ $p->status_ketuntasan === 'Tuntas' ? 'text-teal-700' : 'text-rose-600' }}">
                            {{ $p->nilai }}
                        </td>
                        <td class="py-4 px-3 text-center">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold {{ $p->status_ketuntasan === 'Tuntas' ? 'bg-emerald-100 text-emerald-900 border border-emerald-200' : 'bg-rose-100 text-rose-900 border border-rose-200' }}">
                                {{ $p->status_ketuntasan }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-8 text-center text-slate-400">Belum ada nilai TP yang tercatat.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Form Catatan Intervensi Guru -->
<div id="catatan_guru_section" class="rounded-2xl bg-white p-6 sm:p-7 shadow-xs border border-slate-200/90 space-y-4">
    <div class="pb-3 border-b border-slate-100 flex items-center justify-between">
        <div>
            <h3 class="text-base sm:text-lg font-bold text-slate-900">Catatan Diagnostik & Rencana Intervensi Guru</h3>
            <p class="text-xs text-slate-500 mt-0.5">Guru dapat menyesuaikan atau menambahkan catatan rekomendasi AI di atas sebelum disimpan</p>
        </div>
        <span class="text-xs font-semibold px-2.5 py-1 rounded-lg bg-amber-50 text-amber-800 border border-amber-200">
            Dapat Diedit Guru
        </span>
    </div>
    <form action="{{ route('personalisasi.catatan', $siswa) }}" method="POST" class="space-y-4">
        @csrf
        <div>
            <label class="block text-sm font-semibold text-slate-800 mb-1.5" for="catatan_guru">
                Catatan Bimbingan atau Rekomendasi Tindak Lanjut Guru:
            </label>
            <textarea name="catatan_guru" id="catatan_guru" class="w-full px-4 py-3 text-sm sm:text-base rounded-xl border border-slate-300 bg-white placeholder-slate-400 focus:border-teal-600 focus:outline-none focus:ring-2 focus:ring-teal-500/20 transition-all leading-relaxed" rows="5" placeholder="Tuliskan arahan bimbingan akademik, penugasan remedial per TP, atau materi pengayaan...">{{ old('catatan_guru', $cleanText($personalisasi->catatan_guru)) }}</textarea>
            <p class="text-xs text-slate-500 mt-2 flex items-start gap-2 leading-relaxed">
                <svg class="h-4 w-4 text-teal-700 shrink-0 mt-0.5" aria-hidden="true" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>Catatan ini otomatis terisi dari rekomendasi AI. Jika Anda mengubahnya dan klik <strong>Simpan Catatan Guru</strong>, perubahan akan langsung diperbarui pada kartu <strong>Hasil Evaluasi Diagnostik</strong> di atas.</span>
            </p>
        </div>
        <div class="flex items-center gap-3">
            <button type="submit" class="px-5 py-2.5 rounded-xl bg-teal-700 text-white text-sm font-semibold hover:bg-teal-800 transition-colors shadow-xs focus:ring-2 focus:ring-teal-500 focus:ring-offset-2 inline-flex items-center gap-2">
                <svg class="h-4 w-4" aria-hidden="true" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                </svg>
                <span>Simpan Catatan Guru</span>
            </button>
        </div>
    </form>
</div>
@endsection
