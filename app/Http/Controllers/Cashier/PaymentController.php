<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    // 🔥 HALAMAN PILIH PEMBAYARAN
    public function show(Order $order)
    {
        return view('cashier.payment.show', compact('order'));
    }

    // 🔥 PROSES BAYAR
    public function pay(Request $request, Order $order)
    {
        $request->validate([
            'method' => 'required|in:cash,qris'
        ]);

        // simpan payment
        Payment::create([
            'order_id' => $order->id,
            'method' => $request->method,
            'amount' => $order->total_price,
            'status' => 'paid',
            'paid_at' => now()
        ]);

        // update status order
        $order->update([
            'status' => 'paid'
        ]);

        return redirect()->route('cashier.orders.index')
            ->with('success', 'Pembayaran berhasil');
    }
}