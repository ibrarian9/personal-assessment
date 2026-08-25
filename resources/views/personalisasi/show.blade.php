@extends('layouts.app')

@section('title', 'Diagnostik Personalisasi - ' . $siswa->nama)

@section('content')
<!-- Page Header -->
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-2 border-b border-slate-200/80">
    <div>
        <h2 class="text-2xl font-extrabold tracking-tight text-slate-900">Profil Personalisasi & Diagnostik TP</h2>
        <p class="text-sm text-slate-500 mt-1">Rekomendasi pembelajaran adaptif berbasis ketercapaian TP dan gaya belajar untuk <strong>{{ $siswa->nama }}</strong> ({{ $siswa->nisn }})</p>
    </div>
    <div class="flex items-center gap-3">
        <form action="{{ route('personalisasi.ai-analyze', $siswa) }}" method="POST" class="inline">
            @csrf
            <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-gradient-to-r from-teal-600 to-indigo-600 text-white text-sm font-semibold shadow-sm hover:from-teal-700 hover:to-indigo-700 transition-all focus:ring-2 focus:ring-teal-500 focus:ring-offset-2">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 00-2.456 2.456zM16.894 20.567L16.5 21.75l-.394-1.183a2.25 2.25 0 00-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 001.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 001.423 1.423l1.183.394-1.183.394a2.25 2.25 0 00-1.423 1.423z" />
                </svg>
                <span>Analisis Ulang dengan AI</span>
            </button>
        </form>
        <a href="{{ route('personalisasi.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold rounded-xl border border-slate-300 text-slate-700 bg-white hover:bg-slate-50 transition-colors shadow-xs">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" /></svg>
            <span>Kembali ke Daftar</span>
        </a>
    </div>
</div>

<!-- Overview Row -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Siswa Profile -->
    <div class="rounded-2xl bg-white p-6 shadow-xs border border-slate-200/80 space-y-4">
        <div class="pb-3 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-base font-bold text-slate-900">Identitas Siswa</h3>
            <span class="text-xs text-slate-400 font-mono">{{ $siswa->nisn }}</span>
        </div>
        <table class="w-full text-left text-sm text-slate-700">
            <tbody class="divide-y divide-slate-100">
                <tr>
                    <td class="py-2.5 px-2 font-semibold text-slate-400 w-1/3">NISN</td>
                    <td class="py-2.5 px-2 font-mono font-bold text-slate-900">{{ $siswa->nisn }}</td>
                </tr>
                <tr>
                    <td class="py-2.5 px-2 font-semibold text-slate-400">Nama Siswa</td>
                    <td class="py-2.5 px-2 font-semibold text-slate-900">{{ $siswa->nama }}</td>
                </tr>
                <tr>
                    <td class="py-2.5 px-2 font-semibold text-slate-400">Kelas SMA</td>
                    <td class="py-2.5 px-2 font-medium text-slate-800">{{ $siswa->kelas }}</td>
                </tr>
                <tr>
                    <td class="py-2.5 px-2 font-semibold text-slate-400">Gaya Belajar (VARK)</td>
                    <td class="py-2.5 px-2">
                        <span class="inline-flex items-center px-3 py-1 rounded-md text-xs font-bold bg-purple-100 text-purple-800">
                            {{ $siswa->gaya_belajar }}
                        </span>
                    </td>
                </tr>
                <tr>
                    <td class="py-2.5 px-2 font-semibold text-slate-400">Status Ketuntasan</td>
                    <td class="py-2.5 px-2">
                        <span class="inline-flex items-center px-3 py-0.5 rounded-full text-xs font-bold {{ $siswa->jumlah_tp_belum_tuntas == 0 ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                            {{ $siswa->jumlah_tp_tuntas }} Tuntas / {{ $siswa->jumlah_tp_belum_tuntas }} Perlu Remedial
                        </span>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Status Engine AI Diagnostik -->
    <div class="rounded-2xl bg-gradient-to-br from-white via-teal-50/40 to-teal-100/30 p-6 shadow-xs border border-teal-200/80 space-y-4 flex flex-col justify-between">
        <div>
            <div class="pb-3 border-b border-teal-200/50 flex items-center justify-between">
                <h3 class="text-base font-bold text-slate-900">Status Diagnostik AI</h3>
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-teal-100 text-teal-800 border border-teal-200">
                    <svg class="h-3.5 w-3.5 text-teal-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z" /></svg>
                    <span>AI Powered</span>
                </span>
            </div>
            <div class="flex items-center justify-between py-4">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Rata-Rata Capaian Sumatif</span>
                    <div class="text-4xl font-extrabold text-teal-700 mt-1 font-mono">
                        {{ $personalisasi->skor_performa ?? $siswa->rata_rata_nilai }}
                    </div>
                </div>
                <div>
                    <span class="inline-flex items-center px-3.5 py-1.5 rounded-xl text-sm font-bold shadow-xs {{ ($personalisasi->kategori_pemahaman ?? '') === 'Tinggi' ? 'bg-emerald-100 text-emerald-800 border border-emerald-300' : (($personalisasi->kategori_pemahaman ?? '') === 'Sedang' ? 'bg-blue-100 text-blue-800 border border-blue-300' : 'bg-rose-100 text-rose-800 border border-rose-300') }}">
                        Kategori: {{ $personalisasi->kategori_pemahaman ?? 'Sedang' }}
                    </span>
                </div>
            </div>
        </div>

        <div class="rounded-xl border-l-4 border-teal-600 bg-white/95 p-4 text-xs text-slate-700 shadow-2xs space-y-1">
            <h5 class="font-bold text-teal-900">Ulasan Evaluasi Diagnostik Guru:</h5>
            <p class="leading-relaxed text-slate-600">
                {{ $personalisasi->catatan_guru ?? 'Analisis performa dihasilkan secara otomatis berdasarkan ketercapaian Tujuan Pembelajaran dan karakteristik modalitas belajar siswa SMA.' }}
            </p>
        </div>
    </div>
