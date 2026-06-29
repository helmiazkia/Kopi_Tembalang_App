<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Payment;

class PaymentController extends Controller
{

    /*
    |--------------------------------------------------------------------------
    | CREATE PAYMENT
    |--------------------------------------------------------------------------
    */

    public function create(Order $order)
    {

        $payment = Payment::create([
            'order_id' => $order->id,
            'amount' => $order->total_price,
            'status' => 'pending'
        ]);

        return response()->json([
            'message' => 'Payment created',
            'payment' => $payment
        ]);
    }



    /*
    |--------------------------------------------------------------------------
    | PAYMENT CALLBACK
    |--------------------------------------------------------------------------
    */

    public function callback(Request $request)
    {

        $payment = Payment::where(
            'transaction_id',
            $request->transaction_id
        )->first();

        if (!$payment) {
            return response()->json([
                'message' => 'Payment not found'
            ],404);
        }

        if ($request->status == 'settlement') {

            $payment->update([
                'status' => 'paid',
                'paid_at' => now()
            ]);

            $payment->order->update([
                'status' => 'paid'
            ]);
        }

        return response()->json([
            'message' => 'Payment updated'
        ]);
    }



    /*
    |--------------------------------------------------------------------------
    | PAYMENT STATUS
    |--------------------------------------------------------------------------
    */

    public function status(Order $order)
    {
        // 🔥 AUTO-CANCEL EXPIRED PAYMENTS
        Payment::cancelAllExpired();

        return response()->json([
            'order' => $order,
            'payment' => $order->payment
        ]);

    }

    /*
    |--------------------------------------------------------------------------
    | AUTO-CANCEL EXPIRED PAYMENT
    |--------------------------------------------------------------------------
    */

    public function checkAndCancelExpired(Order $order)
    {
        $payment = $order->payment;

        if (!$payment) {
            return response()->json([
                'message' => 'Payment not found'
            ], 404);
        }

        // Jika status masih pending dan waktu sudah habis
        if ($payment->status === 'pending' && $payment->isExpired()) {
            $payment->update([
                'status' => 'expired'
            ]);

            $order->update([
                'status' => 'cancelled'
            ]);

            return response()->json([
                'message' => 'Payment expired, order cancelled',
                'order' => $order,
                'payment' => $payment
            ]);
        }

        return response()->json([
            'message' => 'Payment is still valid',
            'order' => $order,
            'payment' => $payment
        ]);
    }

}