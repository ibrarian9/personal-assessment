<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class MataPelajaran extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_mapel',
        'nama_mapel',
        'fase',
        'tingkat',
        'kkm',
        'deskripsi',
    ];

    public function lingkupMateris(): HasMany
    {
        return $this->hasMany(LingkupMateri::class)->orderBy('urutan');
    }

    public function tujuanPembelajarans(): HasManyThrough
    {
        return $this->hasManyThrough(TujuanPembelajaran::class, LingkupMateri::class);
    }
}
