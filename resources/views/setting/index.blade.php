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
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5" for="kkm_default">KKM dan Kriteria Ketuntasan TP Default *</label>
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
                    <!-- Status Ringkasan Engine AI Aktif -->
                    <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/90 space-y-2.5">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2.5">
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-semibold text-slate-600">Mesin AI Aktif Utama:</span>
                                <span id="overall_active_engine_badge" class="font-mono text-xs font-bold text-teal-800 bg-teal-100/80 px-2.5 py-1 rounded-lg border border-teal-200 shadow-2xs">
                                    @if(old('ai_provider', $aiProvider) == 'router9')
                                        Router9 &bull; {{ old('router9_model', $router9Model) }}
                                    @elseif(old('ai_provider', $aiProvider) == 'gemini')
                                        Google Gemini &bull; gemini-3.5-flash
                                    @elseif(old('ai_provider', $aiProvider) == 'openai')
                                        OpenAI &bull; gpt-4o-mini
                                    @else
                                        Mode Heuristik Offline
                                    @endif
                                </span>
                            </div>
                            <button type="button" onclick="testCurrentAiConnection()" id="btn_test_connection" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold text-teal-800 bg-white hover:bg-teal-50 border border-teal-300 shadow-2xs transition-colors shrink-0 self-start sm:self-center">
                                <svg id="test_icon" class="w-3.5 h-3.5 text-teal-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                <span id="test_btn_text">Uji Koneksi AI</span>
                            </button>
                        </div>

                        <!-- Result Box: Uji Koneksi -->
                        <div id="test_connection_result" class="hidden p-3 rounded-lg border text-xs leading-relaxed transition-all"></div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5" for="ai_provider">Provider AI Aktif</label>
                        <select name="ai_provider" id="ai_provider" class="w-full px-3.5 py-2 text-sm rounded-xl border border-slate-300 bg-white text-slate-700 focus:border-teal-500 focus:outline-none focus:ring-2 focus:ring-teal-500/20 transition-all font-medium">
                            <option value="gemini" {{ old('ai_provider', $aiProvider) == 'gemini' ? 'selected' : '' }}>Google Gemini &bull; gemini-3.5-flash</option>
                            <option value="router9" {{ old('ai_provider', $aiProvider) == 'router9' ? 'selected' : '' }}>Router9 Gateway &bull; Multi-Model AI</option>
                            <option value="openai" {{ old('ai_provider', $aiProvider) == 'openai' ? 'selected' : '' }}>OpenAI API &bull; gpt-4o-mini</option>
                            <option value="fallback" {{ old('ai_provider', $aiProvider) == 'fallback' ? 'selected' : '' }}>Mode Heuristik Offline &bull; Tanpa Koneksi API</option>
                        </select>
                    </div>

                    <!-- Router9 Multi-Model Configuration Box (Consistent Light Theme) -->
                    <div class="p-4 sm:p-5 rounded-2xl bg-slate-50/80 border border-slate-200/90 space-y-4">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 pb-3 border-b border-slate-200">
                            <div class="flex items-center gap-2.5">
                                <div class="flex h-8 w-8 items-center justify-center rounded-xl bg-teal-100 text-teal-700 border border-teal-200 shadow-2xs">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                </div>
                                <div>
                                    <h4 class="text-sm font-bold text-slate-800">Konfigurasi Gateway Router9</h4>
                                    <p class="text-xs text-slate-500">Koneksi gateway terhubung otomatis ke armada model AI</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-medium bg-emerald-50 text-emerald-800 border border-emerald-200">
                                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                    Koneksi Aktif
                                </span>
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold bg-teal-100 text-teal-800 border border-teal-200">
                                    20 Model Tersedia
                                </span>
                            </div>
                        </div>

                        <!-- Fixed Hidden Values: Router9 API Key & Base URL -->
                        <input type="hidden" name="router9_api_key" id="router9_api_key" value="{{ old('router9_api_key', $router9ApiKey ?? '') }}">
                        <input type="hidden" name="router9_base_url" id="router9_base_url" value="{{ old('router9_base_url', $router9BaseUrl ?? 'https://api.router9.com/v1') }}">

                        <!-- Status Banner: Model AI Aktif Saat Ini -->
                        <div class="p-3.5 rounded-xl bg-teal-50 border border-teal-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 shadow-2xs">
                            <div class="flex items-center gap-2.5">
                                <span class="relative flex h-2.5 w-2.5 shrink-0">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-teal-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-teal-600"></span>
                                </span>
                                <div>
                                    <span class="text-[11px] font-bold text-teal-800 uppercase tracking-wider block">Model yang Aktif Digunakan:</span>
                                    <div class="flex items-center gap-2 mt-0.5">
                                        <span id="active_model_badge" class="font-mono font-bold text-sm text-teal-950 bg-white px-2.5 py-0.5 rounded-lg border border-teal-300 shadow-2xs">
                                            {{ old('router9_model', $router9Model) }}
                                        </span>
                                        <span id="active_model_desc" class="text-xs text-teal-700 hidden sm:inline"></span>
                                    </div>
                                </div>
                            </div>
                            <span class="text-xs text-teal-700 font-medium bg-teal-100/60 px-2.5 py-1 rounded-lg border border-teal-200 self-start sm:self-center">
                                Digunakan untuk Personalisasi AI
                            </span>
                        </div>

                        <!-- Single Intuitive Model Dropdown -->
                        <div>
                            @php
                                $allKnownModelIds = [];
                                foreach($availableRouter9Models as $groupModels) {
                                    $allKnownModelIds = array_merge($allKnownModelIds, array_keys($groupModels));
                                }
                                $currentModelVal = old('router9_model', $router9Model);
                                $isCustomModel = !in_array($currentModelVal, $allKnownModelIds);
                            @endphp

                            <label class="block text-sm font-semibold text-slate-700 mb-1.5" for="router9_model_select">
                                Pilih Model Router9
                            </label>
                            <select id="router9_model_select" class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-slate-300 bg-white text-slate-800 focus:border-teal-500 focus:outline-none focus:ring-2 focus:ring-teal-500/20 transition-all cursor-pointer font-medium">
                                @foreach($availableRouter9Models as $groupName => $models)
                                    <optgroup label="{{ $groupName }}">
                                        @foreach($models as $mId => $mLabel)
                                            <option value="{{ $mId }}" data-label="{{ $mLabel }}" {{ $currentModelVal == $mId ? 'selected' : '' }}>
                                                {{ $mLabel }} &bull; {{ $mId }}
                                            </option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                                <optgroup label="Pilihan Lain">
                                    <option value="custom" data-label="Model Kustom" {{ $isCustomModel ? 'selected' : '' }}>
                                        + Masukkan ID Model Kustom Lainnya
                                    </option>
                                </optgroup>
                            </select>

                            <!-- Hidden Actual Form Input -->
                            <input type="hidden" name="router9_model" id="router9_model" value="{{ $currentModelVal }}">

                            <!-- Conditional Custom Model Input -->
                            <div id="custom_model_container" class="{{ $isCustomModel ? '' : 'hidden' }} mt-3 p-3 rounded-xl bg-slate-50 border border-slate-200">
                                <label class="block text-xs font-semibold text-slate-700 mb-1" for="custom_model_input">
                                    Ketik ID Model Kustom Router9
                                </label>
                                <input type="text" id="custom_model_input" class="w-full px-3 py-2 text-sm rounded-lg border border-slate-300 bg-white font-mono text-slate-800 placeholder-slate-400 focus:border-teal-500 focus:outline-none focus:ring-2 focus:ring-teal-500/20 transition-all" value="{{ $isCustomModel ? $currentModelVal : '' }}" placeholder="contoh: ag/gemini-3.8-pro">
                                <p class="text-[11px] text-slate-500 mt-1">ID model kustom akan dikirim langsung ke Router9 Gateway.</p>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5" for="gemini_api_key">Google Gemini API Key (Langsung)</label>
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
                Asesmen sumatif berfokus pada ketercapaian setiap butir <strong>Tujuan Pembelajaran (TP)</strong> pada masing-masing <strong>Lingkup Materi</strong>. Nilai akhir dihitung dari akumulasi rerata TP dengan Kriteria Ketercapaian Tujuan Pembelajaran (KKTP atau KKM).
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

