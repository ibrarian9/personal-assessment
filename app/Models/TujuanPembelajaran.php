<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TujuanPembelajaran extends Model
{
    use HasFactory;

    protected $fillable = [
        'lingkup_materi_id',
        'kode_tp',
        'deskripsi_tp',
        'target_ketercapaian',
        'urutan',
    ];

    public function lingkupMateri(): BelongsTo
    {
        return $this->belongsTo(LingkupMateri::class);
    }

    public function nilaiSumatifTps(): HasMany
    {
        return $this->hasMany(NilaiSumatifTp::class);
    }
}
