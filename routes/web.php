<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InventoryCategoryController;
use App\Http\Controllers\MeasurementUnitController;
use App\Http\Controllers\InventoryItemController;
use App\Http\Controllers\UnitConversionController;
use App\Http\Controllers\StockMovementController;
use App\Http\Controllers\LowStockController;
use App\Http\Controllers\MenuItemController;
use App\Http\Controllers\SellableItemController;
use App\Http\Controllers\RecipeController;
use App\Http\Controllers\RecipeIngredientController;

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
            // Unit Conversions
            Route::resource('conversions', UnitConversionController::class)
                ->parameters([
                    'conversions' => 'unit_conversion',
                ])
                ->only([
                    'index',
                    'create',
                    'store',
                    'edit',
                    'update',
                ]);
            // Stock Movement History
            Route::get(
                '/movements',
                [StockMovementController::class, 'index']
            )->name('movements.index');
            // Low Stock Monitoring
            Route::get(
                '/low-stock',
                [LowStockController::class, 'index']
            )->name('low-stock.index');
            // Menu Item Management
            Route::resource('menu-items', MenuItemController::class)
                ->parameters([
                    'menu-items' => 'menu_item',
                ])
                ->only([
                    'index',
                    'create',
                    'store',
                    'edit',
                    'update',
                ]);

            Route::patch(
                '/menu-items/{menu_item}/status',
                [MenuItemController::class, 'updateStatus']
            )->name('menu-items.update-status');

            // Café Sellable Product Management
            Route::resource(
                'sellable-items',
                SellableItemController::class
            )
                ->parameters([
                    'sellable-items' => 'sellable_item',
                ])
                ->only([
                    'index',
                    'create',
                    'store',
                    'edit',
                    'update',
                ]);

            Route::patch(
                '/sellable-items/{sellable_item}/status',
                [SellableItemController::class, 'updateStatus']
            )->name('sellable-items.update-status');
            // Recipe Management
            Route::resource('recipes', RecipeController::class)
                ->only([
                    'index',
                    'create',
                    'store',
                    'edit',
                    'update',
                ]);

            Route::patch(
                '/recipes/{recipe}/status',
                [RecipeController::class, 'updateStatus']
            )->name('recipes.update-status');
            // Recipe Ingredient Management
            Route::resource(
                'recipes.ingredients',
                RecipeIngredientController::class
            )->only([
                'index',
                'store',
                'edit',
                'update',
                'destroy',
            ])->scoped();
        });
    });
});

require __DIR__ . '/auth.php';
