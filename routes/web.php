<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InventoryCategoryController;
use App\Http\Controllers\MeasurementUnitController;
use App\Http\Controllers\InventoryItemController;

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

        // Inventory Management
        Route::prefix('inventory')->name('inventory.')->group(function () {

            Route::resource(
                'categories',
                InventoryCategoryController::class
            )->parameters([
                'categories' => 'inventory_category',
            ])->only([
                'index',
                'create',
                'store',
                'edit',
                'update',
            ]);
            Route::resource(
                'units',
                MeasurementUnitController::class
            )->parameters([
                'units' => 'measurement_unit',
            ])->only([
                'index',
                'create',
                'store',
                'edit',
                'update',
            ]);

            // Inventory Items
            Route::resource(
                'items',
                InventoryItemController::class
            )->parameters([
                'items' => 'inventory_item',
            ])->only([
                'index',
                'create',
                'store',
                'edit',
                'update',
            ]);

            Route::patch('/items/{inventory_item}/status', [
                InventoryItemController::class,
                'updateStatus',
            ])->name('items.update-status');
        });
    });
});

require __DIR__ . '/auth.php';
