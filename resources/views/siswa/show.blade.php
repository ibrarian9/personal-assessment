@extends('layouts.app')

@section('title', 'Detail Siswa - ' . $siswa->nama)

@section('content')
<!-- Page Header -->
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-2 border-b border-slate-200/80">
    <div>
        <h2 class="text-2xl font-extrabold tracking-tight text-slate-900">Detail Capaian Siswa</h2>
        <p class="text-sm text-slate-500 mt-1">Profil komprehensif, riwayat capaian per TP, dan diagnostik personalisasi <strong>{{ $siswa->nama }}</strong></p>
    </div>
    <div class="flex items-center gap-3">
        <a href="{{ route('personalisasi.show', $siswa) }}" class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold rounded-xl bg-purple-600 text-white hover:bg-purple-700 transition-colors shadow-xs">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 00-2.456 2.456zM16.894 20.567L16.5 21.75l-.394-1.183a2.25 2.25 0 00-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 001.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 001.423 1.423l1.183.394-1.183.394a2.25 2.25 0 00-1.423 1.423z" /></svg>
            <span>Profil Personalisasi AI</span>
        </a>
        <a href="{{ route('siswa.index') }}" class="inline-flex items-center px-4 py-2 text-sm font-semibold rounded-xl border border-slate-300 text-slate-700 bg-white hover:bg-slate-50 transition-colors shadow-xs">
            Kembali
        </a>
    </div>
</div>

