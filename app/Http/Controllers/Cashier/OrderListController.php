<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class OrderListController extends Controller
{
    public function index()
    {
        // 1. Ambil order PENDING (Belum Bayar) yang belum kadaluarsa
        $pendingOrders = Order::with(['table', 'payment'])
            ->where('status', 'pending')
            ->whereHas('payment', function ($q) {
                $q->where(function ($query) {
                    $query->whereNull('expired_at')
                        ->orWhere('expired_at', '>', Carbon::now());
                });
            })
            ->latest()
            ->get();

        // 2. PERBAIKAN RIWAYAT: Ambil status 'paid', 'preparing', dan 'done'
        // Kita masukkan 'done' juga sebagai jaga-jaga jika ada data lama
        $paidOrders = Order::whereIn('status', ['paid', 'preparing', 'done', 'done'])
            ->whereDate('updated_at', Carbon::today()) 
            ->latest('updated_at')
            ->limit(15) // Kita perbanyak limitnya agar lebih terlihat
            ->get();

        return view('cashier.orderList.index', compact('pendingOrders', 'paidOrders'));
    }

    /**
     * 🔥 SELESAIKAN PESANAN (Ubah ke DONE)
     */
    public function markAsDone(Order $order)
    {
        // Pesanan hanya bisa di-done-kan jika sudah dibayar (paid/preparing)
        if ($order->status === 'pending') {
            return back()->with('error', 'Tagihan belum dibayar!');
        }

        $order->update(['status' => 'done']);

        return back()->with('success', 'Pesanan #' . $order->id . ' selesai.');
    }

    /**
     * Ambil Snap Token Midtrans
     */
    public function getSnapToken(Order $order)
    {
        $payment = $order->payment;

        if (!$payment || !$payment->snap_token) {
            return response()->json(['error' => 'Token tidak ditemukan.'], 404);
        }

        return response()->json([
            'snap_token' => $payment->snap_token,
            'order_id' => $order->id
        ]);
    }

    /**
     * Proses Bayar Cash Manual
     */
    public function pay(Order $order)
    {
        return $this->processCashPayment($order);
    }

    /**
     * Scan Barcode untuk Bayar
     */
    public function scan(Request $request)
    {
        $request->validate(['code' => 'required']);
        $order = Order::find($request->code);

        if (!$order) return back()->withErrors(['error' => 'Order tidak ditemukan']);
        
        // Jika sudah bayar/selesai, langsung ke struk
        if (in_array($order->status, ['paid', 'preparing', 'done', 'done    '])) {
            return redirect()->route('cashier.receipt.show', $order->id);
        }

        return $this->processCashPayment($order);
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
                        'paid_at' => Carbon::now()
                    ]);
                }

                // Masuk ke status 'paid' agar barista tahu harus mulai bikin kopi
                $order->update([
                    'status' => 'paid',
                    'is_printed' => true 
                ]);
            });

            return redirect()->route('cashier.receipt.show', $order->id)
                ->with('success', 'Pembayaran tunai berhasil.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal: ' . $e->getMessage()]);
        }
    }

    public function checkUnprinted()
    {
        // Cek order paid/preparing yang belum dicetak
        $order = Order::whereIn('status', ['paid', 'preparing'])
            ->where('is_printed', false)
            ->first();

        return response()->json([
            'has_new' => !!$order,
            'order_id' => $order?->id
        ]);
    }

    public function markAsPrinted(Order $order)
    {
        $order->update(['is_printed' => true]);
        return response()->json(['success' => true]);
    }
}   