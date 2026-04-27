<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\MenuOptionItem;
use App\Models\Order;
use App\Models\Table;
use App\Models\Category;
use App\Models\OrderItem;
use App\Models\OrderItemOption;
use App\Models\Payment;
use Midtrans\Transaction;
use Illuminate\Http\Request;
use Midtrans\Snap;
use Midtrans\Config;

class OrderController extends Controller
{
    public function index(Request $request, Table $table)
    {
        $categories = Category::all();
        $menus = Menu::with('options.items')
            //Filter kategori
            ->when($request->category, function ($q) use ($request) {
                $q->where('category_id', $request->category);
            })
            // Filter Pencarian Nama
            ->when($request->search, function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            })
            // Filter Stok
            ->when($request->stock === 'available', function ($q) {
                $q->where('is_available', true);
            })
            ->when($request->stock === 'empty', function ($q) {
                $q->where('is_available', false);
            })
            // Urutan Harga
            ->when($request->sort === 'price_low', function ($q) {
                $q->orderBy('price', 'asc');
            })
            ->when($request->sort === 'price_high', function ($q) {
                $q->orderBy('price', 'desc');
            })
            ->get();
        $tables = Table::all();

        return view('cashier.orders.index', compact('menus', 'tables', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'order_type' => 'required',
            'table_id' => 'nullable|required_if:order_type,dine_in',
            'items' => 'required|array',
            'payment_method' => 'required|in:cash,qris'
        ]);

        // ================= ORDER =================
        $order = Order::create([
            'table_id' => $request->order_type == 'dine_in' ? $request->table_id : null,
            'cashier_id' => auth()->id(),
            'order_type' => $request->order_type,
            'customer_name' => $request->customer_name ?? 'Walk In',
            'phone' => $request->phone,
            'notes' => $request->notes,
            'total_price' => 0,
            'status' => 'pending'
        ]);

        $total = 0;

        foreach ($request->items as $item) {

            $menu = Menu::find($item['menu_id']);
            if (!$menu) continue;

            $orderItem = OrderItem::create([
                'order_id' => $order->id,
                'menu_id' => $menu->id,
                'qty' => 1,
                'price' => $menu->price,
                'subtotal' => 0,
                'notes' => $item['notes'] ?? null
            ]);

            $optionTotal = $this->processOptions($orderItem, $item);

            $subtotal = $menu->price + $optionTotal;

            $orderItem->update(['subtotal' => $subtotal]);

            $total += $subtotal;
        }

        $order->update(['total_price' => $total]);

        // ================= PAYMENT =================
        $transactionId = 'ORDER-' . time() . '-' . $order->id;

        $payment = Payment::create([
            'order_id' => $order->id,
            'transaction_id' => $transactionId,
            'method' => $request->payment_method,
            'channel' => null, // 🔥 kosong dulu
            'amount' => $total,
            'status' => $request->payment_method === 'cash' ? 'paid' : 'pending',
            'paid_at' => $request->payment_method === 'cash' ? now() : null
        ]);

        // ================= CASH =================
        if ($request->payment_method === 'cash') {

            $order->update(['status' => 'paid']);

            return response()->json([
                'type' => 'cash',
                'order_id' => $order->id
            ]);
        }

        // ================= QRIS =================
        Config::$serverKey = config('midtrans.serverKey');
        Config::$isProduction = false;

        $params = [
            'transaction_details' => [
                'order_id' => $transactionId,
                'gross_amount' => (int) $total,
            ],


            // 🔥 EXPIRY
            'expiry' => [
                'start_time' => now()->format('Y-m-d H:i:s O'),
                'unit' => 'minute',
                'duration' => 10
            ]
        ];

        $snapToken = Snap::getSnapToken($params);

        $payment->update([
            'snap_token' => $snapToken,
            'expired_at' => now()->addMinutes(10)
        ]);

        return response()->json([
            'type' => 'qris',
            'snap_token' => $snapToken,
            'order_id' => $order->id
        ]);
    }

    /**
     * PROSES OPTION
     */
    private function processOptions(OrderItem $orderItem, array $item): int
    {
        $optionTotal = 0;

        if (empty($item['options'])) return 0;

        foreach ($item['options'] as $optionItemId) {

            $optionItem = MenuOptionItem::find($optionItemId);
            if (!$optionItem) continue;

            OrderItemOption::create([
                'order_item_id' => $orderItem->id,
                'menu_option_item_id' => $optionItem->id,
                'price' => $optionItem->price,
            ]);

            // 🔥 karena qty = 1
            $optionTotal += $optionItem->price;
        }

        return $optionTotal;
    }

    public function cancel(Order $order)
    {
        $payment = $order->payment;

        if (!$payment) {
            return response()->json(['message' => 'Payment tidak ditemukan'], 404);
        }

        // ==============================
        // 🔥 UPDATE DATABASE
        // ==============================
        $order->update([
            'status' => 'cancelled'
        ]);

        $payment->update([
            'status' => 'expired'
        ]);

        // ==============================
        // 🔥 CANCEL KE MIDTRANS (OPTIONAL)
        // ==============================
        try {
            if ($payment->transaction_id) {
                \Midtrans\Config::$serverKey = config('midtrans.serverKey');
                \Midtrans\Config::$isProduction = false;

                Transaction::cancel($payment->transaction_id);
            }
        } catch (\Exception $e) {
            \Log::error("Midtrans cancel gagal: " . $e->getMessage());
        }

        return response()->json([
            'success' => true
        ]);
    }
}
