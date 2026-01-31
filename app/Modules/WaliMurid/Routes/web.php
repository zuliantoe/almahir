<?php

use Illuminate\Support\Facades\Route;
use Modules\WaliMurid\Controllers\WaliMuridController;

Route::middleware('auth')->group(function () {
    // Resource routes - names will be auto-prefixed by ModuleServiceProvider
    // Final routes: walimurid.index, walimurid.create, etc.
    Route::resource('/', WaliMuridController::class)->parameters(['' => 'walimurid']);
});
