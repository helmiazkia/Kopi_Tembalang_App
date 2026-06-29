<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Models\Order;

class ReceiptController extends Controller
{
    public function show(Order $order)
    {
        if (!$order->is_printed) {
            $order->update(['is_printed' => true]);
        }

        $order->load('items.menu', 'items.options.optionItem', 'payment');
        return view('cashier.receipt.show', compact('order'));
    }
}