<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{Order, Table, Payment, OrderItem, OrderItemOption, MenuOptionItem};
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
                $orderItem = OrderItem::create([
                    'order_id' => $order->id,
                    'menu_id'  => $item['id'],
                    'qty'      => $item['qty'] ?? 1,
                    'price'    => $item['price'],
                    'subtotal' => $item['price'] * ($item['qty'] ?? 1),
                    'notes'    => $item['notes'] ?? null
                ]);

                // 🔥 Simpan opsi menu (select & checkbox/qty) ke OrderItemOption
                if (!empty($item['options'])) {
                    foreach ($item['options'] as $optionItemId => $optData) {
                        $optionItem = MenuOptionItem::find($optionItemId);
                        if (!$optionItem) continue;

                        $qtyOpt = $optData['qty'] ?? 1;

                        // Buat record sebanyak qty (sinkron dgn pola cashier: 1 record = 1 unit opsi)
                        for ($i = 0; $i < $qtyOpt; $i++) {
                            OrderItemOption::create([
                                'order_item_id' => $orderItem->id,
                                'menu_option_item_id' => $optionItem->id,
                                'price' => $optionItem->price,
                            ]);
                        }

                        $orderItem->increment('subtotal', $optionItem->price * $qtyOpt);
                    }
                }
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
        $order->load(['items.menu', 'items.options.optionItem.option', 'table']);

        return view('customer.payment.success', compact('order'));
    }
}
