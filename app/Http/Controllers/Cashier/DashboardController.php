<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Payment;

class DashboardController extends Controller
{
    public function index()
    {
        $totalOrders = Order::count();

        $totalIncome = Payment::join('orders', 'payments.order_id', '=', 'orders.id')
            ->where('payments.status', 'paid')
            ->sum('orders.total_price');

        $totalCash = Payment::join('orders', 'payments.order_id', '=', 'orders.id')
            ->where('payments.method', 'cash')
            ->where('payments.status', 'paid')
            ->sum('orders.total_price');

        $totalQris = Payment::join('orders', 'payments.order_id', '=', 'orders.id')
            ->where('payments.method', 'qris')
            ->where('payments.status', 'paid')
            ->sum('orders.total_price');

        return view('cashier.dashboard', compact(
            'totalOrders',
            'totalIncome',
            'totalCash',
            'totalQris'
        ));
    }
}

