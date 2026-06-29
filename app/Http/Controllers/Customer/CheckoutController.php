<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\{Order, Table, Payment, OrderItem};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Midtrans\Snap;
use Midtrans\Config;
use Carbon\Carbon;

class CheckoutController extends Controller
{
    public function index(Request $request, $tableId)
    {
        $table = Table::findOrFail($tableId);

        if (!session('cart') || count(session('cart')) == 0) {
            return redirect()->route('customer.menu', $table->id)->with('error', 'Keranjang Anda masih kosong.');
        }

        return view('customer.checkout.index', compact('table'));
    }

    public function store(Request $request, $tableId)
    {
        $request->validate([
            'customer_name'  => 'required|string|max:255',
            'email'          => 'nullable|email', // Perbaikan: nullable jika opsional
            'phone'          => 'required',
            'payment_method' => 'required|in:cash,qris'
        ]);

        $cart = session('cart');
        $totalPrice = session('cart_total');
        $table = Table::findOrFail($tableId);

        return DB::transaction(function () use ($request, $cart, $totalPrice, $table) {
            // 1. Buat Order
            $order = Order::create([
                'table_id'      => $table->id,
                'customer_name' => $request->customer_name,
                'email'         => $request->email,
                'phone'         => $request->phone,
                'order_type'    => 'dine_in',
                'total_price'   => $totalPrice,
                'status'        => 'pending',
                'is_printed'    => false,
            ]);

            // 2. Simpan Items
            foreach ($cart as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'menu_id'  => $item['id'],
                    'qty'      => $item['qty'] ?? 1, // Ambil qty asli dari cart
                    'price'    => $item['price'],
                    'subtotal' => $item['price'] * ($item['qty'] ?? 1),
                    'notes'    => $item['notes'] ?? null
                ]);
                // Jika ada logika OrderItemOption, masukkan di sini
            }

            // 3. Buat Data Payment
            $transactionId = 'LODO-' . time() . '-' . $order->id;
            $payment = Payment::create([
                'order_id'       => $order->id,
                'transaction_id' => $transactionId,
                'method'         => $request->payment_method,
                'amount'         => $totalPrice,
                'status'         => 'pending',
                'expired_at'     => Carbon::now()->addMinutes(12)
            ]);

            // 4. LOGIKA REDIRECT
            session()->forget(['cart', 'cart_total']); // Hapus cart lebih awal agar aman

            if ($request->payment_method === 'qris') {
                Config::$serverKey = config('midtrans.serverKey');
                Config::$isProduction = config('midtrans.isProduction', false);


                $params = [
                    'transaction_details' => [
                        'order_id'     => $transactionId,
                        'gross_amount' => (int) $totalPrice,
                    ],

                    'customer_details' => [
                        'first_name' => $order->customer_name,
                        'email'      => $order->email,
                        'phone'      => $order->phone,
                    ],


                    'expiry' => [
                        'start_time' => now()->format('Y-m-d H:i:s O'),
                        'unit'       => 'minute',
                        'duration'   => 12,
                    ],
                ];

                try {
                    $snapToken = Snap::getSnapToken($params);
                    $payment->update(['snap_token' => $snapToken]);
                    return redirect()->route('customer.payment.process', $order->id);
                } catch (\Exception $e) {
                    return back()->with('error', 'Midtrans Error: ' . $e->getMessage());
                }
            }

            // Alur Cash (Sesuai rute yang baru ditambah)
            return redirect()->route('customer.payment.cash', $order->id);
        });
    }

    public function cash(Order $order)
    {
        $order->load(['table', 'payment']);
        return view('customer.payment.cash', compact('order'));
    }

    public function process(Order $order)
    {
        $order->load('payment');
        return view('customer.payment.process', compact('order'));
    }

    public function checkStatus(Order $order)
    {
        // 🔥 AUTO-CANCEL SEMUA EXPIRED PAYMENTS
        \App\Models\Payment::cancelAllExpired();

        $order->load('payment');

        // 🔥 AUTO-CHECK EXPIRATION UNTUK ORDER INI JUGA
        // Jika pembayaran masih pending dan waktu sudah habis, ubah status ke cancelled
        if ($order->payment && $order->payment->status === 'pending' && $order->payment->isExpired()) {
            $order->payment->update(['status' => 'expired']);
            $order->update(['status' => 'cancelled']);
        }

        return response()->json(['status' => $order->status]);
    }

    public function success(Order $order)
    {
        // Load items, menu, dan table untuk informasi lengkap di struk
        $order->load(['items.menu', 'table']);

        return view('customer.payment.success', compact('order'));
    }
}
