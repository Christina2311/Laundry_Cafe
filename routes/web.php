<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware([
    'auth',
    'active',
    'role:Owner,Cashier,Staff',
])->group(function () {

    // Dashboard
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Profile Management
    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    // Owner-only modules
    Route::middleware('role:Owner')->group(function () {

        // User Management
        Route::resource('users', UserController::class)
            ->only(['index', 'create', 'store', 'edit', 'update']);

        Route::patch('/users/{user}/status', [
            UserController::class,
            'updateStatus'
        ])->name('users.update-status');
    });
});

require __DIR__ . '/auth.php';