</div>

<!-- Remedial TP Alert Box (If any) -->
@if(count($remedialTps) > 0)
<div class="rounded-2xl bg-rose-50 border border-rose-200 p-6 space-y-3">
    <div class="flex items-center gap-2 text-rose-800 font-bold text-base">
        <svg class="h-5 w-5 text-rose-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
        </svg>
        Daftar Tujuan Pembelajaran (TP) yang Memerlukan Bimbingan Remedial:
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
        @foreach($remedialTps as $rem)
            <div class="rounded-xl bg-white p-3 border border-rose-200 text-xs font-semibold text-rose-900 flex items-start gap-2 shadow-2xs">
                <span class="h-2 w-2 rounded-full bg-rose-500 mt-1.5 shrink-0"></span>
                <span>{{ $rem }}</span>
            </div>
        @endforeach
    </div>
</div>
@endif

<!-- Recommendations Section (Targeted Materials & Adaptive Strategy) -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Rekomendasi Materi Remedial / Pengayaan -->
    <div class="rounded-2xl bg-white p-6 shadow-xs border border-slate-200/80 space-y-3">
        <div class="pb-3 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-base font-bold text-slate-900">Rekomendasi Bahan Ajar & Modul Terarah</h3>
            <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-semibold bg-purple-100 text-purple-800">
                Gaya {{ $siswa->gaya_belajar }}
            </span>
        </div>
        <p class="text-sm text-slate-700 leading-relaxed">
            {{ $personalisasi->rekomendasi_materi ?? 'Belum ada rekomendasi materi yang tergenerate.' }}
        </p>
    </div>

    <!-- Rekomendasi Aktivitas & Intervensi -->
    <div class="rounded-2xl bg-white p-6 shadow-xs border border-slate-200/80 space-y-3">
        <div class="pb-3 border-b border-slate-100">
            <h3 class="text-base font-bold text-slate-900">Strategi Aktivitas Pembelajaran Adaptif</h3>
        </div>
        <div class="text-sm text-slate-700 leading-relaxed whitespace-pre-line">{{ trim($personalisasi->rekomendasi_aktivitas ?? '• Jaga konsistensi pengerjaan tugas dan latihan sumatif TP.') }}</div>
    </div>
