<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Midtrans\Snap;
use Midtrans\Config;

class OrderListController extends Controller
{
    public function index()
    {
        // Mengambil order yang masih pending beserta data payment-nya
        $pendingOrders = Order::with(['table', 'payment'])
            ->where('status', 'pending')
            ->whereHas('payment', function ($q) {
                $q->where(function ($query) {
                    $query->whereNull('expired_at')
                        ->orWhere('expired_at', '>', now());
                });
            })
            ->latest()
            ->get();

        $paidOrders = Order::where('status', 'paid')
            ->latest()
            ->limit(10)
            ->get();

        return view('cashier.orderList.index', compact('pendingOrders', 'paidOrders'));
    }

    /**
     * 🔥 HANYA AMBIL TOKEN YANG SUDAH ADA
     */
    public function getSnapToken(Order $order)
    {
        // Ambil payment yang relasinya sudah dibuat di OrderController@store
        $payment = $order->payment;

        // Validasi apakah payment ada dan punya token
        if (!$payment || !$payment->snap_token) {
            return response()->json([
                'error' => 'Token pembayaran tidak ditemukan. Silakan buat ulang pesanan di Kasir.'
            ], 404);
        }

        // Cek apakah token sudah expired di database kita
        if ($payment->expired_at && now()->gt($payment->expired_at)) {
            return response()->json([
                'error' => 'Waktu pembayaran (10 menit) telah habis. Silakan buat pesanan baru.'
            ], 410);
        }

        // Kirimkan token yang SUDAH ADA di database
        return response()->json([
            'snap_token' => $payment->snap_token,
            'order_id' => $order->id
        ]);
    }

    /**
     * 🔥 PROSES CASH (Sama seperti sebelumnya)
     */
    public function pay(Order $order)
    {
        return DB::transaction(function () use ($order) {
            $payment = $order->payment;

            if ($payment) {
                $payment->update([
                    'method' => 'cash',
                    'status' => 'paid',
                    'paid_at' => now()
                ]);
            }

            $order->update(['status' => 'paid']);

            return redirect()->route('cashier.receipt.show', $order->id)
                ->with('success', 'Pembayaran tunai berhasil.');
        });
    }

    /**
     * 🔥 SCAN UNTUK BAYAR CASH
     */
    public function scan(Request $request)
    {
        $request->validate(['code' => 'required']);
        $order = Order::find($request->code);

        if (!$order) return back()->withErrors(['error' => 'Order tidak ditemukan']);
        if ($order->status == 'paid') return redirect()->route('cashier.receipt.show', $order->id);

        // Jalankan fungsi private dan dapatkan hasilnya (Redirect)
        return $this->processCashPayment($order);
    }
    public function checkUnprinted()
    {
        $order = Order::where('status', 'paid')
            ->where('is_printed', false)
            ->first();

        return response()->json([
            'has_new' => $order ? true : false,
            'order_id' => $order ? $order->id : null
        ]);
    }

    public function markAsPrinted(Order $order)
    {
        $order->update(['is_printed' => true]);
        return response()->json(['success' => true]);
    }

    private function processCashPayment(Order $order)
    {
        try {
            DB::transaction(function () use ($order) {
                $payment = $order->payment;

                if ($payment) {
                    $payment->update([
                        'method' => 'cash',
                        'status' => 'paid',
                        'paid_at' => now()
                    ]);
                }

                $order->update(['status' => 'paid']);
            });

            return redirect()->route('cashier.receipt.show', $order->id)->with('success', 'Pembayaran tunai berhasil.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal memproses pembayaran: ' . $e->getMessage()]);
        }
    }
}
