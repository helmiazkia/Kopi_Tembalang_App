<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;

// ADMIN
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\MenuOptionItemController;
use App\Http\Controllers\Admin\MenuOptionController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\TableController;


// CUSTOMER
use App\Http\Controllers\Customer\CartController;
use App\Http\Controllers\Customer\CheckoutController;
use App\Http\Controllers\Customer\OrderController;


// CASHIER
use App\Http\Controllers\Cashier\OrderController as CashierOrderController;
use App\Http\Controllers\Cashier\OrderListController;
use App\Http\Controllers\Cashier\ReceiptController;


// MIDTRANS
use App\Http\Controllers\MidtransController;

// ================= HOME =================
Route::get('/', fn() => view('welcome'));


// ================= AUTH =================
Route::middleware('auth')->group(function () {

    // ================= PROFILE =================
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');


    // ================= ADMIN =================
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

            Route::resource('orders', AdminOrderController::class)->only(['index', 'show']);

            Route::get('tables/{table}/download-qr', [TableController::class, 'downloadQR'])
                ->name('tables.qr.download');
        });


    // ================= CASHIER =================
    Route::middleware('role:cashier')
        ->prefix('cashier')
        ->name('cashier.')
        ->group(function () {

            // ORDER
            Route::resource('orders', CashierOrderController::class)
                ->only(['index', 'store']);

            // RECEIPT
            Route::get('receipt/{order}', [ReceiptController::class, 'show'])
                ->name('receipt.show');
            Route::get('orderList', [OrderListController::class, 'index'])->name('orderList.index');

            Route::post('orderList/scan', [OrderListController::class, 'scan'])
                ->name('orderList.scan');
            Route::get('orderList/{order}/snap-token', [OrderListController::class, 'getSnapToken'])
                ->name('orderList.snap-token');
            Route::get('/api/check-unprinted', [OrderListController::class, 'checkUnprinted'])
                ->name('api.check.unprinted');

            Route::post('/api/mark-as-printed/{order}', [OrderListController::class, 'markAsPrinted'])
                ->name('api.mark.printed');
            Route::get('/orderList/snap/{order}', [OrderListController::class, 'getSnapToken'])->name('orderList.snap');
            Route::get('/orderList/pay/{order}', [OrderListController::class, 'pay'])->name('orderList.pay');
        });

    // 🔥 API ROUTES (UNTUK CHECK PAYMENT STATUS)
    Route::prefix('api')->group(function () {
        Route::get('check-payment/{orderId}', [MidtransController::class, 'checkPaymentStatus'])
            ->name('api.check.payment');
    });

    // 🔥 TEST CALLBACK (UNTUK DEVELOPMENT)
    Route::get('test-callback/{orderId}', [MidtransController::class, 'testCallback'])
        ->name('test.callback');
});


// ================= CUSTOMER =================

// MENU
// routes/web.php
// Route Menu
Route::get('/menu/{table}', [OrderController::class, 'index'])->name('customer.menu');
Route::get('/menu/{table}/{menu}', [OrderController::class, 'show'])->name('customer.menu.show');

// Route Keranjang (INI YANG KURANG)
Route::get('/cart', [CartController::class, 'index'])->name('customer.cart.index');
Route::post('/cart/sync', [CartController::class, 'sync'])->name('customer.cart.sync');

// Route Checkout & Payment
Route::get('/checkout', [CheckoutController::class, 'index'])->name('customer.checkout.index');
Route::post('/checkout', [CheckoutController::class, 'store'])->name('customer.checkout.store');
// routes/web.php
Route::get('/payment/process/{order}', [CheckoutController::class, 'process'])->name('customer.payment.process');
// PAYMENT FLOw 
Route::get('/payment/success/{order}', [CheckoutController::class, 'success'])->name('customer.payment.success');


// ================= 🔥 API / AJAX =================

// 🔥 CEK STATUS PAYMENT (WAJIB BUAT QRIS REALTIME)
Route::get('/api/check-payment/{order}', function ($id) {

    $order = \App\Models\Order::find($id);

    if (!$order) {
        return response()->json(['status' => 'not_found']);
    }

    return response()->json([
        'status' => $order->status,
        'order_id' => $order->id
    ]);
});


// ================= 🔥 MIDTRANS CALLBACK =================
// ❗ HARUS DI LUAR AUTH
Route::post('/midtrans/callback', [MidtransController::class, 'callback']);


// ================= AUTH ROUTES =================
require __DIR__ . '/auth.php';
