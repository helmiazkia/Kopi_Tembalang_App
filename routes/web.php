<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ProfileController;

// ADMIN
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\MenuOptionItemController;
use App\Http\Controllers\Admin\MenuOptionController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\TableController;

// KITCHEN
use App\Http\Controllers\Kitchen\KitchenController;

// CASHIER
use App\Http\Controllers\Cashier\OrderController as CashierOrderController;
use App\Http\Controllers\Cashier\DashboardController as CashierDashboardController;
use App\Http\Controllers\Cashier\OrderListController;
use App\Http\Controllers\Cashier\MenuController as CashierMenuController;
use App\Http\Controllers\Cashier\ReceiptController;

// CUSTOMER
use App\Http\Controllers\Customer\CartController;
use App\Http\Controllers\Customer\CheckoutController;
use App\Http\Controllers\Customer\OrderController as CustomerOrderController;

// MIDTRANS
use App\Http\Controllers\MidtransController;

// ================= HOME =================
// ================= HOME =================
Route::get('/', function () {
    if (!Auth::check()) {
        return redirect()->route('login');
    }

    return match (Auth::user()->role) {
        'admin'    => redirect()->route('admin.dashboard'),
        'cashier'  => redirect()->route('cashier.dashboard'),
        'kitchen'  => redirect()->route('kitchen.index'),
        default    => redirect()->route('login'),
    };
});

// ================= AUTH REQUIRED =================
Route::middleware('auth')->group(function () {

    // --- PROFILE ---
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ================= ADMIN (Role: admin) =================
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        // ✅ Tambahkan ini
        Route::get('/dashboard/export-top-menus', [DashboardController::class, 'exportTopMenus'])
            ->name('dashboard.exportTopMenus');

        Route::resource('users', UserController::class);
        Route::resource('menus', MenuController::class);
        Route::resource('categories', CategoryController::class);
        Route::resource('menu_option_items', MenuOptionItemController::class);
        Route::resource('menu_options', MenuOptionController::class);
        Route::resource('tables', TableController::class);

        Route::get('tables/{table}/download-qr', [TableController::class, 'downloadQR'])->name('tables.qr.download');

        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/', [ReportController::class, 'index'])->name('index');
            Route::get('/export', [ReportController::class, 'export'])->name('export');
            Route::get('/export-pdf', [ReportController::class, 'exportPdf'])->name('export-pdf');

        });
    });


    // ================= KITCHEN (Role: kitchen, admin) =================
    // Admin diberikan akses ke kitchen untuk monitoring
    Route::middleware('role:kitchen,admin')->prefix('kitchen')->name('kitchen.')->group(function () {
        Route::get('/', [KitchenController::class, 'index'])->name('index');
        Route::post('/ready/{order}', [KitchenController::class, 'markAsDone'])->name('ready');
    });

    // ================= CASHIER (Role: cashier, admin) =================
    Route::middleware('role:cashier,admin')->prefix('cashier')->name('cashier.')->group(function () {
        Route::get('/', [CashierDashboardController::class, 'index'])->name('dashboard');
        Route::resource('orders', CashierOrderController::class)->only(['index', 'store']);

        // Order List & Scan
        Route::get('orderList', [OrderListController::class, 'index'])->name('orderList.index');
        Route::post('orderList/scan', [OrderListController::class, 'scan'])->name('orderList.scan');

        // Payment & Snap
        Route::get('orderList/snap/{order}', [OrderListController::class, 'getSnapToken'])->name('orderList.snap');
        Route::get('orderList/pay/{order}', [OrderListController::class, 'pay'])->name('orderList.pay');

        // Menu (toggle ketersediaan saja)
        Route::get('menus', [CashierMenuController::class, 'index'])->name('menus.index');
        Route::patch('menus/{menu}/toggle', [CashierMenuController::class, 'toggleAvailability'])->name('menus.toggle');

        // Receipt & Printing
        Route::get('receipt/{order}', [ReceiptController::class, 'show'])->name('receipt.show');
        Route::get('api/check-unprinted', [OrderListController::class, 'checkUnprinted'])->name('api.check.unprinted');
        Route::post('api/mark-as-printed/{order}', [OrderListController::class, 'markAsPrinted'])->name('api.mark.printed');
    });

    // 🔥 API INTERNAL CHECK
    Route::get('/api/check-payment/{order}', function ($id) {
        $order = \App\Models\Order::find($id);
        return response()->json([
            'status' => $order ? $order->status : 'not_found',
            'order_id' => $id
        ]);
    })->name('api.check.payment');

// 🔥 PAYMENT - AUTO CANCEL EXPIRED
Route::post('/api/payment/check-expired/{order}', [\App\Http\Controllers\PaymentController::class, 'checkAndCancelExpired'])
    ->name('api.payment.checkExpired');
    Route::get('/menu/{table}/{menu}', [CustomerOrderController::class, 'show'])->name('menu.show');

    // Cart
    Route::get('/cart/{table}', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/sync', [CartController::class, 'sync'])->name('cart.sync');

    // Checkout & Payment
    Route::get('/checkout/{table}', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout/{table}', [CheckoutController::class, 'store'])->name('checkout.store');
    Route::get('/payment/cash/{order}', [CheckoutController::class, 'cash'])->name('payment.cash');

    Route::get('/payment/process/{order}', [CheckoutController::class, 'process'])->name('payment.process');
    Route::get('/payment/success/{order}', [CheckoutController::class, 'success'])->name('payment.success');
    Route::get('/payment/check/{order}', [CheckoutController::class, 'checkStatus'])->name('payment.check');
});


// ================= MIDTRANS CALLBACK (Public) =================
Route::post('/midtrans/callback', [MidtransController::class, 'callback'])->name('midtrans.callback');

require __DIR__ . '/auth.php';
