@extends('layouts.app')

@section('title', 'Dashboard Analitik TP')

@section('content')
<!-- Page Header -->
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-2 border-b border-slate-200/80">
    <div>
        <h2 class="text-2xl font-extrabold tracking-tight text-slate-900">Dashboard Analitik Kurikulum Merdeka</h2>
        <p class="text-sm text-slate-500 mt-1">Pemantauan ketercapaian Tujuan Pembelajaran (TP), capaian sumatif siswa SMA, dan engine personalisasi pembelajaran</p>
    </div>
    <div class="flex items-center gap-3">
        <a href="{{ route('penilaian.matrix') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-teal-600 text-white text-sm font-semibold shadow-sm hover:bg-teal-700 transition-colors focus:ring-2 focus:ring-teal-500 focus:ring-offset-2">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
            </svg>
            Buka Matriks Penilaian per TP
        </a>
    </div>
</div>

<!-- Key Performance Stat Cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
    <!-- Stat 1: Total Siswa -->
    <div class="rounded-2xl bg-white p-6 shadow-xs border border-slate-200/80 hover:border-slate-300 transition-colors">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Total Siswa SMA</p>
                <h3 class="text-3xl font-extrabold text-slate-900 mt-2">{{ $totalSiswa }} <span class="text-sm font-normal text-slate-400">Siswa</span></h3>
            </div>
            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-teal-50 text-teal-600 border border-teal-100">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
            </div>
        </div>
        <p class="mt-3 text-xs text-slate-500 font-medium">Kelas X-A &bull; Kurikulum Merdeka</p>
    </div>

    <!-- Stat 2: Total TP -->
    <div class="rounded-2xl bg-white p-6 shadow-xs border border-slate-200/80 hover:border-slate-300 transition-colors">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Tujuan Pembelajaran</p>
                <h3 class="text-3xl font-extrabold text-slate-900 mt-2">{{ $totalTp }} <span class="text-sm font-normal text-slate-400">TP</span></h3>
            </div>
            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-blue-50 text-blue-600 border border-blue-100">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                </svg>
            </div>
        </div>
        <p class="mt-3 text-xs text-slate-500 font-medium">10 Lingkup Materi (Materi 1..10)</p>
    </div>

    <!-- Stat 3: Rata-Rata Capaian Sumatif -->
    <div class="rounded-2xl bg-white p-6 shadow-xs border border-slate-200/80 hover:border-slate-300 transition-colors">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Rata-Rata Sumatif</p>
                <h3 class="text-3xl font-extrabold text-teal-700 mt-2 font-mono">{{ $rataRataFormatted }}</h3>
            </div>
            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600 border border-emerald-100">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
            </div>
        </div>
        <p class="mt-3 text-xs text-emerald-700 font-medium">Skala 0 - 100 &bull; KKM 75.00</p>
    </div>

    <!-- Stat 4: Ketuntasan TP -->
    <div class="rounded-2xl bg-white p-6 shadow-xs border border-slate-200/80 hover:border-slate-300 transition-colors">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Ketuntasan TP</p>
                <h3 class="text-3xl font-extrabold text-emerald-600 mt-2 font-mono">{{ $persentaseKetuntasan }}%</h3>
            </div>
            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-purple-50 text-purple-600 border border-purple-100">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </div>
        <p class="mt-3 text-xs text-slate-500 font-medium"><strong>{{ $totalTuntas }}</strong> Tuntas &bull; <strong class="text-rose-600">{{ $totalBelumTuntas }}</strong> Remedial</p>
    </div>
</div>

