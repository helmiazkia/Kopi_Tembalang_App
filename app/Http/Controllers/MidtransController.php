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
        Config::$isProduction = config('midtrans.isProduction', true);

        try {
            $data = $request->all();
            
            // 🔥 SOLUSI TIMEOUT: Langsung beri respon 200 jika ini adalah notifikasi tes dashboard
            if (isset($data['order_id']) && str_contains($data['order_id'], 'payment_notif_test')) {
                Log::info('Midtrans Dashboard Test Received');
                return response()->json(['message' => 'Test Notification Received'], 200);
            }

            $notif = new Notification();
            $transactionStatus = $notif->transaction_status;
            $orderIdRaw = $notif->order_id;
            $transactionIdMidtrans = $notif->transaction_id;

            $orderId = $this->extractOrderId($orderIdRaw);
            
            // 🔥 Gunakan find() dengan aman
            $order = Order::with('payment')->find($orderId);

            if (!$order) {
                Log::error("Order ID $orderId tidak ditemukan.");
                return response()->json(['message' => 'Order not found'], 404);
            }

            $payment = $order->payment;
            $channel = $this->resolveChannel($notif);

            if ($payment) {
                $payment->update([
                    'channel' => $channel,
                    'transaction_id' => $transactionIdMidtrans
                ]);
            }

            // Handle Status Pembayaran
            if (in_array($transactionStatus, ['settlement', 'capture'])) {
                if ($payment) {
                    $payment->update(['status' => 'paid', 'paid_at' => now()]);
                }
                
                $order->update([
                    'status' => 'paid',
                    'is_printed' => false // 🔥 Set false agar polling kasir mendeteksi untuk Auto-Print
                ]);
                
                Log::info("Payment Success: Order #{$orderId}");
            } elseif ($transactionStatus == 'pending') {
                if ($payment) $payment->update(['status' => 'pending']);
            } elseif (in_array($transactionStatus, ['expire', 'cancel', 'deny', 'failure'])) {
                if ($payment) $payment->update(['status' => 'failed']);
                $order->update(['status' => 'cancelled']);
            }

            return response()->json(['message' => 'OK'], 200);

        } catch (\Exception $e) {
            Log::error('Midtrans Callback Error: ' . $e->getMessage());
            // Tetap kembalikan 500 agar Midtrans mencoba mengirim ulang jika memang ada error sistem
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    private function extractOrderId($transactionId)
    {
        // 🔥 Penanganan jika ID mengandung tanda hubung (-) seperti ORDER-TIMESTAMP-ID
        if (str_contains($transactionId, '-')) {
            $parts = explode('-', $transactionId);
            $lastPart = end($parts);
            return is_numeric($lastPart) ? $lastPart : $transactionId;
        }
        return $transactionId;
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