<!-- Overview Profile & Performance Summary Grid -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Siswa Identity Card -->
    <div class="lg:col-span-1 rounded-2xl bg-white p-6 shadow-xs border border-slate-200/80 space-y-4">
        <div class="pb-3 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-base font-bold text-slate-900">Identitas Siswa</h3>
            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold {{ $siswa->status === 'Aktif' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600' }}">
                {{ $siswa->status }}
            </span>
        </div>
        <table class="w-full text-left text-sm text-slate-700">
            <tbody class="divide-y divide-slate-100">
                <tr>
                    <td class="py-2.5 px-1 font-semibold text-slate-400">NISN</td>
                    <td class="py-2.5 px-1 font-mono font-bold text-slate-900">{{ $siswa->nisn }}</td>
                </tr>
                <tr>
                    <td class="py-2.5 px-1 font-semibold text-slate-400">Nama Lengkap</td>
                    <td class="py-2.5 px-1 font-semibold text-slate-900">{{ $siswa->nama }}</td>
                </tr>
                <tr>
                    <td class="py-2.5 px-1 font-semibold text-slate-400">Kelas SMA</td>
                    <td class="py-2.5 px-1 font-medium text-slate-800">{{ $siswa->kelas }}</td>
                </tr>
                <tr>
                    <td class="py-2.5 px-1 font-semibold text-slate-400">Gaya Belajar (VARK)</td>
                    <td class="py-2.5 px-1">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-bold bg-purple-100 text-purple-800">
                            {{ str_replace(['Baca/Tulis', '/'], ['Baca dan Tulis', ' dan '], $siswa->gaya_belajar) }}
                        </span>
                    </td>
                </tr>
                <tr>
                    <td class="py-2.5 px-1 font-semibold text-slate-400">Email</td>
                    <td class="py-2.5 px-1 text-slate-600 text-xs">{{ $siswa->email ?? '-' }}</td>
                </tr>
            </tbody>
        </table>
        <div class="pt-2">
            <a href="{{ route('siswa.edit', $siswa) }}" class="w-full inline-flex justify-center items-center gap-1.5 px-3 py-2 text-xs font-semibold rounded-xl border border-slate-300 text-slate-700 hover:bg-slate-50 transition-colors">
                Edit Data Siswa
            </a>
        </div>
    </div>

    <!-- Performance & TP Stats Card -->
    <div class="lg:col-span-2 rounded-2xl bg-gradient-to-br from-white via-teal-50/30 to-teal-100/20 p-6 shadow-xs border border-teal-200/80 space-y-6 flex flex-col justify-between">
        <div>
            <div class="pb-3 border-b border-teal-200/50 flex items-center justify-between">
                <h3 class="text-base font-bold text-slate-900">Rangkuman Ketercapaian TP</h3>
                <span class="text-xs font-semibold text-teal-800">KKM: 75.00</span>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-4">
                <div class="rounded-xl bg-white p-4 border border-teal-100 shadow-2xs">
                    <span class="text-xs font-bold uppercase text-slate-400">Rata-Rata Sumatif</span>
                    <div class="text-3xl font-extrabold text-teal-700 mt-1 font-mono">{{ $siswa->rata_rata_nilai }}</div>
                </div>
                <div class="rounded-xl bg-white p-4 border border-emerald-100 shadow-2xs">
                    <span class="text-xs font-bold uppercase text-emerald-600">TP Tuntas (&ge; 75)</span>
                    <div class="text-3xl font-extrabold text-emerald-600 mt-1 font-mono">{{ $siswa->jumlah_tp_tuntas }} <span class="text-xs font-normal text-slate-400">TP</span></div>
                </div>
                <div class="rounded-xl bg-white p-4 border border-rose-100 shadow-2xs">
                    <span class="text-xs font-bold uppercase text-rose-600">Perlu Remedial (< 75)</span>
                    <div class="text-3xl font-extrabold text-rose-600 mt-1 font-mono">{{ $siswa->jumlah_tp_belum_tuntas }} <span class="text-xs font-normal text-slate-400">TP</span></div>
                </div>
            </div>
        </div>

        @php $pers = $siswa->personalisasis->first(); @endphp
        <div class="rounded-xl border-l-4 border-teal-600 bg-white/95 p-4 text-xs text-slate-700 shadow-2xs space-y-1.5">
            <div class="flex items-center justify-between">
                <h4 class="font-bold text-teal-900">Catatan Diagnostik Guru dan AI:</h4>
                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold {{ ($pers->kategori_pemahaman ?? '') === 'Tinggi' ? 'bg-emerald-100 text-emerald-800' : (($pers->kategori_pemahaman ?? '') === 'Sedang' ? 'bg-blue-100 text-blue-800' : 'bg-rose-100 text-rose-800') }}">
                    Kategori: {{ $pers->kategori_pemahaman ?? 'Sedang' }}
                </span>
            </div>
            <p class="text-slate-600 leading-relaxed">
                {{ str_replace(['Baca/Tulis', '/'], ['Baca dan Tulis', ' atau '], $pers->catatan_guru ?? 'Analisis performa dihasilkan secara otomatis berdasarkan ketercapaian Tujuan Pembelajaran dan modalitas gaya belajar.') }}
            </p>
        </div>
    </div>
</div>

<!-- Detailed per-TP Assessment Table -->
<div class="rounded-2xl bg-white p-6 shadow-xs border border-slate-200/80 space-y-4">
    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
        <div>
            <h3 class="text-base font-bold text-slate-900">Daftar Nilai per Tujuan Pembelajaran (TP)</h3>
            <p class="text-xs text-slate-500">Rincian nilai sumatif per butir indikator Tujuan Pembelajaran</p>
        </div>
        <a href="{{ route('penilaian.matrix') }}" class="inline-flex items-center px-3.5 py-1.5 text-xs font-semibold rounded-lg bg-teal-600 text-white hover:bg-teal-700 transition-colors shadow-xs">
            Edit di Matriks Nilai
        </a>
    </div>

    <div class="overflow-x-auto rounded-xl border border-slate-200">
        <table class="w-full text-left text-sm text-slate-600">
            <thead class="bg-slate-50 text-xs uppercase font-semibold text-slate-500 border-b border-slate-200">
                <tr>
                    <th class="py-3.5 px-4 w-36">Lingkup Materi</th>
                    <th class="py-3.5 px-4 w-28">Kode TP</th>
                    <th class="py-3.5 px-4">Deskripsi Tujuan Pembelajaran</th>
                    <th class="py-3.5 px-4 text-center w-28">Target KKM</th>
                    <th class="py-3.5 px-4 text-center w-28">Nilai Sumatif</th>
                    <th class="py-3.5 px-4 text-center w-36">Status Ketuntasan</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($siswa->nilaiSumatifTps as $nst)
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <td class="py-3.5 px-4 font-semibold text-slate-900">
                            {{ $nst->tujuanPembelajaran->lingkupMateri->kode_lm ?? '-' }}
                            <span class="block text-xs font-normal text-slate-400">{{ $nst->tujuanPembelajaran->lingkupMateri->judul_materi ?? '' }}</span>
                        </td>
                        <td class="py-3.5 px-4 font-mono font-bold text-teal-800">
                            {{ $nst->tujuanPembelajaran->kode_tp ?? '-' }}
                        </td>
                        <td class="py-3.5 px-4 text-xs leading-relaxed space-y-1.5">
                            <div class="text-slate-800 font-medium leading-relaxed">
                                {{ $nst->tujuanPembelajaran->deskripsi_tp ?? '-' }}
                            </div>
                            @if($nst->catatan)
                                @php
                                    $catatanLines = array_filter(array_map('trim', explode("\n", $nst->catatan)));
                                @endphp
                                <div class="p-2.5 rounded-lg border {{ $nst->status_ketuntasan === 'Tuntas' ? 'bg-emerald-50/60 border-emerald-200/70 text-emerald-950' : 'bg-rose-50/70 border-rose-200 text-rose-950' }} text-[11px] leading-relaxed space-y-1">
                                    <span class="font-bold block {{ $nst->status_ketuntasan === 'Tuntas' ? 'text-emerald-800' : 'text-rose-800' }}">3 Rekomendasi Tindak Lanjut:</span>
                                    <div class="space-y-1 text-slate-700 font-normal">
                                        @foreach($catatanLines as $line)
                                            <p>{{ str_replace(['Baca/Tulis', '/'], ['Baca dan Tulis', ' atau '], $line) }}</p>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </td>
                        <td class="py-3.5 px-4 text-center font-mono text-slate-500">
                            {{ $nst->tujuanPembelajaran->target_ketercapaian ?? 75 }}
                        </td>
                        <td class="py-3.5 px-4 text-center font-mono font-extrabold text-base {{ $nst->status_ketuntasan === 'Tuntas' ? 'text-teal-700' : 'text-rose-600' }}">
                            {{ $nst->nilai }}
                        </td>
                        <td class="py-3.5 px-4 text-center">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $nst->status_ketuntasan === 'Tuntas' ? 'bg-emerald-100 text-emerald-800 border border-emerald-200' : 'bg-rose-100 text-rose-800 border border-rose-200' }}">
                                {{ $nst->status_ketuntasan }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-8 text-center text-slate-400">Belum ada data nilai TP yang tercatat.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
