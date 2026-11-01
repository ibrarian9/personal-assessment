<?php

namespace App\Services;

use App\Models\Siswa;
use App\Models\Personalisasi;

class PersonalizationService
{
    protected AiPersonalizationService $aiService;

    public function __construct(?AiPersonalizationService $aiService = null)
    {
        $this->aiService = $aiService ?? new AiPersonalizationService();
    }

    /**
     * Generate atau refresh analisis personalisasi untuk seluruh siswa SMA
     */
    public function generateAll(): int
    {
        return $this->aiService->generateAll();
    }

    /**
     * Generate analisis personalisasi untuk seorang siswa
     */
    public function generateForSiswa(Siswa $siswa): Personalisasi
    {
        return $this->aiService->generateForSiswa($siswa);
    }

    /**
     * Fallback cepat analisis personalisasi untuk seorang siswa (tanpa network call)
     */
    public function fallbackGenerate(Siswa $siswa, ?string $alasan = null): Personalisasi
    {
        return $this->aiService->fallbackGenerate($siswa, $siswa->nilaiSumatifTps, $alasan);
    }
}
