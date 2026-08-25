@extends('layouts.app')

@section('title', 'Pengaturan Sekolah & AI Engine')

@section('content')
<!-- Page Header -->
<x-page-header title="Pengaturan Sistem Sekolah & AI Engine" subtitle="Konfigurasi parameter Kurikulum Merdeka SMA, KKM Tujuan Pembelajaran, dan kredensial AI LLM" />

<div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
    <!-- Form Card -->
    <div class="lg:col-span-7 rounded-2xl bg-white p-6 sm:p-8 shadow-xs border border-slate-200/80">
        <form action="{{ route('setting.update') }}" method="POST" class="space-y-6">
            @csrf
            
            <div class="pb-2">
                <h3 class="text-sm font-bold uppercase tracking-wider text-slate-600 mb-4">Identitas Sekolah & Kurikulum</h3>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5" for="nama_institusi">Nama Sekolah SMA *</label>
                        <input type="text" name="nama_institusi" id="nama_institusi" class="w-full px-3.5 py-2 text-sm rounded-xl border border-slate-300 bg-white focus:border-teal-500 focus:outline-none focus:ring-2 focus:ring-teal-500/20 transition-all" value="{{ old('nama_institusi', $namaInstitusi) }}" required>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5" for="tahun_akademik">Tahun Ajaran & Semester *</label>
                        <input type="text" name="tahun_akademik" id="tahun_akademik" class="w-full px-3.5 py-2 text-sm rounded-xl border border-slate-300 bg-white focus:border-teal-500 focus:outline-none focus:ring-2 focus:ring-teal-500/20 transition-all" value="{{ old('tahun_akademik', $tahunAkademik) }}" required>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mt-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5" for="kkm_default">KKM / Kriteria Ketuntasan TP Default *</label>
                        <input type="number" step="0.5" min="0" max="100" name="kkm_default" id="kkm_default" class="w-full px-3.5 py-2 text-sm rounded-xl border border-slate-300 bg-white focus:border-teal-500 focus:outline-none focus:ring-2 focus:ring-teal-500/20 transition-all font-mono" value="{{ old('kkm_default', $kkmDefault) }}" required>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5" for="kurikulum">Kurikulum</label>
                        <input type="text" name="kurikulum" id="kurikulum" class="w-full px-3.5 py-2 text-sm rounded-xl border border-slate-300 bg-white focus:border-teal-500 focus:outline-none focus:ring-2 focus:ring-teal-500/20 transition-all" value="{{ old('kurikulum', $kurikulum) }}">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mt-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5" for="kepala_sekolah">Nama Kepala Sekolah</label>
                        <input type="text" name="kepala_sekolah" id="kepala_sekolah" class="w-full px-3.5 py-2 text-sm rounded-xl border border-slate-300 bg-white focus:border-teal-500 focus:outline-none focus:ring-2 focus:ring-teal-500/20 transition-all" value="{{ old('kepala_sekolah', $kepalaSekolah) }}">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5" for="nip_kepala_sekolah">NIP Kepala Sekolah</label>
                        <input type="text" name="nip_kepala_sekolah" id="nip_kepala_sekolah" class="w-full px-3.5 py-2 text-sm rounded-xl border border-slate-300 bg-white focus:border-teal-500 focus:outline-none focus:ring-2 focus:ring-teal-500/20 transition-all font-mono" value="{{ old('nip_kepala_sekolah', $nipKepalaSekolah) }}">
                    </div>
                </div>
            </div>

            <!-- AI Engine Settings -->
            <div class="pt-4 border-t border-slate-100">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-bold uppercase tracking-wider text-slate-600 flex items-center gap-2">
                        <svg class="h-4 w-4 text-teal-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 00-2.456 2.456zM16.894 20.567L16.5 21.75l-.394-1.183a2.25 2.25 0 00-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 001.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 001.423 1.423l1.183.394-1.183.394a2.25 2.25 0 00-1.423 1.423z" /></svg>
                        <span>Konfigurasi AI Engine (LLM)</span>
                    </h3>
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-teal-100 text-teal-800">
                        Smart Diagnostic
                    </span>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5" for="ai_provider">Provider AI Aktif</label>
                        <select name="ai_provider" id="ai_provider" class="w-full px-3.5 py-2 text-sm rounded-xl border border-slate-300 bg-white text-slate-700 focus:border-teal-500 focus:outline-none focus:ring-2 focus:ring-teal-500/20 transition-all">
                            <option value="gemini" {{ old('ai_provider', $aiProvider) == 'gemini' ? 'selected' : '' }}>Google Gemini API (Disarankan - gemini-1.5-flash / gemini-2.0-flash)</option>
                            <option value="openai" {{ old('ai_provider', $aiProvider) == 'openai' ? 'selected' : '' }}>OpenAI API (gpt-4o-mini)</option>
                            <option value="fallback" {{ old('ai_provider', $aiProvider) == 'fallback' ? 'selected' : '' }}>Mode Heuristik Offline (Tanpa Koneksi API)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5" for="gemini_api_key">Google Gemini API Key</label>
                        <input type="password" name="gemini_api_key" id="gemini_api_key" class="w-full px-3.5 py-2 text-sm rounded-xl border border-slate-300 bg-white font-mono focus:border-teal-500 focus:outline-none focus:ring-2 focus:ring-teal-500/20 transition-all" value="{{ old('gemini_api_key', $geminiApiKey) }}" placeholder="AIzaSy...">
                        <p class="text-xs text-slate-400 mt-1">Dapatkan gratis di <a href="https://aistudio.google.com/app/apikey" target="_blank" class="text-teal-600 underline">Google AI Studio</a>. Jika dikosongkan, sistem tetap berjalan otomatis menggunakan mode cerdas.</p>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5" for="openai_api_key">OpenAI API Key (Opsional)</label>
                        <input type="password" name="openai_api_key" id="openai_api_key" class="w-full px-3.5 py-2 text-sm rounded-xl border border-slate-300 bg-white font-mono focus:border-teal-500 focus:outline-none focus:ring-2 focus:ring-teal-500/20 transition-all" value="{{ old('openai_api_key', $openaiApiKey) }}" placeholder="sk-...">
                    </div>
                </div>
            </div>

            <div class="pt-2">
                <label class="flex items-start gap-3 p-3.5 rounded-xl border border-slate-200 bg-slate-50/70 cursor-pointer hover:bg-slate-50 transition-colors">
                    <input type="checkbox" name="recalculate" value="1" class="mt-0.5 h-4 w-4 rounded border-slate-300 text-teal-600 focus:ring-teal-500">
                    <span class="text-xs sm:text-sm font-medium text-slate-700 leading-relaxed">
                        Hitung ulang seluruh status ketuntasan TP & perbarui analisis personalisasi AI untuk seluruh siswa di database
                    </span>
                </label>
            </div>

            <div class="pt-4 border-t border-slate-100">
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-teal-600 text-white text-sm font-semibold hover:bg-teal-700 transition-colors shadow-sm focus:ring-2 focus:ring-teal-500 focus:ring-offset-2">
                    Simpan Pengaturan
                </button>
            </div>
        </form>
    </div>

    <!-- Explanation Box: Kurikulum Merdeka Overview -->
    <div class="lg:col-span-5 space-y-6">
        <div class="rounded-2xl bg-gradient-to-br from-teal-800 to-slate-900 p-6 text-white shadow-xs border border-teal-700/50 space-y-3">
            <div class="flex items-center gap-2">
                <span class="flex h-3 w-3 rounded-full bg-teal-400 animate-ping"></span>
                <h3 class="text-base font-bold text-white">Prinsip Asesmen Kurikulum Merdeka</h3>
            </div>
            <p class="text-xs text-slate-300 leading-relaxed">
                Asesmen sumatif berfokus pada ketercapaian setiap butir <strong>Tujuan Pembelajaran (TP)</strong> pada masing-masing <strong>Lingkup Materi</strong>. Nilai akhir dihitung dari akumulasi rerata TP dengan Kriteria Ketercapaian Tujuan Pembelajaran (KKTP / KKM).
            </p>
            <div class="pt-2 text-xs text-teal-300 font-medium space-y-1">
                <p>&bull; <strong>Tuntas:</strong> Nilai TP &ge; 75 (Mencapai target ketuntasan, diberikan program Pengayaan)</p>
                <p>&bull; <strong>Belum Tuntas:</strong> Nilai TP &lt; 75 (Belum mencapai target, diberikan program Bimbingan Remedial)</p>
                <p>&bull; <strong>Diferensiasi Pembelajaran:</strong> Menyesuaikan media dan metode belajar dengan modalitas VARK siswa.</p>
            </div>
        </div>

        <div class="rounded-2xl bg-white p-6 shadow-xs border border-slate-200/80 space-y-4">
            <div class="pb-3 border-b border-slate-100">
                <h3 class="text-base font-bold text-slate-900">Kategori Predikat Hasil Belajar</h3>
            </div>
            <div class="overflow-x-auto rounded-xl border border-slate-200">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-50 uppercase font-semibold text-slate-500 border-b border-slate-200">
                        <tr>
                            <th class="py-2.5 px-3">Rentang Nilai</th>
                            <th class="py-2.5 px-3 text-center">Predikat</th>
                            <th class="py-2.5 px-3 text-center">Ketuntasan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr><td class="py-2 px-3 font-mono">&ge; 90</td><td class="py-2 px-3 text-center font-bold text-emerald-700">A (Sangat Baik)</td><td class="py-2 px-3 text-center font-medium text-emerald-700">Tuntas</td></tr>
                        <tr><td class="py-2 px-3 font-mono">80.00 - 89.99</td><td class="py-2 px-3 text-center font-bold text-blue-700">B (Baik)</td><td class="py-2 px-3 text-center font-medium text-emerald-700">Tuntas</td></tr>
                        <tr><td class="py-2 px-3 font-mono">75.00 - 79.99</td><td class="py-2 px-3 text-center font-bold text-teal-700">C (Cukup)</td><td class="py-2 px-3 text-center font-medium text-emerald-700">Tuntas</td></tr>
                        <tr><td class="py-2 px-3 font-mono">&lt; 75.00</td><td class="py-2 px-3 text-center font-bold text-rose-700">D (Perlu Bimbingan)</td><td class="py-2 px-3 text-center font-medium text-rose-700">Remedial</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
