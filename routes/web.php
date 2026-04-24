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
use App\Http\Controllers\Customer\CartController;
use App\Http\Controllers\Customer\CheckoutController;
use App\Http\Controllers\Customer\OrderController as CustomerOrderController;
use App\Http\Controllers\Cashier\OrderController as CashierOrderController;
use App\Http\Controllers\Cashier\PaymentController;
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
            Route::resource('orders', CashierOrderController::class)->only(['index', 'create', 'store']);



            Route::get('payments', [PaymentController::class, 'index'])->name('payments.index');
            Route::get('payments/{order}', [PaymentController::class, 'show'])->name('payments.show');
            Route::post('payments/{order}/pay', [PaymentController::class, 'pay'])->name('payments.pay');
            
        });
});

Route::get('/menu/{table}', [CustomerOrderController::class, 'index']);
Route::get('/menu/{table}/{menu}', [CustomerOrderController::class, 'show']);
Route::post('/menu/{table}', [CustomerOrderController::class, 'store']);
Route::get('/cart', [CartController::class, 'index']);

Route::get('/order/{order}/qr', fn($order) => view('customer.qr', compact('order')))->name('customer.qr');
Route::get('/order/{order}/success', fn($order) => view('customer.success', compact('order')))->name('customer.success');

Route::get('/checkout', [\App\Http\Controllers\Customer\CheckoutController::class, 'index']);
Route::post('/checkout', [\App\Http\Controllers\Customer\CheckoutController::class, 'store']);

Route::get('/payment/{order}', [\App\Http\Controllers\Customer\PaymentController::class, 'index'])->name('customer.payment');
Route::post('/payment/{order}', [\App\Http\Controllers\Customer\PaymentController::class, 'process']);

require __DIR__ . '/auth.php';
