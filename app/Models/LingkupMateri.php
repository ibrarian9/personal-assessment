<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LingkupMateri extends Model
{
    use HasFactory;

    protected $fillable = [
        'mata_pelajaran_id',
        'kode_lm',
        'judul_materi',
        'deskripsi',
        'urutan',
    ];

    public function mataPelajaran(): BelongsTo
    {
        return $this->belongsTo(MataPelajaran::class);
    }

    public function tujuanPembelajarans(): HasMany
    {
        return $this->hasMany(TujuanPembelajaran::class)->orderBy('urutan');
    }
}
