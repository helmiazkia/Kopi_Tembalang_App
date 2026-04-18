<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{

    public function store(Request $request)
    {

        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'method' => 'required'
        ]);

        $order = Order::findOrFail($validated['order_id']);

        Payment::create([
            'order_id' => $order->id,
            'method' => $validated['method'],
            'amount' => $order->total_price,
            'status' => 'paid',
            'paid_at' => now()
        ]);

        $order->update([
            'status' => 'paid',
            'cashier_id' => auth()->id()
        ]);

        return back()->with('success','Pembayaran berhasil');
    }

}