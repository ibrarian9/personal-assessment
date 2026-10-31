@extends('layouts.app')

@section('title', 'Edit Tujuan Pembelajaran - ' . $tujuanPembelajaran->kode_tp)

@section('content')
<!-- Page Header -->
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-2 border-b border-slate-200/80">
    <div>
        <h2 class="text-2xl font-extrabold tracking-tight text-slate-900">Edit Tujuan Pembelajaran</h2>
        <p class="text-sm text-slate-500 mt-1">Perbarui butir Tujuan Pembelajaran <strong>{{ $tujuanPembelajaran->kode_tp }}</strong></p>
    </div>
    <div>
        <a href="{{ route('tujuan-pembelajaran.index', ['mata_pelajaran_id' => $tujuanPembelajaran->lingkupMateri->mata_pelajaran_id]) }}" class="inline-flex items-center px-4 py-2 text-sm font-semibold rounded-xl border border-slate-300 text-slate-700 bg-white hover:bg-slate-50 transition-colors shadow-xs">
            Kembali
        </a>
    </div>
</div>

<div class="max-w-3xl rounded-2xl bg-white p-6 sm:p-8 shadow-xs border border-slate-200/80">
    <form action="{{ route('tujuan-pembelajaran.update', $tujuanPembelajaran) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5" for="lingkup_materi_id">Lingkup Materi *</label>
                <select name="lingkup_materi_id" id="lingkup_materi_id" class="w-full px-3.5 py-2 text-sm rounded-xl border border-slate-300 bg-white text-slate-700 focus:border-teal-500 focus:outline-none focus:ring-2 focus:ring-teal-500/20 transition-all" required>
                    @foreach($lingkupMateris as $lm)
                        <option value="{{ $lm->id }}" {{ old('lingkup_materi_id', $tujuanPembelajaran->lingkup_materi_id) == $lm->id ? 'selected' : '' }}>
                            {{ $lm->kode_lm }} - {{ $lm->judul_materi }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5" for="kode_tp">Kode TP *</label>
                <input type="text" name="kode_tp" id="kode_tp" class="w-full px-3.5 py-2 text-sm rounded-xl border border-slate-300 bg-white placeholder-slate-400 focus:border-teal-500 focus:outline-none focus:ring-2 focus:ring-teal-500/20 transition-all font-mono" value="{{ old('kode_tp', $tujuanPembelajaran->kode_tp) }}" required>
            </div>
        </div>

        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-1.5" for="deskripsi_tp">Deskripsi Tujuan Pembelajaran (TP) *</label>
            <textarea name="deskripsi_tp" id="deskripsi_tp" class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-slate-300 bg-white placeholder-slate-400 focus:border-teal-500 focus:outline-none focus:ring-2 focus:ring-teal-500/20 transition-all" rows="4" required>{{ old('deskripsi_tp', $tujuanPembelajaran->deskripsi_tp) }}</textarea>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5" for="target_ketercapaian">Target Ketercapaian TP (KKM) *</label>
                <input type="number" step="0.5" min="0" max="100" name="target_ketercapaian" id="target_ketercapaian" class="w-full px-3.5 py-2 text-sm rounded-xl border border-slate-300 bg-white focus:border-teal-500 focus:outline-none focus:ring-2 focus:ring-teal-500/20 transition-all font-mono" value="{{ old('target_ketercapaian', $tujuanPembelajaran->target_ketercapaian) }}" required>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5" for="urutan">Urutan TP</label>
                <input type="number" name="urutan" id="urutan" class="w-full px-3.5 py-2 text-sm rounded-xl border border-slate-300 bg-white focus:border-teal-500 focus:outline-none focus:ring-2 focus:ring-teal-500/20 transition-all font-mono" value="{{ old('urutan', $tujuanPembelajaran->urutan) }}">
            </div>
        </div>

        <div class="pt-4 flex items-center gap-3 border-t border-slate-100">
            <button type="submit" class="px-5 py-2.5 rounded-xl bg-teal-600 text-white text-sm font-semibold hover:bg-teal-700 transition-colors shadow-sm focus:ring-2 focus:ring-teal-500 focus:ring-offset-2">
                Simpan Perubahan
            </button>
            <a href="{{ route('tujuan-pembelajaran.index', ['mata_pelajaran_id' => $tujuanPembelajaran->lingkupMateri->mata_pelajaran_id]) }}" class="px-4 py-2.5 rounded-xl border border-slate-300 text-slate-700 text-sm font-semibold bg-white hover:bg-slate-50 transition-colors">
                Batal
            </a>
        </div>
    </form>
</div>
@endsection
