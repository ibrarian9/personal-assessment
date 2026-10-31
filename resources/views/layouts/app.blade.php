<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') - Sistem Penilaian Sumatif TP (Kurikulum Merdeka SMA)</title>
    
    <!-- Google Fonts: Plus Jakarta Sans & JetBrains Mono -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Chart.js for Interactive Visualizations -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-sans antialiased text-slate-800 bg-slate-50 flex flex-col md:flex-row">

    <!-- Mobile Sidebar Backdrop -->
    <div id="sidebar-backdrop" class="fixed inset-0 bg-slate-900/60 z-40 hidden md:hidden backdrop-blur-xs transition-opacity duration-300"></div>

    <!-- Sidebar Navigation -->
    <aside id="main-sidebar" class="fixed inset-y-0 left-0 z-50 w-72 bg-slate-900 text-slate-300 flex flex-col transition-transform duration-300 -translate-x-full md:translate-x-0 md:static md:inset-auto md:h-screen md:sticky md:top-0 border-r border-slate-800 shadow-xl">
        <!-- Brand Header -->
        <div class="h-16 px-6 flex items-center justify-between border-b border-slate-800/80 bg-slate-950/40">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 group">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-tr from-teal-600 to-emerald-400 text-white font-black text-lg shadow-md shadow-teal-500/20 group-hover:scale-105 transition-transform">
                    TP
                </div>
                <div>
                    <h1 class="text-sm font-black tracking-tight text-white leading-tight">PLA Merdeka</h1>
                    <p class="text-[11px] text-teal-400 font-medium">SMA Assessment Engine</p>
                </div>
            </a>
            <button id="close-sidebar-btn" type="button" aria-label="Tutup menu navigasi" class="md:hidden p-1.5 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800">
                <svg class="h-5 w-5" aria-hidden="true" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <!-- Navigation Menu -->
        <nav class="flex-1 overflow-y-auto px-4 py-6 space-y-1.5 text-sm custom-scrollbar">
            <div class="px-3 pb-2 text-[11px] font-bold uppercase tracking-wider text-slate-500">Utama</div>

            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium transition-colors {{ request()->routeIs('dashboard') ? 'bg-teal-600 text-white shadow-xs font-semibold' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                <svg class="h-5 w-5 {{ request()->routeIs('dashboard') ? 'text-white' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                Dashboard Analitik
            </a>

            <div class="px-3 pt-4 pb-2 text-[11px] font-bold uppercase tracking-wider text-slate-500">Master Kurikulum</div>

            <a href="{{ route('siswa.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium transition-colors {{ request()->routeIs('siswa.*') ? 'bg-teal-600 text-white shadow-xs font-semibold' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                <svg class="h-5 w-5 {{ request()->routeIs('siswa.*') ? 'text-white' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
                Data Siswa SMA
            </a>

            <a href="{{ route('matapelajaran.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium transition-colors {{ request()->routeIs('matapelajaran.*') ? 'bg-teal-600 text-white shadow-xs font-semibold' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                <svg class="h-5 w-5 {{ request()->routeIs('matapelajaran.*') ? 'text-white' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
                Mata Pelajaran
            </a>

            <a href="{{ route('tujuan-pembelajaran.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium transition-colors {{ request()->routeIs('tujuan-pembelajaran.*') ? 'bg-teal-600 text-white shadow-xs font-semibold' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                <svg class="h-5 w-5 {{ request()->routeIs('tujuan-pembelajaran.*') ? 'text-white' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                </svg>
                Lingkup Materi & TP
            </a>

            <div class="px-3 pt-4 pb-2 text-[11px] font-bold uppercase tracking-wider text-slate-500">Evaluasi & Asesmen</div>

            <a href="{{ route('penilaian.matrix') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium transition-colors {{ request()->routeIs('penilaian.matrix') ? 'bg-teal-600 text-white shadow-xs font-semibold' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                <svg class="h-5 w-5 {{ request()->routeIs('penilaian.matrix') ? 'text-white' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                </svg>
                <span class="flex-1">Matriks Nilai per TP</span>
                <span class="px-1.5 py-0.5 rounded text-[10px] font-bold {{ request()->routeIs('penilaian.matrix') ? 'bg-white/20 text-white' : 'bg-slate-800 text-slate-400' }}">Grid</span>
            </a>

            <a href="{{ route('penilaian.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium transition-colors {{ request()->routeIs('penilaian.index') || request()->routeIs('penilaian.create') || request()->routeIs('penilaian.edit') ? 'bg-teal-600 text-white shadow-xs font-semibold' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                <svg class="h-5 w-5 {{ request()->routeIs('penilaian.index') || request()->routeIs('penilaian.create') || request()->routeIs('penilaian.edit') ? 'text-white' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Data Penilaian Sumatif
            </a>

            <a href="{{ route('personalisasi.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium transition-colors {{ request()->routeIs('personalisasi.*') ? 'bg-teal-600 text-white shadow-xs font-semibold' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                <svg class="h-5 w-5 {{ request()->routeIs('personalisasi.*') ? 'text-white' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
                <span class="flex-1">Personalisasi AI</span>
                <span class="px-1.5 py-0.5 rounded text-[10px] font-bold {{ request()->routeIs('personalisasi.*') ? 'bg-white/20 text-white' : 'bg-slate-800 text-slate-400' }}">AI</span>
            </a>

            <a href="{{ route('laporan.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium transition-colors {{ request()->routeIs('laporan.*') ? 'bg-teal-600 text-white shadow-xs font-semibold' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                <svg class="h-5 w-5 {{ request()->routeIs('laporan.*') ? 'text-white' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Laporan & Raport
            </a>

            <div class="px-3 pt-4 pb-2 text-[11px] font-bold uppercase tracking-wider text-slate-500">Konfigurasi</div>

            <a href="{{ route('setting.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium transition-colors {{ request()->routeIs('setting.*') ? 'bg-teal-600 text-white shadow-xs font-semibold' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                <svg class="h-5 w-5 {{ request()->routeIs('setting.*') ? 'text-white' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                Pengaturan Sekolah & AI
            </a>
        </nav>

        <!-- User Profile Card in Sidebar Footer -->
        <div class="p-4 border-t border-slate-800 bg-slate-950/50">
            <div class="flex items-center gap-3">
                <div class="h-9 w-9 rounded-full bg-teal-500/20 border border-teal-400/30 flex items-center justify-center text-teal-300 font-bold text-sm">
                    G
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-semibold text-white truncate">Guru Pengampu</p>
                    <p class="text-[11px] text-slate-400 truncate">Kurikulum Merdeka</p>
                </div>
            </div>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
        <!-- Top Navbar -->
        <header class="h-16 bg-white/80 backdrop-blur-md border-b border-slate-200/80 sticky top-0 z-30 px-4 sm:px-8 flex items-center justify-between shadow-2xs">
            <div class="flex items-center gap-3">
                <button id="open-sidebar-btn" type="button" aria-label="Buka menu navigasi" class="md:hidden p-2 rounded-xl text-slate-600 hover:bg-slate-100 focus:outline-none">
                    <svg class="h-6 w-6" aria-hidden="true" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-teal-50 text-teal-700 border border-teal-200">
                        {{ \App\Models\Setting::get('nama_institusi', 'SMA Negeri 1 Merdeka') }}
                    </span>
                    <span class="text-xs text-slate-400 hidden sm:inline">&bull;</span>
                    <span class="text-xs text-slate-500 font-medium hidden sm:inline">
                        {{ str_replace('/', '-', \App\Models\Setting::get('tahun_akademik', '2025-2026 Ganjil')) }}
                    </span>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('penilaian.matrix') }}" class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg bg-teal-50 text-teal-700 hover:bg-teal-100 transition-colors border border-teal-200">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" /></svg>
                    Input Matriks TP
                </a>
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                    <span class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    KKM: {{ \App\Models\Setting::get('kkm_default', '75') }}
                </span>
            </div>
        </header>

        <!-- Main Body -->
        <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8 space-y-6">
            <!-- Flash Notification Alerts -->
            @if(session('success'))
                <div class="rounded-2xl bg-emerald-50 p-4 border border-emerald-200 text-emerald-800 flex items-start gap-3 shadow-xs transition-all duration-300">
                    <svg class="h-5 w-5 text-emerald-600 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div class="flex-1 text-sm font-medium">{{ session('success') }}</div>
                </div>
            @endif

            @if(session('error'))
                <div class="rounded-2xl bg-rose-50 p-4 border border-rose-200 text-rose-800 flex items-start gap-3 shadow-xs transition-all duration-300">
                    <svg class="h-5 w-5 text-rose-600 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div class="flex-1 text-sm font-medium">{{ session('error') }}</div>
                </div>
            @endif

            @if($errors->any())
                <div class="rounded-2xl bg-rose-50 p-4 border border-rose-200 text-rose-800 space-y-1 text-sm shadow-xs">
                    <p class="font-bold flex items-center gap-1.5">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                        Terdapat beberapa kesalahan pengisian data:
                    </p>
                    <ul class="list-disc list-inside space-y-0.5 text-xs text-rose-700 pl-2">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Content Slot -->
            @yield('content')
        </main>
    </div>

    <!-- Global MicroModal Alert Modal -->
    <div class="modal micromodal-slide" id="global-alert-modal" aria-hidden="true">
        <div class="modal__overlay" tabindex="-1" data-micromodal-close>
            <div class="modal__container rounded-3xl bg-white p-6 sm:p-8 shadow-2xl border border-slate-200" role="dialog" aria-modal="true" aria-labelledby="global-alert-modal-title">
                <div class="flex items-start gap-4">
                    <div id="global-alert-modal-icon" class="h-12 w-12 rounded-2xl bg-teal-50 text-teal-600 flex items-center justify-center border border-teal-100 shrink-0">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div class="flex-1 space-y-1">
                        <h3 class="modal__title text-lg font-bold text-slate-900" id="global-alert-modal-title">Pemberitahuan Sistem</h3>
                        <div class="modal__content text-sm text-slate-600 leading-relaxed" id="global-alert-modal-desc">
                            Operasi berhasil dijalankan.
                        </div>
                    </div>
                </div>
                <div class="modal__footer flex justify-end pt-4 border-t border-slate-100 mt-4">
                    <button type="button" class="px-5 py-2 text-sm font-semibold rounded-xl bg-slate-900 hover:bg-slate-800 text-white shadow-xs transition-colors" data-micromodal-close>
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Global MicroModal Confirm Modal -->
    <div class="modal micromodal-slide" id="global-confirm-modal" aria-hidden="true">
        <div class="modal__overlay" tabindex="-1" data-micromodal-close>
            <div class="modal__container rounded-3xl bg-white p-6 sm:p-8 shadow-2xl border border-slate-200" role="dialog" aria-modal="true" aria-labelledby="global-confirm-modal-title">
                <div class="flex items-start gap-4">
                    <div id="global-confirm-modal-icon" class="h-12 w-12 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center border border-rose-100 shrink-0">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                    </div>
                    <div class="flex-1 space-y-1">
                        <h3 class="modal__title text-lg font-bold text-slate-900" id="global-confirm-modal-title">Konfirmasi Aksi</h3>
                        <div class="modal__content text-sm text-slate-600 leading-relaxed" id="global-confirm-modal-desc">
                            Apakah Anda yakin ingin melanjutkan tindakan ini?
                        </div>
                    </div>
                </div>
                <div class="modal__footer flex items-center justify-end gap-2.5 pt-4 border-t border-slate-100 mt-4">
                    <button type="button" class="px-4 py-2 text-sm font-semibold rounded-xl text-slate-600 hover:bg-slate-100 transition-colors" data-micromodal-close>
                        Batal
                    </button>
                    <button type="button" id="global-confirm-modal-btn" class="px-5 py-2 text-sm font-semibold rounded-xl bg-rose-600 hover:bg-rose-700 text-white shadow-xs transition-colors">
                        Konfirmasi
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Script to Auto-Trigger MicroModal for Server Flash Messages -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            @if(session('success'))
                if (typeof window.showAlertModal === 'function') {
                    window.showAlertModal('Berhasil!', `{!! addslashes(session('success')) !!}`, 'success');
                }
            @elseif(session('error'))
                if (typeof window.showAlertModal === 'function') {
                    window.showAlertModal('Terjadi Kesalahan', `{!! addslashes(session('error')) !!}`, 'error');
                }
            @elseif((isset($errors) && $errors->any()) || (session('errors') && session('errors')->any()))
                @php
                    $activeErrors = (isset($errors) && $errors->any()) ? $errors : session('errors');
                @endphp
                if (typeof window.showAlertModal === 'function') {
                    const errorMsg = `<ul class="list-disc list-inside space-y-1 text-xs text-rose-700">@foreach($activeErrors->all() as $err)<li>{{ addslashes($err) }}</li>@endforeach</ul>`;
                    window.showAlertModal('Validasi Gagal', errorMsg, 'error');
                }
            @endif
        });
    </script>
</body>
</html>