<script>
function syncActiveModelDisplay(modelId, label) {
    const badge = document.getElementById('active_model_badge');
    const desc = document.getElementById('active_model_desc');
    const hiddenInput = document.getElementById('router9_model');

    if (badge) {
        badge.textContent = modelId || 'Belum dipilih';
    }
    if (desc) {
        desc.textContent = label ? `• ${label}` : '';
    }
    if (hiddenInput && modelId) {
        hiddenInput.value = modelId;
    }
    syncOverallEngineBadge();
}

function syncOverallEngineBadge() {
    const providerSelect = document.getElementById('ai_provider');
    const overallBadge = document.getElementById('overall_active_engine_badge');
    const hiddenModelInput = document.getElementById('router9_model');

    if (!providerSelect || !overallBadge) return;
    const p = providerSelect.value;
    const model = (hiddenModelInput && hiddenModelInput.value) ? hiddenModelInput.value : 'ag/gemini-2.5-flash';

    if (p === 'router9') {
        overallBadge.textContent = `Router9 • ${model}`;
    } else if (p === 'gemini') {
        overallBadge.textContent = 'Google Gemini • gemini-3.5-flash';
    } else if (p === 'openai') {
        overallBadge.textContent = 'OpenAI • gpt-4o-mini';
    } else {
        overallBadge.textContent = 'Mode Heuristik Offline';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const select = document.getElementById('router9_model_select');
    const hiddenInput = document.getElementById('router9_model');
    const customContainer = document.getElementById('custom_model_container');
    const customInput = document.getElementById('custom_model_input');
    const providerSelect = document.getElementById('ai_provider');

    if (providerSelect) {
        providerSelect.addEventListener('change', syncOverallEngineBadge);
    }

    function updateFromSelect() {
        if (!select) return;
        const selectedOption = select.options[select.selectedIndex];
        const val = select.value;
        const label = selectedOption ? selectedOption.getAttribute('data-label') : '';

        if (val === 'custom') {
            if (customContainer) customContainer.classList.remove('hidden');
            if (customInput) {
                customInput.focus();
                const customVal = customInput.value.trim() || 'Kustom';
                syncActiveModelDisplay(customVal, 'Model Kustom');
            }
        } else {
            if (customContainer) customContainer.classList.add('hidden');
            syncActiveModelDisplay(val, label);
        }

        // Otomatis pastikan provider aktif diubah ke Router9 jika memilih model Router9
        if (providerSelect && providerSelect.value !== 'router9') {
            providerSelect.value = 'router9';
            syncOverallEngineBadge();
        }
    }

    if (select) {
        select.addEventListener('change', updateFromSelect);
        // Initial setup
        const initialOption = select.options[select.selectedIndex];
        if (initialOption && select.value !== 'custom') {
            syncActiveModelDisplay(select.value, initialOption.getAttribute('data-label'));
        } else if (select.value === 'custom' && customInput) {
            syncActiveModelDisplay(customInput.value.trim() || 'Kustom', 'Model Kustom');
        }
    }

    if (customInput) {
        customInput.addEventListener('input', function() {
            if (select && select.value === 'custom') {
                const customVal = this.value.trim() || 'Kustom';
                syncActiveModelDisplay(customVal, 'Model Kustom');
            }
        });
    }

    syncOverallEngineBadge();
});

