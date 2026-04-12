<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\MenuOptionItemController;
use App\Http\Controllers\Admin\MenuOptionController;
use App\Http\Controllers\Admin\TableController;

Route::get('/', function () {
    return view('welcome');
});



Route::middleware('auth')->group(function () {

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

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

            Route::get('tables/{table}/download-qr', [TableController::class, 'downloadQR'])
                ->name('tables.qr.download');
        });
});




Route::middleware(['auth', 'role:cashier'])->group(function () {
    Route::get('/cashier', function () {
        return "Kasir Page";
    });
});

require __DIR__ . '/auth.php';
