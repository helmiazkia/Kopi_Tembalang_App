<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Models\Order;

class ReceiptController extends Controller
{
    public function show($id)
    {
        $order = Order::with([
            'table',
            'items.menu',
            'items.options.optionItem.menuOption',
            'payment'
        ])->findOrFail($id);

        return view('cashier.receipt.index', compact('order'));
    }
}