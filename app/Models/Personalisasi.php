<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Personalisasi extends Model
{
    use HasFactory;

    protected $fillable = [
        'siswa_id',
        'mata_pelajaran_id',
        'skor_performa',
        'kategori_pemahaman',
        'sumber_analisis',
        'keterangan_sumber',
        'gaya_belajar_rekomendasi',
        'tp_tuntas_count',
        'tp_belum_tuntas_count',
        'daftar_tp_remedial',
        'rekomendasi_materi',
        'rekomendasi_aktivitas',
        'catatan_guru',
        'distribusi_gaya_belajar',
    ];

    protected $casts = [
        'distribusi_gaya_belajar' => 'array',
    ];

    /**
     * Ambil distribusi gaya belajar (VARK) dalam persentase (total 100%)
     */
    public function getDistribusiGayaBelajarAttribute($value): array
    {
        if ($value) {
            $decoded = is_array($value) ? $value : json_decode($value, true);
            if (is_array($decoded) && !empty($decoded)) {
                return $decoded;
            }
        }

        // Fallback distribusi deterministik jika belum tersimpan di DB
        $dominan = $this->gaya_belajar_rekomendasi ?? $this->siswa->gaya_belajar ?? 'Visual';
        $seed = $this->siswa_id ?? 1;

        return \App\Services\AiPersonalizationService::hitungDistribusiVark($dominan, $seed);
    }

    /**
     * Dapatkan representasi badge visual untuk sumber engine analisis
     */
    public function getSumberAnalisisBadgeAttribute(): array
    {
        $sumber = $this->sumber_analisis ?? 'Fallback Heuristik';
        return match ($sumber) {
            'Gemini' => [
                'nama' => 'Google Gemini',
                'tipe' => 'gemini',
                'bg' => 'bg-teal-50 text-teal-800 border-teal-200',
                'badge_header' => 'bg-teal-100 text-teal-900 border-teal-300',
                'dot' => 'bg-teal-500',
                'model' => 'gemini-3.5-flash',
            ],
            'OpenAI' => [
                'nama' => 'OpenAI GPT',
                'tipe' => 'openai',
                'bg' => 'bg-emerald-50 text-emerald-800 border-emerald-200',
                'badge_header' => 'bg-emerald-100 text-emerald-900 border-emerald-300',
                'dot' => 'bg-emerald-500',
                'model' => 'gpt-4o-mini',
            ],
            'Router9' => [
                'nama' => str_contains(strtolower($this->keterangan_sumber ?? ''), 'claude')
                    ? 'Router9 Claude'
                    : (str_contains(strtolower($this->keterangan_sumber ?? ''), 'gpt')
                        ? 'Router9 GPT'
                        : 'Router9 Gemini'),
                'tipe' => 'router9',
                'bg' => 'bg-indigo-50 text-indigo-800 border-indigo-200',
                'badge_header' => 'bg-indigo-100 text-indigo-900 border-indigo-300',
                'dot' => 'bg-indigo-500',
                'model' => preg_match('/(?:Model:|Router9:|Gateway:)\s*([^\s,)]+)/i', $this->keterangan_sumber ?? '', $matches)
                    ? trim($matches[1])
                    : \App\Models\Setting::get('router9_model', config('services.router9.model', 'ag/gemini-2.5-flash')),
            ],
            default => [
                'nama' => 'Fallback Heuristik',
                'tipe' => 'fallback',
                'bg' => 'bg-amber-50 text-amber-900 border-amber-200',
                'badge_header' => 'bg-amber-100 text-amber-900 border-amber-300',
                'dot' => 'bg-amber-500',
                'model' => 'PLA Merdeka Engine',
            ],
        };
    }

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class);
    }

    public function mataPelajaran(): BelongsTo
    {
        return $this->belongsTo(MataPelajaran::class);
    }
}
