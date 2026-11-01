<?php

namespace App\Http\Controllers;

use App\Models\LingkupMateri;
use App\Models\MataPelajaran;
use App\Models\NilaiSumatifTp;
use App\Models\Siswa;
use App\Models\TujuanPembelajaran;
use App\Services\ExcelAssessmentService;
use App\Services\PersonalizationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PenilaianController extends Controller
{
    protected PersonalizationService $personalizationService;
    protected ExcelAssessmentService $excelService;

    public function __construct(
        PersonalizationService $personalizationService,
        ExcelAssessmentService $excelService
    ) {
        $this->personalizationService = $personalizationService;
        $this->excelService = $excelService;
    }

    /**
     * Download format / template Excel penilaian sumatif per TP
     */
    public function exportTemplate(Request $request): StreamedResponse
    {
        $mapelId = $request->query('mata_pelajaran_id');
        $kelas = $request->query('kelas', 'X-A');

        $mapel = MataPelajaran::findOrFail($mapelId);

        return $this->excelService->exportTemplate($mapel, $kelas);
    }

    /**
     * Upload & import file Excel matriks nilai sumatif per TP
     */
    public function importExcel(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'file_excel' => 'required|file|mimes:xlsx,xls,csv|max:10240',
            'mata_pelajaran_id' => 'required|exists:mata_pelajarans,id',
            'kelas' => 'required|string',
        ]);

        try {
            $result = $this->excelService->importExcel(
                $request->file('file_excel'),
                (int) $validated['mata_pelajaran_id'],
                $validated['kelas']
            );

            return redirect()->route('penilaian.matrix', [
                'mata_pelajaran_id' => $validated['mata_pelajaran_id'],
                'kelas' => $validated['kelas'],
            ])->with('success', "Berhasil mengimpor data Excel! {$result['students_count']} siswa, {$result['scores_count']} butir nilai TP berhasil disinkronkan, dan personalisasi AI telah diperbarui.");
        } catch (\Throwable $e) {
            return redirect()->back()
                ->with('error', 'Gagal mengimpor file Excel: ' . $e->getMessage());
        }
    }

    public function index(Request $request): View
    {
        $mapels = MataPelajaran::all();
        $selectedMapelId = $request->query('mata_pelajaran_id', $mapels->first()?->id);
        $selectedKelas = $request->query('kelas', 'X-A');

        $kelasList = Siswa::select('kelas')->distinct()->pluck('kelas');

        $selectedMapel = $selectedMapelId ? MataPelajaran::find($selectedMapelId) : null;

        $siswas = Siswa::where('kelas', $selectedKelas)
            ->with(['nilaiSumatifTps.tujuanPembelajaran.lingkupMateri', 'personalisasis'])
            ->orderBy('nama')
            ->get();

        // Ambil semua TP untuk mapel ini
        $tujuanPembelajarans = [];
        if ($selectedMapel) {
            $tujuanPembelajarans = TujuanPembelajaran::whereHas('lingkupMateri', function ($q) use ($selectedMapel) {
                $q->where('mata_pelajaran_id', $selectedMapel->id);
            })->with('lingkupMateri')->get();
        }

        return view('penilaian.index', compact(
            'mapels',
            'selectedMapel',
            'selectedKelas',
            'kelasList',
            'siswas',
            'tujuanPembelajarans'
        ));
    }

    /**
     * Tampilan Matriks Input Nilai per TP (Spreadsheet Grid View sesuai Gambar 1)
     */
    public function matrix(Request $request): View
    {
        $mapels = MataPelajaran::all();
        $selectedMapelId = $request->query('mata_pelajaran_id', $mapels->first()?->id);
        $selectedKelas = $request->query('kelas', 'X-A');

        $kelasList = Siswa::select('kelas')->distinct()->pluck('kelas');
        $selectedMapel = $selectedMapelId ? MataPelajaran::find($selectedMapelId) : null;

        $lingkupMateris = [];
        $allTps = [];
        if ($selectedMapel) {
            $lingkupMateris = LingkupMateri::where('mata_pelajaran_id', $selectedMapel->id)
                ->with('tujuanPembelajarans')
                ->orderBy('urutan')
                ->get();

            foreach ($lingkupMateris as $lm) {
                foreach ($lm->tujuanPembelajarans as $tp) {
                    $allTps[] = $tp;
                }
            }
        }

        $siswas = Siswa::where('kelas', $selectedKelas)
            ->with(['nilaiSumatifTps'])
            ->orderBy('nama')
            ->get();

        // Buat map nilai: [siswa_id][tp_id] => nilai
        $scoreMap = [];
        foreach ($siswas as $siswa) {
            foreach ($siswa->nilaiSumatifTps as $nst) {
                $scoreMap[$siswa->id][$nst->tujuan_pembelajaran_id] = $nst->nilai;
            }
        }

        return view('penilaian.matrix', compact(
            'mapels',
            'selectedMapel',
            'selectedKelas',
            'kelasList',
            'lingkupMateris',
            'allTps',
            'siswas',
            'scoreMap'
        ));
    }

    /**
     * Simpan masal nilai matriks per-TP (Spreadsheet Batch Save)
     */
    public function matrixSave(Request $request): RedirectResponse
    {
        $mapelId = $request->input('mata_pelajaran_id');
        $kelas = $request->input('kelas', 'X-A');
        $matrixScores = $request->input('scores', []); // [siswa_id][tp_id] => value

        $mapel = MataPelajaran::findOrFail($mapelId);
        $kkm = (float) $mapel->kkm;

        $affectedSiswaIds = [];
        $siswaIds = array_keys($matrixScores);
        $siswasMap = Siswa::whereIn('id', $siswaIds)->get()->keyBy('id');
        $allTpsMap = TujuanPembelajaran::all()->keyBy('id');

        // Ambil data nilai yang sudah ada di database untuk deteksi perubahan aktual
        $existingScores = NilaiSumatifTp::whereIn('siswa_id', $siswaIds)
            ->get()
            ->groupBy('siswa_id');

        \Illuminate\Support\Facades\DB::transaction(function () use ($matrixScores, $siswasMap, $allTpsMap, $kkm, $existingScores, &$affectedSiswaIds) {
            foreach ($matrixScores as $siswaId => $tpScores) {
                $siswa = $siswasMap->get($siswaId);
                if (!$siswa) continue;

                $studentExisting = $existingScores->get($siswaId)?->keyBy('tujuan_pembelajaran_id');
                $isStudentModified = false;

                foreach ($tpScores as $tpId => $nilaiRaw) {
                    if ($nilaiRaw === null || $nilaiRaw === '') continue;

                    $nilai = max(0, min(100, (float) $nilaiRaw));
                    $existingItem = $studentExisting?->get($tpId);

                    // Hanya simpan jika nilai berubah atau belum pernah ada
                    if (!$existingItem || (float) $existingItem->nilai !== $nilai) {
                        $tp = $allTpsMap->get($tpId);
                        $target = $tp ? $tp->target_ketercapaian : $kkm;
                        $status = NilaiSumatifTp::tentukanKetuntasan($nilai, $target);

                        NilaiSumatifTp::updateOrCreate(
                            [
                                'siswa_id' => $siswa->id,
                                'tujuan_pembelajaran_id' => $tpId,
                            ],
                            [
                                'nilai' => $nilai,
                                'status_ketuntasan' => $status,
                                'catatan' => $status === 'Tuntas' ? 'Tuntas mencapai target TP' : 'Perlu bimbingan remedial',
                            ]
                        );

                        $isStudentModified = true;
                    }
                }

                if ($isStudentModified) {
                    $affectedSiswaIds[] = $siswa->id;
                }
            }
        });

        // Sinkronkan data diagnostik secara instan hanya untuk siswa yang nilainya benar-benar berubah
        if (!empty($affectedSiswaIds)) {
            $affectedStudents = Siswa::with('nilaiSumatifTps.tujuanPembelajaran.lingkupMateri.mataPelajaran')
                ->whereIn('id', array_unique($affectedSiswaIds))
                ->get();
            foreach ($affectedStudents as $s) {
                $this->personalizationService->fallbackGenerate($s, 'Sinkronisasi instan matriks nilai');
            }
        }

        $count = count($affectedSiswaIds);
        $message = $count > 0
            ? "Matriks nilai sumatif per TP berhasil disimpan ({$count} siswa diperbarui)!"
            : 'Matriks nilai telah sesuai, tidak ada perubahan data.';

        return redirect()->route('penilaian.matrix', ['mata_pelajaran_id' => $mapelId, 'kelas' => $kelas])
            ->with('success', $message);
    }

    public function create(Request $request): View
    {
        $siswas = Siswa::where('status', 'Aktif')->orderBy('nama')->get();
        $mapels = MataPelajaran::all();
        $tps = TujuanPembelajaran::with('lingkupMateri.mataPelajaran')->orderBy('kode_tp')->get();

        $selectedSiswaId = $request->query('siswa_id');

        return view('penilaian.create', compact('siswas', 'mapels', 'tps', 'selectedSiswaId'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'siswa_id' => 'required|exists:siswas,id',
            'tujuan_pembelajaran_id' => 'required|exists:tujuan_pembelajarans,id',
            'nilai' => 'required|numeric|min:0|max:100',
            'catatan' => 'nullable|string',
        ]);

        $tp = TujuanPembelajaran::findOrFail($validated['tujuan_pembelajaran_id']);
        $status = NilaiSumatifTp::tentukanKetuntasan((float)$validated['nilai'], $tp->target_ketercapaian);

        $penilaian = NilaiSumatifTp::updateOrCreate(
            [
                'siswa_id' => $validated['siswa_id'],
                'tujuan_pembelajaran_id' => $validated['tujuan_pembelajaran_id'],
            ],
            [
                'nilai' => $validated['nilai'],
                'status_ketuntasan' => $status,
                'catatan' => $validated['catatan'] ?? ($status === 'Tuntas' ? 'Tuntas' : 'Perlu remedial'),
            ]
        );

        $siswa = Siswa::find($validated['siswa_id']);
        if ($siswa) {
            $this->personalizationService->generateForSiswa($siswa);
        }

        return redirect()->route('penilaian.index')
            ->with('success', "Nilai untuk {$tp->kode_tp} berhasil disimpan.");
    }

    public function edit(NilaiSumatifTp $penilaian): View
    {
        $penilaian->load(['siswa', 'tujuanPembelajaran.lingkupMateri.mataPelajaran']);
        return view('penilaian.edit', compact('penilaian'));
    }

    public function update(Request $request, NilaiSumatifTp $penilaian): RedirectResponse
    {
        $validated = $request->validate([
            'nilai' => 'required|numeric|min:0|max:100',
            'catatan' => 'nullable|string',
        ]);

        $tp = $penilaian->tujuanPembelajaran;
        $status = NilaiSumatifTp::tentukanKetuntasan((float)$validated['nilai'], $tp ? $tp->target_ketercapaian : 75);

        $penilaian->update([
            'nilai' => $validated['nilai'],
            'status_ketuntasan' => $status,
            'catatan' => $validated['catatan'],
        ]);

        $siswa = $penilaian->siswa;
        if ($siswa) {
            $this->personalizationService->generateForSiswa($siswa);
        }

        return redirect()->route('penilaian.index')
            ->with('success', 'Nilai sumatif TP berhasil diperbarui.');
    }

    public function destroy(NilaiSumatifTp $penilaian): RedirectResponse
    {
        $siswa = $penilaian->siswa;
        $penilaian->delete();

        if ($siswa) {
            $this->personalizationService->generateForSiswa($siswa);
        }

        return redirect()->route('penilaian.index')
            ->with('success', 'Nilai sumatif TP berhasil dihapus.');
    }

    /**
     * Reset / hapus nilai sumatif TP untuk satu atau banyak siswa terpilih pada mapel & kelas
     */
    public function matrixResetScores(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'siswa_ids' => 'required|array',
            'siswa_ids.*' => 'exists:siswas,id',
            'mata_pelajaran_id' => 'required|exists:mata_pelajarans,id',
            'kelas' => 'required|string',
        ]);

        $mapelId = (int) $validated['mata_pelajaran_id'];
        $kelas = $validated['kelas'];
        $siswaIds = $validated['siswa_ids'];

        // Ambil semua TP milik mata pelajaran ini
        $tpIds = TujuanPembelajaran::whereHas('lingkupMateri', function ($q) use ($mapelId) {
            $q->where('mata_pelajaran_id', $mapelId);
        })->pluck('id')->toArray();

        // Hapus nilai
        $deletedCount = NilaiSumatifTp::whereIn('siswa_id', $siswaIds)
            ->whereIn('tujuan_pembelajaran_id', $tpIds)
            ->delete();

        // Refresh personalisasi AI
        foreach ($siswaIds as $sId) {
            $s = Siswa::find($sId);
            if ($s) {
                $this->personalizationService->generateForSiswa($s);
            }
        }

        return redirect()->route('penilaian.matrix', ['mata_pelajaran_id' => $mapelId, 'kelas' => $kelas])
            ->with('success', "Berhasil mereset {$deletedCount} butir nilai untuk " . count($siswaIds) . " siswa terpilih.");
    }

    /**
     * Bulk delete data siswa dari matriks kelas
     */
    public function matrixBulkDeleteSiswa(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'siswa_ids' => 'required|array',
            'siswa_ids.*' => 'exists:siswas,id',
            'mata_pelajaran_id' => 'required|exists:mata_pelajarans,id',
            'kelas' => 'required|string',
        ]);

        $mapelId = (int) $validated['mata_pelajaran_id'];
        $kelas = $validated['kelas'];
        $siswaIds = $validated['siswa_ids'];
        $count = count($siswaIds);

        Siswa::whereIn('id', $siswaIds)->delete();

        return redirect()->route('penilaian.matrix', ['mata_pelajaran_id' => $mapelId, 'kelas' => $kelas])
            ->with('success', "Berhasil menghapus {$count} data siswa dari sistem.");
    }

    /**
     * Quick update data siswa dari popup modal di matriks
     */
    public function matrixQuickUpdateSiswa(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'siswa_id' => 'required|exists:siswas,id',
            'nama' => 'required|string|max:255',
            'nisn' => 'required|string|max:20',
            'gaya_belajar' => 'required|in:Visual,Auditori,Kinestetik,Baca/Tulis',
            'mata_pelajaran_id' => 'required|exists:mata_pelajarans,id',
            'kelas' => 'required|string',
        ]);

        $siswa = Siswa::findOrFail($validated['siswa_id']);
        $siswa->update([
            'nama' => $validated['nama'],
            'nisn' => $validated['nisn'],
            'gaya_belajar' => $validated['gaya_belajar'],
        ]);

        // Refresh personalisasi AI
        $this->personalizationService->generateForSiswa($siswa);

        return redirect()->route('penilaian.matrix', [
            'mata_pelajaran_id' => $validated['mata_pelajaran_id'],
            'kelas' => $validated['kelas']
        ])->with('success', "Data siswa {$siswa->nama} berhasil diperbarui.");
    }
}
