<?php

namespace App\Http\Controllers;

use App\Models\NilaiSumatifTp;
use App\Models\Setting;
use App\Services\PersonalizationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingController extends Controller
{
    protected PersonalizationService $personalizationService;

    public function __construct(PersonalizationService $personalizationService)
    {
        $this->personalizationService = $personalizationService;
    }

    public function index(): View
    {
        $namaInstitusi = Setting::get('nama_institusi', 'SMA Negeri 1 Merdeka');
        $tahunAkademik = Setting::get('tahun_akademik', '2025/2026 Ganjil');
        $kkmDefault = Setting::get('kkm_default', '75');
        $kepalaSekolah = Setting::get('kepala_sekolah', 'Dr. H. Bambang Sutrisno, M.Pd.');
        $nipKepalaSekolah = Setting::get('nip_kepala_sekolah', '196805121994031005');
        $kurikulum = Setting::get('kurikulum', 'Kurikulum Merdeka (Fase E & F)');

        $aiProvider = Setting::get('ai_provider', config('services.ai.provider', 'gemini'));
        $geminiApiKey = Setting::get('gemini_api_key', config('services.gemini.api_key', ''));
        $openaiApiKey = Setting::get('openai_api_key', config('services.openai.api_key', ''));

        return view('setting.index', compact(
            'namaInstitusi',
            'tahunAkademik',
            'kkmDefault',
            'kepalaSekolah',
            'nipKepalaSekolah',
            'kurikulum',
            'aiProvider',
            'geminiApiKey',
            'openaiApiKey'
        ));
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama_institusi' => 'required|string|max:150',
            'tahun_akademik' => 'required|string|max:50',
            'kkm_default' => 'required|numeric|min:0|max:100',
            'kepala_sekolah' => 'nullable|string|max:150',
            'nip_kepala_sekolah' => 'nullable|string|max:50',
            'kurikulum' => 'nullable|string|max:100',
            'ai_provider' => 'nullable|string|in:gemini,openai,fallback',
            'gemini_api_key' => 'nullable|string|max:255',
            'openai_api_key' => 'nullable|string|max:255',
            'recalculate' => 'nullable|boolean',
        ]);

        Setting::set('nama_institusi', $validated['nama_institusi'], 'Nama Sekolah');
        Setting::set('tahun_akademik', $validated['tahun_akademik'], 'Tahun Ajaran & Semester');
        Setting::set('kkm_default', $validated['kkm_default'], 'KKM Default TP');
        Setting::set('kepala_sekolah', $validated['kepala_sekolah'] ?? '', 'Kepala Sekolah');
        Setting::set('nip_kepala_sekolah', $validated['nip_kepala_sekolah'] ?? '', 'NIP Kepala Sekolah');
        Setting::set('kurikulum', $validated['kurikulum'] ?? 'Kurikulum Merdeka', 'Kurikulum');

        if ($request->has('ai_provider')) {
            Setting::set('ai_provider', $validated['ai_provider'] ?? 'gemini', 'AI Provider');
        }
        if ($request->has('gemini_api_key')) {
            Setting::set('gemini_api_key', $validated['gemini_api_key'] ?? '', 'Google Gemini API Key');
        }
        if ($request->has('openai_api_key')) {
            Setting::set('openai_api_key', $validated['openai_api_key'] ?? '', 'OpenAI API Key');
        }

        // Hitung ulang ketuntasan jika opsi recalculate dicentang
        if ($request->boolean('recalculate')) {
            $kkm = (float) $validated['kkm_default'];
            $allScores = NilaiSumatifTp::with('tujuanPembelajaran')->get();
            foreach ($allScores as $ns) {
                $target = $ns->tujuanPembelajaran ? $ns->tujuanPembelajaran->target_ketercapaian : $kkm;
                $ns->update([
                    'status_ketuntasan' => NilaiSumatifTp::tentukanKetuntasan((float)$ns->nilai, $target)
                ]);
            }
            $this->personalizationService->generateAll();
        }

        return redirect()->route('setting.index')->with('success', 'Pengaturan sistem sekolah dan AI engine berhasil disimpan.');
    }
}
