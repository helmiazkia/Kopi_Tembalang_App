<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Models\Order;

class ReceiptController extends Controller
{
    public function show(Order $order)
    {
        // 🔥 PASTIKAN ORDER SUDAH DIBAYAR
        if ($order->status !== 'paid') {
            abort(403, 'Order belum dibayar');
        }

        $order->load([
            'items.menu',
            'items.options.optionItem'
        ]);

        return view('cashier.receipt.show', compact('order'));
    }
}