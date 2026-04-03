<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;

class DashboardController extends Controller
{
    public function index()
    {
        $totalOrders = Order::count();

        $totalIncome = Payment::join('orders', 'payments.order_id', '=', 'orders.id')
            ->where('payments.payment_status', 'paid')
            ->sum('orders.total_price');

        $totalCash = Payment::join('orders', 'payments.order_id', '=', 'orders.id')
            ->where('payments.payment_method', 'cash')
            ->where('payments.payment_status', 'paid')
            ->sum('orders.total_price');

        $totalQris = Payment::join('orders', 'payments.order_id', '=', 'orders.id')
            ->where('payments.payment_method', 'qris')
            ->where('payments.payment_status', 'paid')
            ->sum('orders.total_price');

        return view('admin.dashboard', compact(
            'totalOrders',
            'totalIncome',
            'totalCash',
            'totalQris'
        ));
    }
}
