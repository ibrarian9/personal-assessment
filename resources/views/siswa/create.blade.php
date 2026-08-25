@extends('layouts.app')

@section('title', 'Tambah Siswa Baru')

@section('content')
<!-- Page Header -->
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-2 border-b border-slate-200/80">
    <div>
        <h2 class="text-2xl font-extrabold tracking-tight text-slate-900">Tambah Siswa Baru</h2>
        <p class="text-sm text-slate-500 mt-1">Registrasi identitas siswa SMA, kelas, dan asesmen diagnostik awal gaya belajar VARK</p>
    </div>
    <div>
        <a href="{{ route('siswa.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold rounded-xl border border-slate-300 text-slate-700 bg-white hover:bg-slate-50 transition-colors shadow-xs">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" /></svg>
            <span>Kembali</span>
        </a>
    </div>
</div>

<div class="max-w-3xl rounded-2xl bg-white p-6 sm:p-8 shadow-xs border border-slate-200/80">
    <form action="{{ route('siswa.store') }}" method="POST" class="space-y-6">
        @csrf
        
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5" for="nisn">Nomor Induk Siswa Nasional (NISN) *</label>
                <input type="text" name="nisn" id="nisn" class="w-full px-3.5 py-2 text-sm rounded-xl border border-slate-300 bg-white placeholder-slate-400 focus:border-teal-500 focus:outline-none focus:ring-2 focus:ring-teal-500/20 transition-all font-mono" value="{{ old('nisn') }}" required placeholder="Contoh: 3144333149">
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5" for="nama">Nama Lengkap Siswa *</label>
                <input type="text" name="nama" id="nama" class="w-full px-3.5 py-2 text-sm rounded-xl border border-slate-300 bg-white placeholder-slate-400 focus:border-teal-500 focus:outline-none focus:ring-2 focus:ring-teal-500/20 transition-all" value="{{ old('nama') }}" required placeholder="Nama lengkap siswa">
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5" for="kelas">Kelas SMA *</label>
                <select name="kelas" id="kelas" class="w-full px-3.5 py-2 text-sm rounded-xl border border-slate-300 bg-white text-slate-700 focus:border-teal-500 focus:outline-none focus:ring-2 focus:ring-teal-500/20 transition-all" required>
                    <option value="X-A" {{ old('kelas', 'X-A') == 'X-A' ? 'selected' : '' }}>Kelas X-A (Fase E)</option>
                    <option value="X-B" {{ old('kelas') == 'X-B' ? 'selected' : '' }}>Kelas X-B (Fase E)</option>
                    <option value="X-C" {{ old('kelas') == 'X-C' ? 'selected' : '' }}>Kelas X-C (Fase E)</option>
                    <option value="XI-IPA 1" {{ old('kelas') == 'XI-IPA 1' ? 'selected' : '' }}>Kelas XI-IPA 1 (Fase F)</option>
                    <option value="XI-IPA 2" {{ old('kelas') == 'XI-IPA 2' ? 'selected' : '' }}>Kelas XI-IPA 2 (Fase F)</option>
                    <option value="XI-IPS 1" {{ old('kelas') == 'XI-IPS 1' ? 'selected' : '' }}>Kelas XI-IPS 1 (Fase F)</option>
                    <option value="XII-IPA" {{ old('kelas') == 'XII-IPA' ? 'selected' : '' }}>Kelas XII-IPA (Fase F)</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5" for="email">Email Siswa / Wali</label>
                <input type="email" name="email" id="email" class="w-full px-3.5 py-2 text-sm rounded-xl border border-slate-300 bg-white placeholder-slate-400 focus:border-teal-500 focus:outline-none focus:ring-2 focus:ring-teal-500/20 transition-all" value="{{ old('email') }}" placeholder="siswa@sman1merdeka.sch.id">
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5" for="gaya_belajar">Modalitas Gaya Belajar (VARK) *</label>
                <select name="gaya_belajar" id="gaya_belajar" class="w-full px-3.5 py-2 text-sm rounded-xl border border-slate-300 bg-white text-slate-700 focus:border-teal-500 focus:outline-none focus:ring-2 focus:ring-teal-500/20 transition-all" required>
                    <option value="Visual" {{ old('gaya_belajar', 'Visual') == 'Visual' ? 'selected' : '' }}>Visual (Grafis, Diagram, Animasi)</option>
                    <option value="Auditori" {{ old('gaya_belajar') == 'Auditori' ? 'selected' : '' }}>Auditori (Diskusi, Penjelasan Suara)</option>
                    <option value="Kinestetik" {{ old('gaya_belajar') == 'Kinestetik' ? 'selected' : '' }}>Kinestetik (Praktikum, Eksperimen Nyata)</option>
                    <option value="Baca/Tulis" {{ old('gaya_belajar') == 'Baca/Tulis' ? 'selected' : '' }}>Baca/Tulis (Teks, Literatur, Modul)</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5" for="status">Status Kesiswaan *</label>
                <select name="status" id="status" class="w-full px-3.5 py-2 text-sm rounded-xl border border-slate-300 bg-white text-slate-700 focus:border-teal-500 focus:outline-none focus:ring-2 focus:ring-teal-500/20 transition-all" required>
                    <option value="Aktif" {{ old('status', 'Aktif') == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="Non-Aktif" {{ old('status') == 'Non-Aktif' ? 'selected' : '' }}>Non-Aktif</option>
                    <option value="Mutasi" {{ old('status') == 'Mutasi' ? 'selected' : '' }}>Mutasi</option>
                    <option value="Lulus" {{ old('status') == 'Lulus' ? 'selected' : '' }}>Lulus</option>
                </select>
            </div>
        </div>

        <div class="pt-4 flex items-center gap-3 border-t border-slate-100">
            <button type="submit" class="px-5 py-2.5 rounded-xl bg-teal-600 text-white text-sm font-semibold hover:bg-teal-700 transition-colors shadow-sm focus:ring-2 focus:ring-teal-500 focus:ring-offset-2">
                Simpan Data Siswa
            </button>
            <a href="{{ route('siswa.index') }}" class="px-4 py-2.5 rounded-xl border border-slate-300 text-slate-700 text-sm font-semibold bg-white hover:bg-slate-50 transition-colors">
                Batal
            </a>
        </div>
    </form>
</div>
@endsection
