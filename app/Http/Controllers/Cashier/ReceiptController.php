<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Models\Order;

class ReceiptController extends Controller
{
    public function show(Order $order)
    {
        // Setiap kali halaman struk dibuka, pastikan database tahu ini sudah diprint
        if (!$order->is_printed) {
            $order->update(['is_printed' => true]);
        }

        $order->load('items.menu', 'payment');
        return view('cashier.receipt.show', compact('order'));
    }
}
