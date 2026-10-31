<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\LingkupMateriController;
use App\Http\Controllers\MataPelajaranController;
use App\Http\Controllers\PenilaianController;
use App\Http\Controllers\PersonalisasiController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\TujuanPembelajaranController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - Sistem Penilaian Sumatif per TP (Kurikulum Merdeka SMA)
|--------------------------------------------------------------------------
*/

// Dashboard
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// Master Siswa SMA
Route::resource('siswa', SiswaController::class);

// Master Mata Pelajaran SMA
Route::resource('matapelajaran', MataPelajaranController::class);

// Lingkup Materi & Tujuan Pembelajaran (TP)
Route::resource('tujuan-pembelajaran', TujuanPembelajaranController::class);
Route::post('lingkup-materi', [LingkupMateriController::class, 'store'])->name('lingkup-materi.store');
Route::put('lingkup-materi/{lingkupMateri}', [LingkupMateriController::class, 'update'])->name('lingkup-materi.update');
Route::delete('lingkup-materi/{lingkupMateri}', [LingkupMateriController::class, 'destroy'])->name('lingkup-materi.destroy');

// Penilaian Sumatif per TP (Termasuk Spreadsheet Matrix Grid View & Excel Import/Export)
Route::prefix('penilaian')->name('penilaian.')->group(function () {
    Route::get('/', [PenilaianController::class, 'index'])->name('index');
    Route::get('/matrix', [PenilaianController::class, 'matrix'])->name('matrix');
    Route::post('/matrix', [PenilaianController::class, 'matrixSave'])->name('matrix.save');
    Route::post('/matrix/reset-scores', [PenilaianController::class, 'matrixResetScores'])->name('matrix.reset-scores');
    Route::post('/matrix/bulk-delete-siswa', [PenilaianController::class, 'matrixBulkDeleteSiswa'])->name('matrix.bulk-delete-siswa');
    Route::post('/matrix/quick-update-siswa', [PenilaianController::class, 'matrixQuickUpdateSiswa'])->name('matrix.quick-update-siswa');
    Route::get('/template-excel', [PenilaianController::class, 'exportTemplate'])->name('template-excel');
    Route::post('/import-excel', [PenilaianController::class, 'importExcel'])->name('import-excel');
    Route::get('/create', [PenilaianController::class, 'create'])->name('create');
    Route::post('/', [PenilaianController::class, 'store'])->name('store');
    Route::get('/{penilaian}/edit', [PenilaianController::class, 'edit'])->name('edit');
    Route::put('/{penilaian}', [PenilaianController::class, 'update'])->name('update');
    Route::delete('/{penilaian}', [PenilaianController::class, 'destroy'])->name('destroy');
});

// Personalisasi Pembelajaran Adaptif (PLA) & AI Diagnostik
Route::prefix('personalisasi')->name('personalisasi.')->group(function () {
    Route::get('/', [PersonalisasiController::class, 'index'])->name('index');
    Route::post('/generate', [PersonalisasiController::class, 'generate'])->name('generate');
    Route::get('/{siswa}', [PersonalisasiController::class, 'show'])->name('show');
    Route::post('/{siswa}/ai-analyze', [PersonalisasiController::class, 'aiAnalyze'])->name('ai-analyze');
    Route::post('/{siswa}/catatan', [PersonalisasiController::class, 'updateCatatan'])->name('catatan');
});

// Laporan & Cetak Raport Sumatif
Route::prefix('laporan')->name('laporan.')->group(function () {
    Route::get('/', [LaporanController::class, 'index'])->name('index');
    Route::get('/cetak', [LaporanController::class, 'cetak'])->name('cetak');
});

// Pengaturan Sistem Sekolah & AI Engine
Route::prefix('setting')->name('setting.')->group(function () {
    Route::get('/', [SettingController::class, 'index'])->name('index');
    Route::post('/', [SettingController::class, 'update'])->name('update');
    Route::post('/test-connection', [SettingController::class, 'testConnection'])->name('test-connection');
});
