<?php

namespace Tests\Unit;

use App\Models\NilaiSumatifTp;
use PHPUnit\Framework\TestCase;

class NilaiSumatifTpTest extends TestCase
{
    public function test_tentukan_ketuntasan_kkm_75(): void
    {
        $this->assertEquals('Tuntas', NilaiSumatifTp::tentukanKetuntasan(75.00, 75.00));
        $this->assertEquals('Tuntas', NilaiSumatifTp::tentukanKetuntasan(95.00, 75.00));
        $this->assertEquals('Tuntas', NilaiSumatifTp::tentukanKetuntasan(80.00, 75.00));
        $this->assertEquals('Belum Tuntas', NilaiSumatifTp::tentukanKetuntasan(74.99, 75.00));
        $this->assertEquals('Belum Tuntas', NilaiSumatifTp::tentukanKetuntasan(55.00, 75.00));
        $this->assertEquals('Belum Tuntas', NilaiSumatifTp::tentukanKetuntasan(0.00, 75.00));
    }

    public function test_tentukan_predikat(): void
    {
        $this->assertEquals('A (Sangat Baik)', NilaiSumatifTp::tentukanPredikat(95.00));
        $this->assertEquals('A (Sangat Baik)', NilaiSumatifTp::tentukanPredikat(90.00));
        $this->assertEquals('B (Baik)', NilaiSumatifTp::tentukanPredikat(85.00));
        $this->assertEquals('B (Baik)', NilaiSumatifTp::tentukanPredikat(80.00));
        $this->assertEquals('C (Cukup / Tuntas)', NilaiSumatifTp::tentukanPredikat(78.00));
        $this->assertEquals('C (Cukup / Tuntas)', NilaiSumatifTp::tentukanPredikat(75.00));
        $this->assertEquals('D (Perlu Bimbingan)', NilaiSumatifTp::tentukanPredikat(74.00));
        $this->assertEquals('D (Perlu Bimbingan)', NilaiSumatifTp::tentukanPredikat(50.00));
    }
}