<!-- Interactive Chart Section -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Chart 1: Kategori Pemahaman Siswa -->
    <div class="rounded-2xl bg-white p-6 shadow-xs border border-slate-200/80 space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <div>
                <h3 class="text-base font-bold text-slate-900">Distribusi Kategori Pemahaman Siswa</h3>
                <p class="text-xs text-slate-500">Klasifikasi ketercapaian kompetensi TP kelas</p>
            </div>
            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-slate-100 text-slate-600">
                AI Diagnostic
            </span>
        </div>
        <div class="h-64 flex items-center justify-center">
            <canvas id="kategoriChart"></canvas>
        </div>
    </div>

    <!-- Chart 2: Modalitas Gaya Belajar VARK -->
    <div class="rounded-2xl bg-white p-6 shadow-xs border border-slate-200/80 space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <div>
                <h3 class="text-base font-bold text-slate-900">Modalitas Gaya Belajar Siswa (VARK)</h3>
                <p class="text-xs text-slate-500">Distribusi profil modalitas untuk diferensiasi pembelajaran</p>
            </div>
            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-purple-100 text-purple-800">
                Kurikulum Merdeka
            </span>
        </div>
        <div class="h-64 flex items-center justify-center">
            <canvas id="varkChart"></canvas>
        </div>
    </div>
</div>

