<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Table;
use App\Models\Menu;
use Illuminate\Http\Request;
use App\Models\MenuOptionItem;
use App\Models\OrderItemOption;

class OrderSimulationController extends Controller
{

    public function index()
    {
        $tables = Table::all();
        $menus = Menu::with('options.items')->get();
        $optionItems = MenuOptionItem::with('menuOption')->get();

        return view('order.index', compact(
            'tables',
            'menus',
            'optionItems'
        ));
    }
    public function store(Request $request)
    {

        $order = Order::create([
            'table_id' => $request->table_id,
            'customer_name' => $request->customer_name,
            'status' => 'pending',
            'total_price' => 0
        ]);

        $total = 0;

        foreach ($request->items as $item) {

            $menu = Menu::find($item['menu_id']);

            $subtotal = $menu->price * $item['qty'];

            $orderItem = OrderItem::create([
                'order_id' => $order->id,
                'menu_id' => $menu->id,
                'qty' => $item['qty'],
                'price' => $menu->price,
                'subtotal' => $subtotal
            ]);

            $total += $subtotal;

            if (isset($item['options'])) {

                foreach ($item['options'] as $option_id) {

                    $option = MenuOptionItem::find($option_id);

                    OrderItemOption::create([
                        'order_item_id' => $orderItem->id,
                        'menu_option_item_id' => $option->id,
                        'price' => $option->price ?? 0
                    ]);

                    $total += $option->price ?? 0;
                }
            }
        }

        $order->update([
            'total_price' => $total
        ]);

        return back()->with('success', 'Order berhasil dibuat');
    }
}
