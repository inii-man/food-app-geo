<?php

use App\Http\Controllers\MerchantController;
use App\Http\Controllers\NearbyMerchantController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Merchants
    Route::resource('merchants', MerchantController::class);
    Route::get('/merchants-map', [MerchantController::class, 'map'])->name('merchants.map');
    Route::get('/merchants-nearby', [MerchantController::class, 'nearby'])->name('merchants.nearby');

    // menu

    // page foodmenu
    Route::get('/foodmenu', [App\Http\Controllers\FoodMenuController::class, 'index'])->name('foodmenu.index');

    // page create foodmenu
    Route::get('/foodmenu/create', [App\Http\Controllers\FoodMenuController::class, 'create'])->name('foodmenu.create');

    // store foodmenu
    Route::post('/foodmenu', [App\Http\Controllers\FoodMenuController::class, 'store'])->name('foodmenu.store');

    // edit foodmenu
    Route::get('/foodmenu/{id}/edit', [App\Http\Controllers\FoodMenuController::class, 'edit'])->name('foodmenu.edit');

    // update foodmenu
    Route::put('/foodmenu/{id}', [App\Http\Controllers\FoodMenuController::class, 'update'])->name('foodmenu.update');

    // delete foodmenu
    Route::delete('/foodmenu/{id}', [App\Http\Controllers\FoodMenuController::class, 'destroy'])->name('foodmenu.destroy');

    // Orders routes
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    // Route::get('/orders/create', [OrderController::class, 'create'])->name('orders.create'); // Deprecated - use shopping cart in merchant page
    Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');

    Route::middleware(['role:merchant'])->group(function () {
        Route::get('/dashboard/merchant', function () {
            return 'Selamat datang Merchant!';
        });

        // Merchant order management
        Route::get('/merchant/orders', [OrderController::class, 'merchantIndex'])->name('merchant.orders.index');
        Route::patch('/merchant/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('merchant.orders.updateStatus');
    });

    Route::middleware(['role:admin'])->group(function () {
        Route::get('/dashboard/admin', function () {
            return 'Selamat datang Admin!';
        });
    });

    Route::middleware(['role:customer'])->group(function () {
        Route::get('/dashboard/customer', function () {
            return 'Selamat datang Customer!';
        });
    });
});

// API Routes for AJAX calls
Route::middleware('auth')->prefix('api')->group(function () {
    Route::get('/merchants/nearby', [NearbyMerchantController::class, 'search']);
    Route::get('/merchants/all', [NearbyMerchantController::class, 'all']);
});

require __DIR__ . '/auth.php';
