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

        return response()->json([
            'order' => $order,
            'payment' => $order->payment
        ]);

    }

}