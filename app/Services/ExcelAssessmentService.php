<?php

namespace App\Services;

use App\Models\LingkupMateri;
use App\Models\MataPelajaran;
use App\Models\NilaiSumatifTp;
use App\Models\Siswa;
use App\Models\TujuanPembelajaran;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExcelAssessmentService
{
    protected PersonalizationService $personalizationService;

    public function __construct(PersonalizationService $personalizationService)
    {
        $this->personalizationService = $personalizationService;
    }

    /**
     * Generate template Excel terformat khusus untuk mata pelajaran & kelas terpilih.
     * Header Row 1: Lingkup Materi (Merged)
     * Header Row 2: Kode TP
     * Data Rows: Siswa & Nilai
     */
    public function exportTemplate(MataPelajaran $mapel, string $kelas): StreamedResponse
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Matriks Nilai TP');

        // Ambil data Lingkup Materi & TP untuk Mapel ini
        $lingkupMateris = LingkupMateri::where('mata_pelajaran_id', $mapel->id)
            ->with('tujuanPembelajarans')
            ->orderBy('urutan')
            ->get();

        // Jika belum ada TP sama sekali, buatkan placeholder struktur standar
        if ($lingkupMateris->isEmpty() || $lingkupMateris->every(fn($lm) => $lm->tujuanPembelajarans->isEmpty())) {
            $lingkupMateris = collect();
            for ($m = 1; $m <= 2; $m++) {
                $lm = new LingkupMateri(['kode_lm' => "Materi {$m}", 'judul_materi' => "Lingkup Materi {$m}"]);
                $lm->setRelation('tujuanPembelajarans', collect([
                    new TujuanPembelajaran(['kode_tp' => "TP {$m}.1", 'deskripsi_tp' => "Indikator Tujuan Pembelajaran {$m}.1"]),
                    new TujuanPembelajaran(['kode_tp' => "TP {$m}.2", 'deskripsi_tp' => "Indikator Tujuan Pembelajaran {$m}.2"]),
                ]));
                $lingkupMateris->push($lm);
            }
        }

        // Ambil data siswa di kelas ini
        $siswas = Siswa::where('kelas', $kelas)
            ->with(['nilaiSumatifTps'])
            ->orderBy('nama')
            ->get();

        // Map nilai yang sudah ada: [siswa_id][tp_id] => nilai
        $scoreMap = [];
        foreach ($siswas as $s) {
            foreach ($s->nilaiSumatifTps as $nst) {
                $scoreMap[$s->id][$nst->tujuan_pembelajaran_id] = $nst->nilai;
            }
        }

        // --- ROW 1 & 2: HEADER SETUP ---
        $sheet->setCellValue('A1', 'NO');
        $sheet->setCellValue('A2', 'NO');
        $sheet->mergeCells('A1:A2');

        $sheet->setCellValue('B1', 'NAMA SISWA');
        $sheet->setCellValue('B2', 'NAMA SISWA');
        $sheet->mergeCells('B1:B2');

        $sheet->setCellValue('C1', 'NISN');
        $sheet->setCellValue('C2', 'NISN');
        $sheet->mergeCells('C1:C2');

        $colIdx = 4; // Start at Column D
        $tpColumns = []; // Map colIdx => TP object

        foreach ($lingkupMateris as $lm) {
            $tpList = $lm->tujuanPembelajarans;
            if ($tpList->isEmpty()) continue;

            $startCol = $colIdx;
            $startColLetter = Coordinate::stringFromColumnIndex($startCol);

            foreach ($tpList as $tp) {
                $curColLetter = Coordinate::stringFromColumnIndex($colIdx);
                // Row 2: Kode TP
                $sheet->setCellValue("{$curColLetter}2", $tp->kode_tp);
                $tpColumns[$colIdx] = $tp;
                $colIdx++;
            }

            $endCol = $colIdx - 1;
            $endColLetter = Coordinate::stringFromColumnIndex($endCol);

            // Row 1: Lingkup Materi (Merge spans across its TPs)
            $sheet->setCellValue("{$startColLetter}1", "{$lm->kode_lm}: {$lm->judul_materi}");
            if ($startCol < $endCol) {
                $sheet->mergeCells("{$startColLetter}1:{$endColLetter}1");
            }
        }

        $lastColIndex = $colIdx - 1;
        $lastColLetter = Coordinate::stringFromColumnIndex($lastColIndex);

        // --- DATA ROWS (ROW 3 ONWARDS) ---
        $rowNum = 3;
        if ($siswas->isNotEmpty()) {
            foreach ($siswas as $index => $siswa) {
                $sheet->setCellValue("A{$rowNum}", $index + 1);
                $sheet->setCellValue("B{$rowNum}", $siswa->nama);
                $sheet->setCellValueExplicit("C{$rowNum}", (string)$siswa->nisn, DataType::TYPE_STRING);

                foreach ($tpColumns as $cIdx => $tp) {
                    $colLetter = Coordinate::stringFromColumnIndex($cIdx);
                    $existingScore = $tp->id ? ($scoreMap[$siswa->id][$tp->id] ?? null) : null;
                    if ($existingScore !== null) {
                        $sheet->setCellValue("{$colLetter}{$rowNum}", (float)$existingScore);
                    }
                }
                $rowNum++;
            }
        } else {
            // Contoh baris dummy jika belum ada siswa
            for ($i = 1; $i <= 5; $i++) {
                $sheet->setCellValue("A{$rowNum}", $i);
                $sheet->setCellValue("B{$rowNum}", "Contoh Siswa SMA {$i}");
                $sheet->setCellValueExplicit("C{$rowNum}", "314120938" . $i, DataType::TYPE_STRING);
                $rowNum++;
            }
        }

        $lastDataRow = $rowNum - 1;

        // --- STYLING & FORMATTING ---
        // Header Styling Row 1 (Teal Background, Bold, Centered)
        $sheet->getStyle("A1:{$lastColLetter}1")->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 11,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '0F766E'], // Teal 700
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
        ]);

        // Header Styling Row 2 (Teal 600 Background, Bold, Centered)
        $sheet->getStyle("A2:{$lastColLetter}2")->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 10,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '0D9488'], // Teal 600
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // All Data Cells Borders
        $sheet->getStyle("A1:{$lastColLetter}{$lastDataRow}")->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'CBD5E1'], // Slate 300
                ],
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Center Alignment for No, NISN, and TP Scores
        $sheet->getStyle("A3:A{$lastDataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("C3:C{$lastDataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        if ($lastColIndex >= 4) {
            $sheet->getStyle("D3:{$lastColLetter}{$lastDataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }

        // Set row heights
        $sheet->getRowDimension(1)->setRowHeight(28);
        $sheet->getRowDimension(2)->setRowHeight(24);
        for ($r = 3; $r <= $lastDataRow; $r++) {
            $sheet->getRowDimension($r)->setRowHeight(22);
        }

        // Set Column Widths
        $sheet->getColumnDimension('A')->setWidth(6);
        $sheet->getColumnDimension('B')->setWidth(28);
        $sheet->getColumnDimension('C')->setWidth(16);
        for ($c = 4; $c <= $lastColIndex; $c++) {
            $colLetter = Coordinate::stringFromColumnIndex($c);
            $sheet->getColumnDimension($colLetter)->setWidth(14);
        }

        $filename = "Template_Matriks_Nilai_" . Str::slug($mapel->nama_mapel) . "_{$kelas}.xlsx";

        return new StreamedResponse(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Cache-Control' => 'max-age=0',
        ]);
    }

    /**
     * Import file Excel matriks nilai dan simpan ke database secara otomatis.
     */
    public function importExcel(UploadedFile $file, int $mataPelajaranId, string $kelas): array
    {
        $mapel = MataPelajaran::findOrFail($mataPelajaranId);
        $kkm = (float) $mapel->kkm;

        $spreadsheet = IOFactory::load($file->getRealPath());
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray(null, true, true, true);

        if (count($rows) < 3) {
            throw new \Exception('Format file Excel tidak valid atau data siswa kosong. Minimal harus ada 2 baris header dan 1 baris data.');
        }

        $headerRow1 = $rows[1] ?? []; // Lingkup Materi
        $headerRow2 = $rows[2] ?? []; // Kode TP

        // Deteksi kolom No, Nama, NISN
        $namaCol = null;
        $nisnCol = null;
        $tpColumns = []; // [colKey => ['lm_name' => '...', 'tp_code' => '...']]

        // Normalisasi forward-fill untuk merged cell di Header Row 1 (Lingkup Materi)
        $currentLmName = null;
        $normalizedHeader1 = [];
        foreach ($headerRow1 as $colKey => $val) {
            $cleanVal = trim((string)$val);
            if (!empty($cleanVal)) {
                $currentLmName = $cleanVal;
            }
            $normalizedHeader1[$colKey] = $currentLmName;
        }

        foreach ($headerRow2 as $colKey => $val) {
            $clean2 = strtoupper(trim((string)$val));
            $clean1 = strtoupper(trim((string)($headerRow1[$colKey] ?? '')));

            if ($clean2 === 'NAMA' || $clean2 === 'NAMA SISWA' || $clean1 === 'NAMA' || $clean1 === 'NAMA SISWA') {
                $namaCol = $colKey;
            } elseif ($clean2 === 'NISN' || $clean1 === 'NISN') {
                $nisnCol = $colKey;
            } elseif (!empty($val) && $colKey !== 'A' && $clean2 !== 'NO') {
                // Ini adalah kolom TP
                $tpCode = trim((string)$val);
                $lmName = $normalizedHeader1[$colKey] ?? 'Materi 1';

                $tpColumns[$colKey] = [
                    'lm_name' => $lmName,
                    'tp_code' => $tpCode,
                ];
            }
        }

        // Fallback default jika tidak terdeteksi via nama kolom
        if (!$namaCol) $namaCol = 'B';
        if (!$nisnCol) $nisnCol = 'C';

        if (empty($tpColumns)) {
            // Ambil semua kolom setelah NISN
            $cols = array_keys($headerRow2);
            $nisnIndex = array_search($nisnCol, $cols);
            $tpColsList = array_slice($cols, $nisnIndex + 1);

            foreach ($tpColsList as $cKey) {
                $tpVal = trim((string)($headerRow2[$cKey] ?? ''));
                if (!empty($tpVal)) {
                    $tpColumns[$cKey] = [
                        'lm_name' => $normalizedHeader1[$cKey] ?? 'Materi 1',
                        'tp_code' => $tpVal,
                    ];
                }
            }
        }

        if (empty($tpColumns)) {
            throw new \Exception('Tidak ditemukan kolom Tujuan Pembelajaran (TP) pada baris header ke-2.');
        }

        // Siapkan / Cache Lingkup Materi & TP di database
        $createdLmMap = []; // [lm_name] => LingkupMateri
        $createdTpMap = []; // [tp_code] => TujuanPembelajaran

        $existingLms = LingkupMateri::where('mata_pelajaran_id', $mapel->id)->with('tujuanPembelajarans')->get();
        foreach ($existingLms as $elm) {
            $createdLmMap[$elm->kode_lm] = $elm;
            $createdLmMap[$elm->judul_materi] = $elm;
            $createdLmMap["{$elm->kode_lm}: {$elm->judul_materi}"] = $elm;
            foreach ($elm->tujuanPembelajarans as $etp) {
                $createdTpMap[$etp->kode_tp] = $etp;
            }
        }

        $lmOrderCounter = $existingLms->count();

        // Siapkan struktur LM & TP dari file Excel
        foreach ($tpColumns as $colKey => $tpInfo) {
            $rawLm = $tpInfo['lm_name'];
            $rawTp = $tpInfo['tp_code'];

            // Parse nama LM (misal: "Materi 1: Struktur Sel" atau "Materi 1")
            $lmKode = 'Materi 1';
            $lmJudul = $rawLm;
            if (preg_match('/^(Materi\s*\d+)\s*[:\-]?\s*(.*)$/i', $rawLm, $matches)) {
                $lmKode = $matches[1];
                $lmJudul = !empty($matches[2]) ? $matches[2] : $matches[1];
            }

            if (!isset($createdLmMap[$rawLm]) && !isset($createdLmMap[$lmKode])) {
                $lmOrderCounter++;
                $newLm = LingkupMateri::create([
                    'mata_pelajaran_id' => $mapel->id,
                    'kode_lm' => $lmKode,
                    'judul_materi' => $lmJudul,
                    'urutan' => $lmOrderCounter,
                ]);
                $createdLmMap[$rawLm] = $newLm;
                $createdLmMap[$lmKode] = $newLm;
            }

            $currentLm = $createdLmMap[$rawLm] ?? $createdLmMap[$lmKode];

            if (!isset($createdTpMap[$rawTp])) {
                $newTp = TujuanPembelajaran::create([
                    'lingkup_materi_id' => $currentLm->id,
                    'kode_tp' => $rawTp,
                    'deskripsi_tp' => "Capaian indikator Tujuan Pembelajaran {$rawTp}",
                    'target_ketercapaian' => $kkm,
                ]);
                $createdTpMap[$rawTp] = $newTp;
            }
        }

        // Iterasi baris data (Row 3 s.d. selesai)
        $importedStudents = 0;
        $importedScores = 0;
        $affectedSiswaIds = [];

        DB::beginTransaction();
        try {
            for ($r = 3; $r <= count($rows); $r++) {
                $row = $rows[$r] ?? [];
                $nama = trim((string)($row[$namaCol] ?? ''));
                $nisn = trim((string)($row[$nisnCol] ?? ''));

                if (empty($nama) && empty($nisn)) {
                    continue; // Skip baris kosong
                }

                if (empty($nisn)) {
                    $nisn = 'NISN-' . strtoupper(Str::random(8));
                }

                // Cari atau buat siswa
                $siswa = Siswa::firstOrCreate(
                    ['nisn' => $nisn],
                    [
                        'nama' => $nama ?: "Siswa {$nisn}",
                        'kelas' => $kelas,
                        'gaya_belajar' => 'Visual',
                        'status' => 'Aktif',
                    ]
                );

                // Update nama / kelas jika berubah
                if (!empty($nama) && $siswa->nama !== $nama) {
                    $siswa->nama = $nama;
                    $siswa->save();
                }

                $affectedSiswaIds[] = $siswa->id;
                $importedStudents++;

                // Simpan skor untuk setiap kolom TP
                foreach ($tpColumns as $colKey => $tpInfo) {
                    $scoreRaw = $row[$colKey] ?? null;
                    if ($scoreRaw === null || $scoreRaw === '') {
                        continue;
                    }

                    $scoreClean = str_replace(',', '.', trim((string)$scoreRaw));

                    if (is_numeric($scoreClean)) {
                        $nilai = max(0, min(100, (float)$scoreClean));
                        $tpObj = $createdTpMap[$tpInfo['tp_code']] ?? null;

                        if ($tpObj) {
                            $target = $tpObj->target_ketercapaian ?: $kkm;
                            $status = NilaiSumatifTp::tentukanKetuntasan($nilai, $target);

                            NilaiSumatifTp::updateOrCreate(
                                [
                                    'siswa_id' => $siswa->id,
                                    'tujuan_pembelajaran_id' => $tpObj->id,
                                ],
                                [
                                    'nilai' => $nilai,
                                    'status_ketuntasan' => $status,
                                    'catatan' => $status === 'Tuntas' ? 'Tuntas mencapai target TP' : 'Perlu bimbingan remedial',
                                ]
                            );
                            $importedScores++;
                        }
                    }
                }
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }

        // Generate ulang personalisasi AI untuk siswa-siswa terkait
        foreach (array_unique($affectedSiswaIds) as $sId) {
            $s = Siswa::find($sId);
            if ($s) {
                $this->personalizationService->generateForSiswa($s);
            }
        }

        return [
            'students_count' => $importedStudents,
            'scores_count' => $importedScores,
            'tps_count' => count($tpColumns),
            'mapel_name' => $mapel->nama_mapel,
            'kelas' => $kelas,
        ];
    }
}
