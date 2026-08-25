<?php

namespace Tests\Feature;

use App\Models\MataPelajaran;
use App\Models\NilaiSumatifTp;
use App\Models\Siswa;
use App\Models\TujuanPembelajaran;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MatrixActionsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_can_quick_update_student_from_matrix(): void
    {
        $siswa = Siswa::first();
        $mapel = MataPelajaran::first();

        $response = $this->post(route('penilaian.matrix.quick-update-siswa'), [
            'siswa_id' => $siswa->id,
            'nama' => 'Nama Siswa Terupdate',
            'nisn' => '3149991122',
            'gaya_belajar' => 'Kinestetik',
            'mata_pelajaran_id' => $mapel->id,
            'kelas' => $siswa->kelas,
        ]);

        $response->assertRedirect(route('penilaian.matrix', [
            'mata_pelajaran_id' => $mapel->id,
            'kelas' => $siswa->kelas,
        ]));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('siswas', [
            'id' => $siswa->id,
            'nama' => 'Nama Siswa Terupdate',
            'nisn' => '3149991122',
            'gaya_belajar' => 'Kinestetik',
        ]);
    }

    public function test_can_bulk_reset_scores_from_matrix(): void
    {
        $mapel = MataPelajaran::first();
        $siswas = Siswa::take(2)->get();
        $siswaIds = $siswas->pluck('id')->toArray();

        // Pastikan ada nilai
        $scoreCountBefore = NilaiSumatifTp::whereIn('siswa_id', $siswaIds)->count();
        $this->assertGreaterThan(0, $scoreCountBefore);

        $response = $this->post(route('penilaian.matrix.reset-scores'), [
            'siswa_ids' => $siswaIds,
            'mata_pelajaran_id' => $mapel->id,
            'kelas' => 'X-A',
        ]);

        $response->assertRedirect(route('penilaian.matrix', [
            'mata_pelajaran_id' => $mapel->id,
            'kelas' => 'X-A',
        ]));
        $response->assertSessionHas('success');

        // Nilai untuk mapel ini harus terhapus
        $tpIds = TujuanPembelajaran::whereHas('lingkupMateri', function ($q) use ($mapel) {
            $q->where('mata_pelajaran_id', $mapel->id);
        })->pluck('id')->toArray();

        $scoreCountAfter = NilaiSumatifTp::whereIn('siswa_id', $siswaIds)
            ->whereIn('tujuan_pembelajaran_id', $tpIds)
            ->count();

        $this->assertEquals(0, $scoreCountAfter);
    }

    public function test_can_bulk_delete_students_from_matrix(): void
    {
        $mapel = MataPelajaran::first();
        $siswas = Siswa::take(2)->get();
        $siswaIds = $siswas->pluck('id')->toArray();

        $response = $this->post(route('penilaian.matrix.bulk-delete-siswa'), [
            'siswa_ids' => $siswaIds,
            'mata_pelajaran_id' => $mapel->id,
            'kelas' => 'X-A',
        ]);

        $response->assertRedirect(route('penilaian.matrix', [
            'mata_pelajaran_id' => $mapel->id,
            'kelas' => 'X-A',
        ]));
        $response->assertSessionHas('success');

        foreach ($siswaIds as $id) {
            $this->assertDatabaseMissing('siswas', ['id' => $id]);
        }
    }
}