function testCurrentAiConnection() {
    const btn = document.getElementById('btn_test_connection');
    const btnText = document.getElementById('test_btn_text');
    const resultBox = document.getElementById('test_connection_result');
    const providerSelect = document.getElementById('ai_provider');
    const hiddenModel = document.getElementById('router9_model');

    if (!btn || !resultBox || !providerSelect) return;

    btn.disabled = true;
    btn.classList.add('opacity-75', 'cursor-wait');
    btnText.textContent = 'Menguji...';
    resultBox.className = 'p-3 rounded-lg border bg-slate-100 border-slate-200 text-slate-700 text-xs flex items-center gap-2';
    resultBox.innerHTML = `
        <svg class="animate-spin h-4 w-4 text-teal-600" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
        </svg>
        <span>Menghubungi server API untuk menguji respons dan latensi...</span>
    `;
    resultBox.classList.remove('hidden');

    const provider = providerSelect.value;
    const model = (hiddenModel && hiddenModel.value) ? hiddenModel.value : '';

    fetch('{{ route("setting.test-connection") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
        },
        body: JSON.stringify({ provider: provider, model: model }),
    })
    .then(res => res.json())
    .then(data => {
        btn.disabled = false;
        btn.classList.remove('opacity-75', 'cursor-wait');
        btnText.textContent = 'Uji Koneksi AI';

        if (data.success) {
            resultBox.className = 'p-3 rounded-lg border bg-emerald-50 border-emerald-300 text-emerald-900 text-xs space-y-1.5 shadow-2xs';
            resultBox.innerHTML = `
                <div class="flex items-center justify-between font-bold text-emerald-800">
                    <div class="flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        <span>${data.message}</span>
                    </div>
                    ${data.latency_ms !== undefined ? `<span class="font-mono text-[11px] bg-emerald-100 text-emerald-800 px-2 py-0.5 rounded border border-emerald-200 font-semibold">${data.latency_ms} ms</span>` : ''}
                </div>
                <div class="text-[11px] text-emerald-700 flex flex-wrap items-center gap-x-4 gap-y-1">
                    <span><strong>Penyedia:</strong> ${data.provider}</span>
                    ${data.model ? `<span><strong>Model:</strong> <code class="font-mono font-semibold bg-emerald-100/60 px-1 py-0.5 rounded">${data.model}</code></span>` : ''}
                    ${data.reply ? `<span><strong>Respon AI:</strong> "${data.reply}"</span>` : ''}
                </div>
            `;
        } else {
            resultBox.className = 'p-3 rounded-lg border bg-rose-50 border-rose-300 text-rose-900 text-xs space-y-1 shadow-2xs';
            resultBox.innerHTML = `
                <div class="flex items-center gap-1.5 font-bold text-rose-800">
                    <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <span>Uji Koneksi Gagal</span>
                </div>
                <p class="text-[11px] text-rose-700 leading-relaxed">${data.message}</p>
            `;
        }
    })
    .catch(err => {
        btn.disabled = false;
        btn.classList.remove('opacity-75', 'cursor-wait');
        btnText.textContent = 'Uji Koneksi AI';

        resultBox.className = 'p-3 rounded-lg border bg-rose-50 border-rose-300 text-rose-900 text-xs shadow-2xs';
        resultBox.innerHTML = `<p class="font-semibold text-rose-800">Terjadi kendala saat menghubungi server lokal: ${err.message}</p>`;
    });
}
</script>
@endsection
