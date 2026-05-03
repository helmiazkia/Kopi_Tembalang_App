<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Midtrans\{Config, Notification};
use Illuminate\Support\Facades\Log;

class MidtransController extends Controller
{
    public function callback(Request $request)
    {
        Config::$serverKey = config('midtrans.serverKey');
        Config::$isProduction = config('midtrans.isProduction', false);

        try {
            $notif = new Notification();

            $transactionStatus = $notif->transaction_status;
            $paymentType = $notif->payment_type;
            $orderIdRaw = $notif->order_id;
            $transactionIdMidtrans = $notif->transaction_id;

            $orderId = $this->extractOrderId($orderIdRaw);
            $order = Order::with('payment')->find($orderId);

            if (!$order || !$order->payment) {
                return response()->json(['message' => 'Order/Payment not found'], 404);
            }

            $payment = $order->payment;
            $channel = $this->resolveChannel($notif);

            // Update info transaksi dari Midtrans
            $payment->update([
                'channel' => $channel,
                'transaction_id' => $transactionIdMidtrans
            ]);

            // Handle Status
            if (in_array($transactionStatus, ['settlement', 'capture'])) {
                $payment->update(['status' => 'paid', 'paid_at' => now()]);
                $order->update([
                    'status' => 'paid'
                ]);
                Log::info("Payment Success: Order #{$orderId}");
            } elseif ($transactionStatus == 'pending') {
                $payment->update(['status' => 'pending']);
            } elseif ($transactionStatus == 'expire') {
                $payment->update(['status' => 'expired']);
                $order->update(['status' => 'cancelled']);
            } elseif (in_array($transactionStatus, ['cancel', 'deny', 'failure'])) {
                $payment->update(['status' => 'failed']);
                $order->update(['status' => 'cancelled']);
            }

            return response()->json(['message' => 'OK']);
        } catch (\Exception $e) {
            Log::error('Midtrans Callback Error: ' . $e->getMessage());
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    private function extractOrderId($transactionId)
    {
        $parts = explode('-', $transactionId);
        return end($parts);
    }

    private function resolveChannel($notif)
    {
        if ($notif->payment_type === 'qris') return $notif->acquirer ?? 'qris';
        if ($notif->payment_type === 'bank_transfer') return $notif->va_numbers[0]->bank ?? 'va';
        if ($notif->payment_type === 'echannel') return 'mandiri';
        return $notif->payment_type;
    }

    public function checkPaymentStatus($orderId)
    {
        $order = Order::with('payment')->find($orderId);
        if (!$order) return response()->json(['status' => 'error'], 404);

        return response()->json([
            'status' => $order->payment->status ?? 'unknown',
            'order_id' => $order->id,
            'paid_at' => $order->payment ? $order->payment->paid_at : null
        ]);
    }
}
