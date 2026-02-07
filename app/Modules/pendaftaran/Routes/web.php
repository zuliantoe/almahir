<?php

use Illuminate\Support\Facades\Route;
use Modules\Pendaftaran\Controllers\SeleksiController;
use Modules\Pendaftaran\Controllers\PendaftaranController;


Route::get('/pendaftaran', [PendaftaranController::class, 'create'])
    ->name('pendaftaran.create');

Route::post('/pendaftaran', [PendaftaranController::class, 'store'])
    ->name('pendaftaran.store');



Route::middleware(['auth'])->prefix('admin')->group(function () {

    Route::get('/pendaftaran', [PendaftaranController::class, 'index'])
        ->name('pendaftaran.index');

    Route::get('/pendaftaran/{id}', [PendaftaranController::class, 'show'])
        ->name('pendaftaran.show');

    Route::put('/pendaftaran/{id}/status', [PendaftaranController::class, 'updateStatus'])
        ->name('pendaftaran.updateStatus');

});


Route::middleware(['auth'])->group(function () {
    Route::post('/seleksi', [SeleksiController::class, 'store']);
    Route::put('/seleksi/{id}', [SeleksiController::class, 'update']);
    Route::delete('/seleksi/{id}', [SeleksiController::class, 'destroy']);
});
