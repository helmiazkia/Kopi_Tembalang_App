<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\MenuOptionItemController;
use App\Http\Controllers\Admin\MenuOptionController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\TableController;

use App\Http\Controllers\Cashier\OrderController as CashierOrderController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Cashier\ReceiptController;
use App\Http\Controllers\OrderSimulationController;

Route::get('/', function () {
    return view('welcome');
});


Route::middleware('auth')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | PROFILE
    |--------------------------------------------------------------------------
    */

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');


    /*
    |--------------------------------------------------------------------------
    | ADMIN
    |--------------------------------------------------------------------------
    */

    Route::middleware('role:admin')
        ->prefix('admin')
        ->name('admin.')
        ->group(function () {

            Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

            Route::resource('menus', MenuController::class);
            Route::resource('categories', CategoryController::class);
            Route::resource('menu_option_items', MenuOptionItemController::class);
            Route::resource('menu_options', MenuOptionController::class);
            Route::resource('tables', TableController::class);

            // monitoring order
            Route::resource('orders', OrderController::class)->only(['index', 'show']);

            // download QR meja
            Route::get('tables/{table}/download-qr', [TableController::class, 'downloadQR'])
                ->name('tables.qr.download');
        });


    /*
    |--------------------------------------------------------------------------
    | CASHIER
    |--------------------------------------------------------------------------
    */

    Route::middleware('role:cashier')
        ->prefix('cashier')
        ->name('cashier.')
        ->group(function () {

            // halaman kasir melihat order
            Route::get('/orders', [CashierOrderController::class, 'index'])->name('orders.index');

            Route::get('/orders/create', [CashierOrderController::class, 'create'])->name('orders.create');

            Route::post('/orders', [CashierOrderController::class, 'store'])->name('orders.store');

            Route::post('/orders/{id}/pay-cash', [CashierOrderController::class, 'payCash'])->name('orders.payCash');
        });
});


Route::get('/order', [OrderSimulationController::class, 'index']);
Route::post('/order', [OrderSimulationController::class, 'store']);

require __DIR__ . '/auth.php';
