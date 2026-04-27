<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Models\Payment;

class PaymentController extends Controller
{
    public function show($snapToken)
    {
        $payment = Payment::where('snap_token', $snapToken)->first();

        if (!$payment) {
            abort(404, 'Payment tidak ditemukan');
        }

        $order = $payment->order;

        return view('cashier.payment.qris', [
            'snapToken' => $payment->snap_token,
            'order' => $order,
            'payment' => $payment
        ]);
    }
}