<!-- Two Column Section: Recent Students & At-Risk Remedial -->
<div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
    <!-- Left: Data Capaian Siswa Terbaru (8 cols) -->
    <div class="lg:col-span-8 rounded-2xl bg-white p-6 shadow-xs border border-slate-200/80 space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <div>
                <h3 class="text-base font-bold text-slate-900">Rekap Capaian Siswa & Ketercapaian TP</h3>
                <p class="text-xs text-slate-500">Daftar siswa beserta rata-rata capaian sumatif dan status TP</p>
            </div>
            <a href="{{ route('penilaian.matrix') }}" class="inline-flex items-center text-xs font-semibold text-teal-600 hover:text-teal-700">
                Lihat Matriks Lengkap
            </a>
        </div>
        
        <div class="overflow-x-auto rounded-xl border border-slate-200">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-xs uppercase font-semibold text-slate-500 border-b border-slate-200">
                    <tr>
                        <th class="py-3 px-4">Nama Siswa</th>
                        <th class="py-3 px-4">NISN</th>
                        <th class="py-3 px-4 text-center">Gaya Belajar</th>
                        <th class="py-3 px-4 text-center">Rata-Rata</th>
                        <th class="py-3 px-4 text-center">Ketuntasan TP</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($siswas as $s)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-3 px-4">
                                <strong class="font-semibold text-slate-900 block">{{ $s->nama }}</strong>
                                <small class="text-slate-400 text-xs">{{ $s->kelas }}</small>
                            </td>
                            <td class="py-3 px-4 font-mono text-xs font-bold text-slate-700">{{ $s->nisn }}</td>
                            <td class="py-3 px-4 text-center">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-purple-100 text-purple-800">
                                    {{ str_replace(['Baca/Tulis', '/'], ['Baca dan Tulis', ' dan '], $s->gaya_belajar) }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-center font-mono font-extrabold text-teal-700">
                                {{ $s->rata_rata_nilai }}
                            </td>
                            <td class="py-3 px-4 text-center">
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $s->jumlah_tp_belum_tuntas == 0 ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                                    {{ $s->jumlah_tp_tuntas }} Tuntas, {{ $s->jumlah_tp_belum_tuntas }} Remedial
                                </span>
                            </td>
                            <td class="py-3 px-4 text-center">
                                <a href="{{ route('personalisasi.show', $s->id) }}" class="inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-lg bg-slate-100 text-slate-700 hover:bg-slate-200 transition-colors">
                                    Profil AI
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-6 text-center text-slate-400">Belum ada data nilai. Silakan isi melalui menu Matriks Nilai.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Right: Siswa Perlu Remedial TP (4 cols) -->
    <div class="lg:col-span-4 rounded-2xl bg-white p-6 shadow-xs border border-slate-200/80 space-y-4">
        <div class="pb-3 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="text-base font-bold text-slate-900">Perlu Bimbingan Remedial</h3>
                <p class="text-xs text-slate-500">Siswa dengan TP belum tuntas</p>
            </div>
            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-rose-100 text-rose-800">
                At-Risk
            </span>
        </div>

        <div class="space-y-3">
            @forelse($atRiskStudents as $ar)
                <div class="rounded-xl border border-rose-100 bg-rose-50/50 p-3.5 space-y-2 hover:bg-rose-50 transition-colors">
                    <div class="flex items-start justify-between">
                        <div>
                            <h4 class="text-sm font-bold text-slate-900">{{ $ar->siswa->nama ?? '-' }}</h4>
                            <p class="text-xs text-slate-500 font-mono">NISN: {{ $ar->siswa->nisn ?? '-' }} &bull; {{ $ar->siswa->kelas ?? '-' }}</p>
                        </div>
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-rose-100 text-rose-800 border border-rose-200">
                            {{ $ar->tp_belum_tuntas_count }} TP Remedial
                        </span>
                    </div>
                    <p class="text-xs text-slate-600 leading-relaxed line-clamp-2">
                        {{ str_replace(['Baca/Tulis', '/TP', 'per/TP', 'per-TP', ' / ', '/'], ['Baca dan Tulis', ' TP', 'per TP', 'per TP', ' atau ', ' atau '], $ar->rekomendasi_materi ?? 'Perlu bimbingan dan tes formatif ulang untuk butir TP terkait.') }}
                    </p>
                    <div class="pt-1 flex items-center justify-between text-xs">
                        <span class="text-slate-400">Modalitas: <strong>{{ str_replace(['Baca/Tulis', '/'], ['Baca dan Tulis', ' dan '], $ar->siswa->gaya_belajar ?? 'Visual') }}</strong></span>
                        <a href="{{ route('personalisasi.show', $ar->siswa_id) }}" class="inline-flex items-center font-semibold text-teal-700 hover:text-teal-800">
                            Rencana Remedial
                        </a>
                    </div>
                </div>
            @empty
                <div class="py-8 text-center text-xs text-slate-400">
                    Seluruh siswa telah mencapai target ketuntasan Tujuan Pembelajaran.
                </div>
            @endforelse
        </div>
    </div>
</div>

<!-- Chart initialization script -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Chart 1: Kategori Pemahaman
        const ctxKategori = document.getElementById('kategoriChart');
        if (ctxKategori) {
            new Chart(ctxKategori, {
                type: 'doughnut',
                data: {
                    labels: ['Tinggi (Pengayaan)', 'Sedang (Optimal)', 'Perlu Pendampingan'],
                    datasets: [{
                        data: [
                            {{ $kategoriDistribution['Tinggi'] }},
                            {{ $kategoriDistribution['Sedang'] }},
                            {{ $kategoriDistribution['Perlu Pendampingan'] }}
                        ],
                        backgroundColor: ['#10b981', '#3b82f6', '#f43f5e'],
                        borderWidth: 2,
                        borderColor: '#ffffff',
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom' }
                    }
                }
            });
        }

        // Chart 2: VARK
        const ctxVark = document.getElementById('varkChart');
        if (ctxVark) {
            new Chart(ctxVark, {
                type: 'bar',
                data: {
                    labels: ['Visual', 'Auditori', 'Kinestetik', 'Baca dan Tulis'],
                    datasets: [{
                        label: 'Jumlah Siswa',
                        data: [
                            {{ $varkDistribution['Visual'] }},
                            {{ $varkDistribution['Auditori'] }},
                            {{ $varkDistribution['Kinestetik'] }},
                            {{ $varkDistribution['Baca/Tulis'] }}
                        ],
                        backgroundColor: ['#8b5cf6', '#06b6d4', '#f59e0b', '#10b981'],
                        borderRadius: 8,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { precision: 0 }
                        }
                    }
                }
            });
        }
    });
</script>
@endsection
