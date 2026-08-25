<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Siswa extends Model
{
    use HasFactory;

    protected $fillable = [
        'nisn',
        'nama',
        'kelas',
        'email',
        'gaya_belajar',
        'status',
    ];

    public function nilaiSumatifTps(): HasMany
    {
        return $this->hasMany(NilaiSumatifTp::class);
    }

    public function personalisasis(): HasMany
    {
        return $this->hasMany(Personalisasi::class);
    }

    /**
     * Hitung rata-rata nilai sumatif TP
     */
    public function getRataRataNilaiAttribute(): float
    {
        $avg = $this->nilaiSumatifTps()->avg('nilai');
        return $avg ? round((float) $avg, 2) : 0.00;
    }

    /**
     * Hitung jumlah TP tuntas
     */
    public function getJumlahTpTuntasAttribute(): int
    {
        return $this->nilaiSumatifTps()->where('status_ketuntasan', 'Tuntas')->count();
    }

    /**
     * Hitung jumlah TP belum tuntas
     */
    public function getJumlahTpBelumTuntasAttribute(): int
    {
        return $this->nilaiSumatifTps()->where('status_ketuntasan', 'Belum Tuntas')->count();
    }
}
