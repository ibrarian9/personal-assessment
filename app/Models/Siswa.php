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
        if ($this->relationLoaded('nilaiSumatifTps')) {
            $avg = $this->nilaiSumatifTps->avg('nilai');
            return $avg ? round((float) $avg, 2) : 0.00;
        }

        $avg = $this->nilaiSumatifTps()->avg('nilai');
        return $avg ? round((float) $avg, 2) : 0.00;
    }

    /**
     * Hitung jumlah TP tuntas
     */
    public function getJumlahTpTuntasAttribute(): int
    {
        if ($this->relationLoaded('nilaiSumatifTps')) {
            return $this->nilaiSumatifTps->where('status_ketuntasan', 'Tuntas')->count();
        }

        return $this->nilaiSumatifTps()->where('status_ketuntasan', 'Tuntas')->count();
    }

    /**
     * Hitung jumlah TP belum tuntas
     */
    public function getJumlahTpBelumTuntasAttribute(): int
    {
        if ($this->relationLoaded('nilaiSumatifTps')) {
            return $this->nilaiSumatifTps->where('status_ketuntasan', 'Belum Tuntas')->count();
        }

        return $this->nilaiSumatifTps()->where('status_ketuntasan', 'Belum Tuntas')->count();
    }
}
