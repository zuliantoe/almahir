<?php

use Illuminate\Support\Facades\Route;
use App\Modules\ManajemenAsetDanAsrama\Controllers\DashboardController;
use App\Modules\ManajemenAsetDanAsrama\Controllers\PengajuanController;
use App\Modules\ManajemenAsetDanAsrama\Controllers\PersetujuanController;
use App\Modules\ManajemenAsetDanAsrama\Controllers\PengadaanController;
use App\Modules\ManajemenAsetDanAsrama\Controllers\AsetController;
use App\Modules\ManajemenAsetDanAsrama\Controllers\KamarController;
use App\Modules\ManajemenAsetDanAsrama\Controllers\PenghuniController;
use App\Modules\ManajemenAsetDanAsrama\Controllers\JadwalPiketController;
use App\Modules\ManajemenAsetDanAsrama\Controllers\KerusakanController;
use App\Modules\ManajemenAsetDanAsrama\Controllers\PemeliharaanController;
use App\Modules\ManajemenAsetDanAsrama\Controllers\TrashController;

/*
|--------------------------------------------------------------------------
| ManajemenAsetDanAsrama Module Routes
|--------------------------------------------------------------------------
|
| Routes are automatically prefixed with '/manajemenasetdanasrama' and named 'manajemenasetdanasrama.*'
| Middleware: web (auto-applied by ModuleServiceProvider)
|
*/

Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('index');
    
    // Pengajuan Aset
// Pengajuan Aset
Route::prefix('pengajuan')->name('pengajuan.')->group(function () {
    Route::get('/', [PengajuanController::class, 'index'])->name('index');
    Route::get('create', [PengajuanController::class, 'create'])->name('create');
    Route::post('/', [PengajuanController::class, 'store'])->name('store');
    Route::get('{id}', [PengajuanController::class, 'show'])->name('show');
    Route::get('{id}/edit', [PengajuanController::class, 'edit'])->name('edit');
    Route::put('{id}', [PengajuanController::class, 'update'])->name('update');
    Route::delete('{id}', [PengajuanController::class, 'destroy'])->name('destroy');
    // Route untuk ajukan ulang (resubmit) pengajuan yang ditolak
    Route::post('{id}/ajukan-ulang', [PengajuanController::class, 'ajukanUlang'])->name('ajukan-ulang');
});
    
    // Persetujuan
    Route::prefix('persetujuan')->name('persetujuan.')->group(function () {
        Route::get('/', [PersetujuanController::class, 'index'])->name('index');
        Route::post('{id}/approve', [PersetujuanController::class, 'approve'])->name('approve');
        Route::post('{id}/reject', [PersetujuanController::class, 'reject'])->name('reject');
    });
    
    // Pengadaan
    Route::prefix('pengadaan')->name('pengadaan.')->group(function () {
        Route::get('/', [PengadaanController::class, 'index'])->name('index');
        Route::get('{id}/proses', [PengadaanController::class, 'proses'])->name('proses');
        Route::post('store', [PengadaanController::class, 'store'])->name('store');
        Route::post('{id}/selesai', [PengadaanController::class, 'selesai'])->name('selesai');
    });
    
    // Master Aset
    Route::prefix('aset')->name('aset.')->group(function () {
        Route::get('/', [AsetController::class, 'index'])->name('index');
        Route::get('{id}', [AsetController::class, 'show'])->name('show');
        Route::get('{id}/edit', [AsetController::class, 'edit'])->name('edit');
        Route::put('{id}', [AsetController::class, 'update'])->name('update');
        Route::delete('{id}', [AsetController::class, 'destroy'])->name('destroy');
        Route::post('{id}/duplicate', [AsetController::class, 'duplicate'])->name('duplicate');
    });
    
    // Kamar
    Route::prefix('kamar')->name('kamar.')->group(function () {
        Route::get('/', [KamarController::class, 'index'])->name('index');
        Route::get('create', [KamarController::class, 'create'])->name('create');
        Route::post('/', [KamarController::class, 'store'])->name('store');
        Route::get('{id}', [KamarController::class, 'show'])->name('show');
        Route::get('{id}/edit', [KamarController::class, 'edit'])->name('edit');
        Route::put('{id}', [KamarController::class, 'update'])->name('update');
        Route::delete('{id}', [KamarController::class, 'destroy'])->name('destroy');
    });
    
    // Penghuni Kamar
    Route::prefix('penghuni')->name('penghuni.')->group(function () {
        Route::get('/', [PenghuniController::class, 'index'])->name('index');
        Route::get('create', [PenghuniController::class, 'create'])->name('create');
        Route::post('/', [PenghuniController::class, 'store'])->name('store');
        Route::get('{id}/edit', [PenghuniController::class, 'edit'])->name('edit');
        Route::put('{id}', [PenghuniController::class, 'update'])->name('update');
        Route::delete('{id}', [PenghuniController::class, 'destroy'])->name('destroy');
    });
    
    // Jadwal Piket
    Route::prefix('jadwal-piket')->name('jadwal-piket.')->group(function () {
        Route::get('/', [JadwalPiketController::class, 'index'])->name('index');
        Route::get('create', [JadwalPiketController::class, 'create'])->name('create');
        Route::post('/', [JadwalPiketController::class, 'store'])->name('store');
        Route::get('{id}/edit', [JadwalPiketController::class, 'edit'])->name('edit');
        Route::put('{id}', [JadwalPiketController::class, 'update'])->name('update');
        Route::delete('{id}', [JadwalPiketController::class, 'destroy'])->name('destroy');
        Route::post('{id}/selesai', [JadwalPiketController::class, 'selesai'])->name('selesai');
        Route::post('auto-generate', [JadwalPiketController::class, 'autoGenerate'])->name('auto-generate');
    });
    
    // Kerusakan
    Route::prefix('kerusakan')->name('kerusakan.')->group(function () {
        Route::get('/', [KerusakanController::class, 'index'])->name('index');
        Route::get('create', [KerusakanController::class, 'create'])->name('create');
        Route::post('/', [KerusakanController::class, 'store'])->name('store');
        Route::get('{id}/edit', [KerusakanController::class, 'edit'])->name('edit');
        Route::put('{id}', [KerusakanController::class, 'update'])->name('update');
        Route::delete('{id}', [KerusakanController::class, 'destroy'])->name('destroy');
        Route::post('{id}/proses-pemeliharaan', [KerusakanController::class, 'prosesPemeliharaan'])->name('proses-pemeliharaan');
    });
    
    // Pemeliharaan
    Route::prefix('pemeliharaan')->name('pemeliharaan.')->group(function () {
        Route::get('/', [PemeliharaanController::class, 'index'])->name('index');
        Route::get('create', [PemeliharaanController::class, 'create'])->name('create');
        Route::post('/', [PemeliharaanController::class, 'store'])->name('store');
        Route::get('{id}/edit', [PemeliharaanController::class, 'edit'])->name('edit');
        Route::put('{id}', [PemeliharaanController::class, 'update'])->name('update');
        Route::delete('{id}', [PemeliharaanController::class, 'destroy'])->name('destroy');
        Route::post('{id}/selesai', [PemeliharaanController::class, 'selesai'])->name('selesai');
    });
    
    // Trash
    Route::prefix('trash')->name('trash.')->group(function () {
        Route::get('/', [TrashController::class, 'index'])->name('index');
        Route::post('{type}/{id}/restore', [TrashController::class, 'restore'])->name('restore');
        Route::delete('{type}/{id}/force-delete', [TrashController::class, 'forceDelete'])->name('force-delete');
    });
});