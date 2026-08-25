<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NilaiSumatifTp extends Model
{
    use HasFactory;

    protected $fillable = [
        'siswa_id',
        'tujuan_pembelajaran_id',
        'nilai',
        'status_ketuntasan',
        'catatan',
    ];

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class);
    }

    public function tujuanPembelajaran(): BelongsTo
    {
        return $this->belongsTo(TujuanPembelajaran::class);
    }

    /**
     * Hitung otomatis status ketuntasan berdasarkan target ketercapaian TP / KKM
     */
    public static function tentukanKetuntasan(float $nilai, float $kkm = 75.00): string
    {
        return $nilai >= $kkm ? 'Tuntas' : 'Belum Tuntas';
    }

    /**
     * Konversi nilai ke predikat huruf mutu (A, B, C, D)
     */
    public static function tentukanPredikat(float $nilai): string
    {
        if ($nilai >= 90) return 'A (Sangat Baik)';
        if ($nilai >= 80) return 'B (Baik)';
        if ($nilai >= 75) return 'C (Cukup / Tuntas)';
        return 'D (Perlu Bimbingan)';
    }
}
