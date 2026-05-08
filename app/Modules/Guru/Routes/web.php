<?php

use Illuminate\Support\Facades\Route;
use Modules\Guru\Controllers\GuruController;
use Modules\Guru\Controllers\GuruDashboardController;

Route::middleware(['auth', 'role:GURU'])->group(function () {
    Route::get('/dashboard', [GuruDashboardController::class, 'index'])->name('dashboard');
});

Route::middleware('auth')->group(function () {
    // Resource routes - names will be auto-prefixed by ModuleServiceProvider
    // Final routes: guru.index, guru.create, guru.store, etc.
    Route::resource('/', GuruController::class)->parameters(['' => 'guru']);
    
    Route::get('/kalender-akademik', [\Modules\Akademik\Controllers\KalenderAkademikController::class, 'index'])->name('kalender-akademik');
});
