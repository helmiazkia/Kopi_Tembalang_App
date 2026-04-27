<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Midtrans\Config;
use Midtrans\Notification;

class MidtransController extends Controller
{
    public function callback(Request $request)
    {
        Config::$serverKey = config('midtrans.serverKey');
        Config::$isProduction = config('midtrans.isProduction');

        try {
            $notif = new Notification();

            $transactionStatus = $notif->transaction_status;
            $paymentType = $notif->payment_type;
            $fraudStatus = $notif->fraud_status ?? null;
            $transactionIdMidtrans = $notif->transaction_id ?? null;

            $orderId = $this->extractOrderId($notif->order_id);

            // 🔥 ambil channel
            $channel = $this->resolveChannel($notif);
        } catch (\Exception $e) {

            $data = $request->all();

            if (empty($data)) {
                return response()->json(['message' => 'Invalid notification data'], 400);
            }

            $transactionStatus = $data['transaction_status'] ?? null;
            $paymentType = $data['payment_type'] ?? null;
            $fraudStatus = $data['fraud_status'] ?? null;
            $transactionIdMidtrans = $data['transaction_id'] ?? null;

            $orderId = isset($data['order_id'])
                ? $this->extractOrderId($data['order_id'])
                : null;

            $channel = $paymentType; // fallback

            if (!$transactionStatus || !$orderId) {
                return response()->json(['message' => 'Missing required data'], 400);
            }
        }

        $order = Order::find($orderId);
        if (!$order) return response()->json(['message' => 'Order not found']);

        $payment = $order->payment;
        if (!$payment) return response()->json(['message' => 'Payment not found']);

        // ==============================
        // 🔥 UPDATE CHANNEL + TRX ID
        // ==============================
        $payment->update([
            'channel' => $channel,
            'transaction_id' => $transactionIdMidtrans ?? $payment->transaction_id
        ]);

        // ==============================
        // 🔥 LOG
        // ==============================
        \Log::info('Midtrans Callback', [
            'order_id' => $orderId,
            'status' => $transactionStatus,
            'type' => $paymentType,
            'channel' => $channel,
            'fraud' => $fraudStatus
        ]);

        // ==============================
        // 🔥 HANDLE STATUS
        // ==============================

        // ✅ SUCCESS
        if (in_array($transactionStatus, ['settlement', 'capture'])) {

            $payment->update([
                'status' => 'paid',
                'paid_at' => now()
            ]);

            $order->update(['status' => 'paid']);
        }

        // ⏳ PENDING
        elseif ($transactionStatus == 'pending') {

            $payment->update([
                'status' => 'pending'
            ]);
        }

        // ⌛ EXPIRED
        elseif ($transactionStatus == 'expire') {

            $payment->update([
                'status' => 'expired'
            ]);

            $order->update([
                'status' => 'cancelled'
            ]);
        }

        // ❌ FAILED
        elseif (in_array($transactionStatus, ['cancel', 'deny', 'failure'])) {

            $payment->update([
                'status' => 'failed'
            ]);

            $order->update([
                'status' => 'cancelled'
            ]);
        }

        return response()->json(['message' => 'OK']);
    }
    // 🔥 EXTRACT ORDER ID FROM TRANSACTION ID
    private function extractOrderId($transactionId)
    {
        // Format: ORDER-timestamp-orderId
        // Extract the last part after the last dash
        $parts = explode('-', $transactionId);
        return end($parts);
    }

    // 🔥 AMBIL CHANNEL DETAIL
    private function resolveChannel($notif)
    {
        // QRIS (lihat acquirer)
        if ($notif->payment_type === 'qris') {
            return $notif->acquirer ?? 'qris';
        }

        // GOPAY
        if ($notif->payment_type === 'gopay') {
            return 'gopay';
        }

        // SHOPEEPAY
        if ($notif->payment_type === 'shopeepay') {
            return 'shopeepay';
        }

        // VA
        if ($notif->payment_type === 'bank_transfer') {
            return $notif->va_numbers[0]->bank ?? 'va';
        }

        // MANDIRI
        if ($notif->payment_type === 'echannel') {
            return 'mandiri';
        }

        return $notif->payment_type;
    }

    // 🔥 CHECK PAYMENT STATUS (UNTUK POLLING)
    public function checkPaymentStatus($orderId)
    {
        try {
            $order = Order::with('payment')->findOrFail($orderId);

            return response()->json([
                'status' => $order->payment->status ?? 'unknown',
                'order_id' => $order->id,
                'paid_at' => $order->payment->paid_at ?? null
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // 🔥 TEST CALLBACK (UNTUK DEVELOPMENT)
    public function testCallback($orderId)
    {
        try {
            $order = Order::findOrFail($orderId);
            $payment = $order->payment;

            if (!$payment) {
                \Log::error("Test callback: Payment not found for order {$orderId}");
                return response()->json(['success' => false, 'message' => 'Payment not found']);
            }

            // Mark as paid
            $payment->update([
                'status' => 'paid',
                'paid_at' => now(),
                'channel' => 'qris_test'
            ]);

            $order->update(['status' => 'paid']);

            \Log::info("Test callback: Order {$orderId} marked as PAID via test");

            return response()->json(['success' => true, 'message' => 'Payment marked as completed!']);
        } catch (\Exception $e) {
            \Log::error("Test callback error for order {$orderId}: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }
}
