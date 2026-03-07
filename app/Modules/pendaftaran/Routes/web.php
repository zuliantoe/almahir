<?php

use Illuminate\Support\Facades\Route;
use Modules\Pendaftaran\Controllers\SeleksiController;
use Modules\Pendaftaran\Controllers\PendaftaranController;


Route::middleware(['web'])->group(function () {

    Route::get('/', [PendaftaranController::class, 'create'])
        ->name('create');

    Route::post('/', [PendaftaranController::class, 'store'])
        ->name('store');
    Route::get('/cek-nisn/{nisn}', function ($nisn) {

        return response()->json([
            'exists' => \Modules\Pendaftaran\Models\Pendaftaran::where('nisn', $nisn)->exists()
        ]);
    });

    Route::get('/cek-email/{email}', function ($email) {

        return response()->json([
            'exists' => \Modules\Pendaftaran\Models\Pendaftaran::where('email', $email)->exists()
        ]);
    });
});

Route::prefix('admin')->group(function () {

    // URL: pendaftaran/admin/pendaftaran
    Route::get('/pendaftaran', [PendaftaranController::class, 'index'])
        ->name('admin.pendaftaran');

    // URL: pendaftaran/admin/pendaftaran/{id}
    Route::get('/pendaftaran/{id}', [PendaftaranController::class, 'show'])
        ->name('admin.pendaftaran.show');

    Route::get(
        '/pendaftaran/{id}/jadwal',
        [SeleksiController::class, 'index']
    )->name('pendaftaran.admin.jadwal.index');

    Route::post(
        '/pendaftaran/{id}/jadwal',
        [SeleksiController::class, 'store']
    )->name('pendaftaran.admin.jadwal.store');
});



Route::middleware(['auth'])->prefix('admin/pendaftaran')->name('pendaftaran.')->group(function () {

    Route::get('/', [PendaftaranController::class, 'index'])
        ->name('index');

    Route::get('/{id}', [PendaftaranController::class, 'show'])
        ->name('show');
});

Route::middleware(['auth'])->group(function () {
    Route::post('/seleksi', [SeleksiController::class, 'store']);
    Route::put('/seleksi/{id}', [SeleksiController::class, 'update']);
    Route::delete('/seleksi/{id}', [SeleksiController::class, 'destroy']);
});
