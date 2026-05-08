<?php

use Illuminate\Support\Facades\Route;
use Modules\Perizinan\Controllers\PerizinanController;

Route::middleware(['auth'])->group(function () {
    Route::get('/', [PerizinanController::class, 'index'])->name('index');
    Route::get('/create', [PerizinanController::class, 'create'])->name('create');
    Route::post('/', [PerizinanController::class, 'store'])->name('store');
    Route::get('/{id}', [PerizinanController::class, 'show'])->name('show');
    Route::post('/{id}/status', [PerizinanController::class, 'updateStatus'])->name('update-status');
});
