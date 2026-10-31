@extends('layouts.app')

@section('title', 'Input Nilai Sumatif TP')

@section('content')
<!-- Page Header -->
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-2 border-b border-slate-200/80">
    <div>
        <h2 class="text-2xl font-extrabold tracking-tight text-slate-900">Input Nilai Sumatif per TP</h2>
        <p class="text-sm text-slate-500 mt-1">Pencatatan skor sumatif siswa SMA untuk butir Tujuan Pembelajaran tertentu</p>
    </div>
    <div>
        <a href="{{ route('penilaian.matrix') }}" class="inline-flex items-center px-4 py-2 text-sm font-semibold rounded-xl border border-slate-300 text-slate-700 bg-white hover:bg-slate-50 transition-colors shadow-xs">
            Ke Matriks Grid
        </a>
    </div>
</div>

<div class="max-w-3xl rounded-2xl bg-white p-6 sm:p-8 shadow-xs border border-slate-200/80">
    <form action="{{ route('penilaian.store') }}" method="POST" class="space-y-6">
        @csrf
        
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5" for="siswa_id">Pilih Siswa SMA *</label>
                <select name="siswa_id" id="siswa_id" class="w-full px-3.5 py-2 text-sm rounded-xl border border-slate-300 bg-white text-slate-700 focus:border-teal-500 focus:outline-none focus:ring-2 focus:ring-teal-500/20 transition-all" required>
                    <option value="">-- Pilih Siswa --</option>
                    @foreach($siswas as $s)
                        <option value="{{ $s->id }}" {{ (old('siswa_id', $selectedSiswaId) == $s->id) ? 'selected' : '' }}>
                            {{ $s->nama }} (NISN: {{ $s->nisn }} - {{ $s->kelas }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5" for="tujuan_pembelajaran_id">Pilih Tujuan Pembelajaran (TP) *</label>
                <select name="tujuan_pembelajaran_id" id="tujuan_pembelajaran_id" class="w-full px-3.5 py-2 text-sm rounded-xl border border-slate-300 bg-white text-slate-700 focus:border-teal-500 focus:outline-none focus:ring-2 focus:ring-teal-500/20 transition-all" required>
                    <option value="">-- Pilih TP --</option>
                    @foreach($tps as $tp)
                        <option value="{{ $tp->id }}" {{ old('tujuan_pembelajaran_id') == $tp->id ? 'selected' : '' }}>
                            {{ $tp->lingkupMateri->kode_lm ?? '' }} - {{ $tp->kode_tp }}: {{ \Illuminate\Support\Str::limit($tp->deskripsi_tp, 50) }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-1.5" for="nilai">Nilai Sumatif TP (0 - 100) *</label>
            <input type="number" step="0.5" min="0" max="100" name="nilai" id="nilai" class="w-full px-3.5 py-2 text-sm rounded-xl border border-slate-300 bg-white placeholder-slate-400 focus:border-teal-500 focus:outline-none focus:ring-2 focus:ring-teal-500/20 transition-all font-mono" value="{{ old('nilai') }}" required placeholder="Contoh: 85.00">
            <p class="text-xs text-slate-400 mt-1">Standar ketuntasan minimal KKM adalah 75.00.</p>
        </div>

        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-1.5" for="catatan">Catatan dan Umpan Balik Guru</label>
            <textarea name="catatan" id="catatan" class="w-full px-3.5 py-2 text-sm rounded-xl border border-slate-300 bg-white placeholder-slate-400 focus:border-teal-500 focus:outline-none focus:ring-2 focus:ring-teal-500/20 transition-all" rows="3" placeholder="Catatan perkembangan atau rekomendasi penguatan...">{{ old('catatan') }}</textarea>
        </div>

        <div class="pt-4 flex items-center gap-3 border-t border-slate-100">
            <button type="submit" class="px-5 py-2.5 rounded-xl bg-teal-600 text-white text-sm font-semibold hover:bg-teal-700 transition-colors shadow-sm focus:ring-2 focus:ring-teal-500 focus:ring-offset-2">
                Simpan Nilai Sumatif
            </button>
            <a href="{{ route('penilaian.index') }}" class="px-4 py-2.5 rounded-xl border border-slate-300 text-slate-700 text-sm font-semibold bg-white hover:bg-slate-50 transition-colors">
                Batal
            </a>
        </div>
    </form>
</div>
@endsection
