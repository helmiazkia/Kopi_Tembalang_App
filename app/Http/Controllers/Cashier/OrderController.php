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
use Illuminate\Http\Request;

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
            'items' => 'required|array'
        ]);

        // 🔥 CREATE ORDER
        $order = Order::create([
            'table_id' => $request->order_type == 'dine_in'
                ? $request->table_id
                : null,

            'cashier_id' => auth()->id(),

            'order_type' => $request->order_type,
            'customer_name' => $request->customer_name ?? 'Walk In',
            'phone' => $request->phone,

            'notes' => $request->notes, // GLOBAL NOTES

            'total_price' => 0,
            'status' => 'pending'
        ]);

        $total = 0;

        foreach ($request->items as $item) {

            $menu = Menu::find($item['menu_id']);
            if (!$menu) continue;

            // 🔥 CREATE ITEM
            $orderItem = OrderItem::create([
                'order_id' => $order->id,
                'menu_id' => $menu->id,
                'qty' => 1, // karena sistem 1 item = 1
                'price' => $menu->price,
                'subtotal' => 0,
                'notes' => $item['notes'] ?? null // ITEM NOTES
            ]);

            // 🔥 PROCESS OPTION
            $optionTotal = $this->processOptions($orderItem, $item);

            // 🔥 FIX: TANPA QTY
            $subtotal = $menu->price + $optionTotal;

            $orderItem->update([
                'subtotal' => $subtotal
            ]);

            $total += $subtotal;
        }

        // 🔥 UPDATE TOTAL ORDER
        $order->update([
            'total_price' => $total
        ]);

        return redirect()
            ->route('cashier.orders.index')
            ->with('success', 'Order berhasil dibuat!');
    }

    /**
     * PROSES OPTION
     */
    private function processOptions(OrderItem $orderItem, array $item): int
    {
        $optionTotal = 0;

        if (empty($item['options'])) return 0;

        foreach ($item['options'] as $optionId => $optionItemId) {

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
}
