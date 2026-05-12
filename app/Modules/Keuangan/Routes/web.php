<?php

use Illuminate\Support\Facades\Route;
use Modules\Keuangan\Controllers\KeuanganController;
use Modules\Keuangan\Controllers\PemasukanController;
use Modules\Keuangan\Controllers\PengeluaranController;
use Modules\Keuangan\Controllers\SumberController;
use Modules\Keuangan\Controllers\TujuanController;
use Modules\Keuangan\Controllers\TransaksiController;
use Modules\Keuangan\Controllers\UangSakuController;
use Modules\Keuangan\Controllers\TagihanSantriController;
use Modules\Keuangan\Controllers\PembayaranSantriController;

/*
|--------------------------------------------------------------------------
| Keuangan Module Routes
|--------------------------------------------------------------------------
|
| Routes are automatically prefixed with '/keuangan' and named 'keuangan.*'
| Middleware: web (auto-applied by ModuleServiceProvider)
|
*/

Route::middleware(['auth'])->group(function () {
    Route::get('/', [KeuanganController::class, 'index'])->name('index');
    Route::resource('pemasukans', PemasukanController::class);
    Route::resource('pengeluarans', PengeluaranController::class);
    Route::resource('sumbers', SumberController::class);
    Route::resource('tujuans', TujuanController::class);
    
    Route::resource('uangsakus', UangSakuController::class);
    Route::patch('uangsakus/{id}/status', [UangSakuController::class, 'updateStatus'])->name('uangsakus.updateStatus');
    Route::resource('tagihansantris', TagihanSantriController::class);
    Route::resource('pembayaransantris', PembayaranSantriController::class);
    
    // Laporan Transaksi
    Route::get('transaksis/print', [TransaksiController::class, 'print'])->name('transaksis.print');
    Route::get('transaksis', [TransaksiController::class, 'index'])->name('transaksis.index');
});