</div>

<!-- Assessment History Table -->
<div class="rounded-2xl bg-white p-6 shadow-xs border border-slate-200/80 space-y-4">
    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
        <div>
            <h3 class="text-base font-bold text-slate-900">Riwayat Nilai per Tujuan Pembelajaran (TP)</h3>
            <p class="text-xs text-slate-500">Seluruh butir TP yang dinilai pada Kurikulum Merdeka</p>
        </div>
        <a href="{{ route('penilaian.matrix') }}" class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold rounded-lg bg-teal-600 text-white hover:bg-teal-700 transition-colors shadow-xs">
            Edit Nilai Matriks &rarr;
        </a>
    </div>
    <div class="overflow-x-auto rounded-xl border border-slate-200">
        <table class="w-full text-left text-sm text-slate-600">
            <thead class="bg-slate-50 text-xs uppercase font-semibold text-slate-500 border-b border-slate-200">
                <tr>
                    <th class="py-3 px-3">Lingkup Materi</th>
                    <th class="py-3 px-3">Kode TP</th>
                    <th class="py-3 px-4">Deskripsi TP</th>
                    <th class="py-3 px-2 text-center">Target KKM</th>
                    <th class="py-3 px-3 text-center">Nilai</th>
                    <th class="py-3 px-3 text-center">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($siswa->nilaiSumatifTps as $p)
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <td class="py-3 px-3 font-semibold text-slate-900">
                            {{ $p->tujuanPembelajaran->lingkupMateri->kode_lm ?? '-' }}
                        </td>
                        <td class="py-3 px-3 font-mono font-bold text-teal-800">{{ $p->tujuanPembelajaran->kode_tp ?? '-' }}</td>
                        <td class="py-3 px-4 text-xs text-slate-700 leading-relaxed">{{ $p->tujuanPembelajaran->deskripsi_tp ?? '-' }}</td>
                        <td class="py-3 px-2 text-center font-mono">{{ $p->tujuanPembelajaran->target_ketercapaian ?? 75 }}</td>
                        <td class="py-3 px-3 text-center font-mono font-extrabold text-base {{ $p->status_ketuntasan === 'Tuntas' ? 'text-teal-700' : 'text-rose-600' }}">
                            {{ $p->nilai }}
                        </td>
                        <td class="py-3 px-3 text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $p->status_ketuntasan === 'Tuntas' ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                                {{ $p->status_ketuntasan }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-6 text-center text-slate-400">Belum ada nilai TP yang tercatat.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Form Catatan Intervensi Guru -->
<div class="rounded-2xl bg-white p-6 shadow-xs border border-slate-200/80 space-y-4">
    <div class="pb-3 border-b border-slate-100">
        <h3 class="text-base font-bold text-slate-900">Catatan Diagnostik & Rencana Intervensi Guru</h3>
    </div>
    <form action="{{ route('personalisasi.catatan', $siswa) }}" method="POST" class="space-y-4">
        @csrf
        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-1.5" for="catatan_guru">
                Catatan Bimbingan / Rekomendasi Tindak Lanjut Guru:
            </label>
            <textarea name="catatan_guru" id="catatan_guru" class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-slate-300 bg-white placeholder-slate-400 focus:border-teal-500 focus:outline-none focus:ring-2 focus:ring-teal-500/20 transition-all" rows="3" placeholder="Tuliskan arahan bimbingan akademik, penugasan remedial per-TP, atau materi pengayaan...">{{ old('catatan_guru', $personalisasi->catatan_guru) }}</textarea>
        </div>
        <button type="submit" class="px-5 py-2.5 rounded-xl bg-teal-600 text-white text-sm font-semibold hover:bg-teal-700 transition-colors shadow-sm focus:ring-2 focus:ring-teal-500 focus:ring-offset-2">
            Simpan Catatan Guru
        </button>
    </form>
</div>
@endsection
