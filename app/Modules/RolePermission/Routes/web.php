<?php

use Illuminate\Support\Facades\Route;
use Modules\RolePermission\Controllers\RolePermissionController;

Route::middleware(['auth', 'role:SUPER_ADMIN'])->group(function () {
    Route::resource('/', RolePermissionController::class)->parameters(['' => 'role']);
});
