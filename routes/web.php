<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DevController;
use Modules\Auth\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\DashboardController;

// Dashboard / Home (requires auth)
Route::middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
});

/*
|--------------------------------------------------------------------------
| Authentication Routes (NO PREFIX)
|--------------------------------------------------------------------------
*/

// Guest routes (login)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// Authenticated routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Notifications
    Route::get('/notifications/read-all', function() {
        auth()->user()->unreadNotifications->markAsRead();
        return redirect()->back()->with('success', 'Semua notifikasi telah ditandai dibaca.');
    })->name('notifications.readAll');

    Route::get('/notifications/{id}/read', function($id) {
        $notification = auth()->user()->notifications()->findOrFail($id);
        $notification->markAsRead();
        return redirect($notification->data['url'] ?? url('/'));
    })->name('notifications.read');

    // User Profile
    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/avatar', [\App\Http\Controllers\ProfileController::class, 'updateAvatar'])->name('profile.update-avatar');
    Route::put('/password', [\App\Http\Controllers\ProfileController::class, 'updatePassword'])->name('password.update');
});

/*
|--------------------------------------------------------------------------
| User Manager Routes (SUPER_ADMIN only, NO PREFIX)
|--------------------------------------------------------------------------
*/
use Modules\UserManager\Controllers\UserManagerController;

Route::middleware(['auth', 'role:SUPER_ADMIN'])->group(function () {
    // Resource routes for user CRUD
    Route::resource('users', UserManagerController::class);
    
    // Custom route for toggling user status
    Route::post('users/{id}/toggle-status', [UserManagerController::class, 'toggleStatus'])
        ->name('users.toggle-status');
    
    // AJAX route for fetching linkable data
    Route::get('users/api/linkable-data', [UserManagerController::class, 'getLinkableData'])
        ->name('users.linkable-data');
});

/*
|--------------------------------------------------------------------------
| Development Routes (Debug Mode Only)
|--------------------------------------------------------------------------
*/
if (config('app.debug') && class_exists(\App\Http\Controllers\DevController::class)) {
    Route::get('/dev/ui-guide', [\App\Http\Controllers\DevController::class, 'uiGuide'])->name('dev.ui-guide');
}

