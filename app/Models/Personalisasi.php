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
        'gaya_belajar_rekomendasi',
        'tp_tuntas_count',
        'tp_belum_tuntas_count',
        'daftar_tp_remedial',
        'rekomendasi_materi',
        'rekomendasi_aktivitas',
        'catatan_guru',
    ];

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class);
    }

    public function mataPelajaran(): BelongsTo
    {
        return $this->belongsTo(MataPelajaran::class);
    }
}
