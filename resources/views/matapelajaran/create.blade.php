@extends('layouts.app')

@section('title', 'Tambah Mata Pelajaran')

@section('content')
<!-- Page Header -->
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-2 border-b border-slate-200/80">
    <div>
        <h2 class="text-2xl font-extrabold tracking-tight text-slate-900">Tambah Mata Pelajaran SMA</h2>
        <p class="text-sm text-slate-500 mt-1">Registrasi mata pelajaran baru, fase kurikulum, dan standar KKM</p>
    </div>
    <div>
        <a href="{{ route('matapelajaran.index') }}" class="inline-flex items-center px-4 py-2 text-sm font-semibold rounded-xl border border-slate-300 text-slate-700 bg-white hover:bg-slate-50 transition-colors shadow-xs">
            Kembali
        </a>
    </div>
</div>

<div class="max-w-3xl rounded-2xl bg-white p-6 sm:p-8 shadow-xs border border-slate-200/80">
    <form action="{{ route('matapelajaran.store') }}" method="POST" class="space-y-6">
        @csrf
        
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5" for="kode_mapel">Kode Mata Pelajaran *</label>
                <input type="text" name="kode_mapel" id="kode_mapel" class="w-full px-3.5 py-2 text-sm rounded-xl border border-slate-300 bg-white placeholder-slate-400 focus:border-teal-500 focus:outline-none focus:ring-2 focus:ring-teal-500/20 transition-all font-mono" value="{{ old('kode_mapel') }}" required placeholder="Contoh: IPAS-X atau BIO-XI">
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5" for="nama_mapel">Nama Mata Pelajaran *</label>
                <input type="text" name="nama_mapel" id="nama_mapel" class="w-full px-3.5 py-2 text-sm rounded-xl border border-slate-300 bg-white placeholder-slate-400 focus:border-teal-500 focus:outline-none focus:ring-2 focus:ring-teal-500/20 transition-all" value="{{ old('nama_mapel') }}" required placeholder="Contoh: IPAS (Biologi SMA)">
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5" for="fase">Fase Kurikulum *</label>
                <select name="fase" id="fase" class="w-full px-3.5 py-2 text-sm rounded-xl border border-slate-300 bg-white text-slate-700 focus:border-teal-500 focus:outline-none focus:ring-2 focus:ring-teal-500/20 transition-all" required>
                    <option value="Fase E" {{ old('fase') == 'Fase E' ? 'selected' : '' }}>Fase E (Kelas X SMA)</option>
                    <option value="Fase F" {{ old('fase') == 'Fase F' ? 'selected' : '' }}>Fase F (Kelas XI - XII)</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5" for="tingkat">Tingkat Kelas *</label>
                <select name="tingkat" id="tingkat" class="w-full px-3.5 py-2 text-sm rounded-xl border border-slate-300 bg-white text-slate-700 focus:border-teal-500 focus:outline-none focus:ring-2 focus:ring-teal-500/20 transition-all" required>
                    <option value="Kelas X" {{ old('tingkat') == 'Kelas X' ? 'selected' : '' }}>Kelas X</option>
                    <option value="Kelas XI" {{ old('tingkat') == 'Kelas XI' ? 'selected' : '' }}>Kelas XI</option>
                    <option value="Kelas XII" {{ old('tingkat') == 'Kelas XII' ? 'selected' : '' }}>Kelas XII</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5" for="kkm">KKM Target TP *</label>
                <input type="number" step="0.5" min="0" max="100" name="kkm" id="kkm" class="w-full px-3.5 py-2 text-sm rounded-xl border border-slate-300 bg-white focus:border-teal-500 focus:outline-none focus:ring-2 focus:ring-teal-500/20 transition-all font-mono" value="{{ old('kkm', '75.00') }}" required>
            </div>
        </div>

        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-1.5" for="deskripsi">Capaian Pembelajaran dan Deskripsi Mata Pelajaran</label>
            <textarea name="deskripsi" id="deskripsi" class="w-full px-3.5 py-2 text-sm rounded-xl border border-slate-300 bg-white placeholder-slate-400 focus:border-teal-500 focus:outline-none focus:ring-2 focus:ring-teal-500/20 transition-all" rows="3" placeholder="Deskripsi ringkas capaian elemen dan mata pelajaran...">{{ old('deskripsi') }}</textarea>
        </div>

        <div class="pt-4 flex items-center gap-3 border-t border-slate-100">
            <button type="submit" class="px-5 py-2.5 rounded-xl bg-teal-600 text-white text-sm font-semibold hover:bg-teal-700 transition-colors shadow-sm focus:ring-2 focus:ring-teal-500 focus:ring-offset-2">
                Simpan Mata Pelajaran
            </button>
            <a href="{{ route('matapelajaran.index') }}" class="px-4 py-2.5 rounded-xl border border-slate-300 text-slate-700 text-sm font-semibold bg-white hover:bg-slate-50 transition-colors">
                Batal
            </a>
        </div>
    </form>
</div>
@endsection
