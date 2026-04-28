<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\{Order, Table, Payment, OrderItem};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Midtrans\Snap;
use Midtrans\Config;

class CheckoutController extends Controller
{
    /**
     * 🔥 MENAMPILKAN HALAMAN FORM DATA PEMESAN
     * Method ini yang menyebabkan error jika tidak ada.
     */
    public function index(Request $request)
    {
        // 1. Ambil data table
        $table = Table::findOrFail($request->table);

        // 2. Jika keranjang kosong, redirect balik ke menu meja tersebut
        if (!session('cart') || count(session('cart')) == 0) {
            // 🔥 PERBAIKAN: Tambahkan parameter $table->id atau $table
            return redirect()->route('customer.menu', $table->id)->with('error', 'Keranjang Anda masih kosong.');
        }

        return view('customer.checkout.index', compact('table'));
    }

    /**
     * 🔥 PROSES SIMPAN PESANAN DAN REDIRECT PEMBAYARAN
     */
    public function store(Request $request)
    {
        $request->validate([
            'table_id'       => 'required|exists:tables,id',
            'customer_name'  => 'required|string|max:255',
            'email'          => 'required|email',
            'phone'          => 'required',
            'payment_method' => 'required|in:cash,qris'
        ]);

        $cart = session('cart');
        $totalPrice = session('cart_total');

        return DB::transaction(function () use ($request, $cart, $totalPrice) {
            // 1. Buat Order (Langsung Dine In)
            $order = Order::create([
                'table_id'      => $request->table_id,
                'customer_name' => $request->customer_name,
                'email'         => $request->email,
                'phone'         => $request->phone,
                'order_type'    => 'dine_in', // 🔥 Kunci di sini
                'total_price'   => $totalPrice,
                'status'        => 'pending'
            ]);

            // 2. Simpan Items (Looping dari session cart)
            foreach ($cart as $item) {
                $orderItem = OrderItem::create([
                    'order_id' => $order->id,
                    'menu_id'  => $item['id'],
                    'qty'      => 1,
                    'price'    => $item['price'],
                    'subtotal' => $item['price'], // Sederhanakan atau tambah logika opsi
                    'notes'    => $item['notes'] ?? null
                ]);
            }

            // 3. Buat Data Payment
            $transactionId = 'CUST-' . time() . '-' . $order->id;
            $payment = Payment::create([
                'order_id'       => $order->id,
                'transaction_id' => $transactionId,
                'method'         => $request->payment_method,
                'amount'         => $totalPrice,
                'status'         => 'pending',
                'expired_at'     => now()->addMinutes(15)
            ]);

            // 4. LOGIKA REDIRECT
            if ($request->payment_method === 'qris') {
                // Alur Midtrans
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
                    'enabled_payments' => ['qris', 'gopay', 'shopeepay'],
                ];

                try {
                    $snapToken = Snap::getSnapToken($params);
                    $payment->update(['snap_token' => $snapToken]);

                    // Hapus cart setelah berhasil generate snap token
                    session()->forget(['cart', 'cart_total']);

                    // Redirect ke halaman khusus yang memicu popup Midtrans
                    return redirect()->route('customer.payment.process', $order->id);
                } catch (\Exception $e) {
                    return back()->with('error', 'Gagal terhubung ke Midtrans: ' . $e->getMessage());
                }
            }

            // Alur Cash (Tunai ke Kasir)
            session()->forget(['cart', 'cart_total']);
            return redirect()->route('customer.payment.cash', $order->id);
        });
    }
    // app/Http/Controllers/Customer/CheckoutController.php

    public function process(Order $order)
    {
        // Load payment untuk mengambil snap_token
        $order->load('payment');

        // Kirim data ke view khusus pembayaran
        return view('customer.payment.process', compact('order'));
    }

    /**
     * 🔥 HALAMAN SETELAH PEMBAYARAN BERHASIL (SUCCESS PAGE)
     */
    public function success(Order $order)
    {
        // Load item dan menu agar bisa ditampilkan di struk digital customer
        $order->load('items.menu');

        return view('customer.payment.success', compact('order'));
    }
}
