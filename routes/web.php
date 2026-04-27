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
use App\Http\Controllers\Customer\OrderController as CustomerOrderController;
use App\Http\Controllers\Customer\PaymentController as CustomerPaymentController;

// CASHIER
use App\Http\Controllers\Cashier\OrderController as CashierOrderController;
use App\Http\Controllers\Cashier\PaymentController as CashierPaymentController;
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

            // (OPSIONAL - kalau masih dipakai)
            Route::get('payment/{snapToken}', [CashierPaymentController::class, 'show'])
                ->name('payment.show');
            Route::post('orders/{order}/cancel', [CashierOrderController::class, 'cancel'])
                ->name('orders.cancel');
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
Route::get('/menu/{table}', [CustomerOrderController::class, 'index']);
Route::get('/menu/{table}/{menu}', [CustomerOrderController::class, 'show']);
Route::post('/menu/{table}', [CustomerOrderController::class, 'store']);

// CART
Route::get('/cart', [CartController::class, 'index']);

// PAYMENT FLOW
Route::get('/order/{order}/qr', fn($order) => view('customer.qr', compact('order')))
    ->name('customer.qr');

Route::get('/order/{order}/success', fn($order) => view('customer.success', compact('order')))
    ->name('customer.success');

// CHECKOUT
Route::get('/checkout', [CheckoutController::class, 'index']);
Route::post('/checkout', [CheckoutController::class, 'store']);

// CUSTOMER PAYMENT
Route::get('/payment/{order}', [CustomerPaymentController::class, 'index'])
    ->name('customer.payment');


Route::post('/payment/{order}', [CustomerPaymentController::class, 'process']);


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
