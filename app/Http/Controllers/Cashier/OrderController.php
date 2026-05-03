<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Models\{Menu, MenuOptionItem, Order, Table, Category, OrderItem, OrderItemOption, Payment};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{DB, Log};
use Midtrans\{Snap, Config, Transaction};

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::all();
        $tables = Table::orderBy('table_number', 'asc')->get();

        $menus = Menu::with('options.items')
            ->when($request->category, fn($q) => $q->where('category_id', $request->category))
            ->when($request->search, fn($q) => $q->where('name', 'like', '%' . $request->search . '%'))
            ->when($request->stock === 'available', fn($q) => $q->where('is_available', true))
            ->when($request->stock === 'empty', fn($q) => $q->where('is_available', false))
            ->when($request->sort === 'price_low', fn($q) => $q->orderBy('price', 'asc'))
            ->when($request->sort === 'price_high', fn($q) => $q->orderBy('price', 'desc'))
            ->get();

        return view('cashier.orders.index', compact('menus', 'tables', 'categories'));
    }

    public function store(Request $request)
    {
        // 1. Validasi Super Lengkap
        $request->validate([
            'order_type' => 'required|in:dine_in,takeaway',
            'table_id' => 'nullable|required_if:order_type,dine_in',
            'customer_name' => 'required|string|max:255',
            'email' => 'nullable|email', // Tambahkan validasi email
            'items' => 'required|array',
            'payment_method' => 'required|in:cash,qris'
        ]);

        return DB::transaction(function () use ($request) {
            // 2. Buat Order
            $order = Order::create([
                'table_id' => $request->order_type == 'dine_in' ? $request->table_id : null,
                'cashier_id' => auth()->id(),
                'order_type' => $request->order_type,
                'customer_name' => $request->customer_name,
                'email' => $request->email, // Simpan Email
                'phone' => $request->phone,
                'notes' => $request->notes,
                'total_price' => 0,
                'status' => 'pending',
                'is_printed' => true,
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

            // 3. Buat Payment
            $transactionId = 'ORDER-' . time() . '-' . $order->id;
            $payment = Payment::create([
                'order_id' => $order->id,
                'transaction_id' => $transactionId,
                'method' => $request->payment_method,
                'amount' => $total,
                'status' => $request->payment_method === 'cash' ? 'paid' : 'pending',
                'paid_at' => $request->payment_method === 'cash' ? now() : null,
                'expired_at' => now()->addMinutes(10)
            ]);

            if ($request->payment_method === 'cash') {
                $order->update(['status' => 'paid']);
                return response()->json(['type' => 'cash', 'order_id' => $order->id]);
            }

            // 4. Midtrans Snap
            Config::$serverKey = config('midtrans.serverKey');
            Config::$isProduction = config('midtrans.isProduction', false);

            $params = [
                'transaction_details' => [
                    'order_id' => $transactionId,
                    'gross_amount' => (int) $total,
                ],
                'customer_details' => [
                    'first_name' => $order->customer_name,
                    'email' => $order->email, // Kirim Email ke Midtrans
                    'phone' => $order->phone,
                ],
                'enabled_payments' => ['qris', 'gopay', 'shopeepay'],
                'expiry' => ['start_time' => now()->format('Y-m-d H:i:s O'), 'unit' => 'minute', 'duration' => 10]
            ];

            $snapToken = Snap::getSnapToken($params);
            $payment->update(['snap_token' => $snapToken]);

            return response()->json(['type' => 'qris', 'snap_token' => $snapToken, 'order_id' => $order->id]);
        });
    }

    private function processOptions(OrderItem $orderItem, array $item): int
    {
        if (empty($item['options'])) return 0;
        $optionTotal = 0;

        foreach ($item['options'] as $optionItemId) {
            $optionItem = MenuOptionItem::find($optionItemId);
            if (!$optionItem) continue;

            OrderItemOption::create([
                'order_item_id' => $orderItem->id,
                'menu_option_item_id' => $optionItem->id,
                'price' => $optionItem->price,
            ]);
            $optionTotal += $optionItem->price;
        }

        return $optionTotal;
    }
}
