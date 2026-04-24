<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Menu;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index($order)
    {
        return view('customer.payment.index');
    }

    public function process(Request $request, $order)
    {
        $checkout = session('checkout');
        $cart = json_decode($request->cart, true);

        $order = Order::create([
            'table_id' => $checkout['table_id'],
            'customer_name' => $checkout['customer_name'],
            'phone' => $checkout['phone'],
            'order_type' => 'dine_in',
            'status' => 'pending',
            'total_price' => 0
        ]);

        $total = 0;

        foreach ($cart as $item) {

            $menu = Menu::find($item['id']);
            if(!$menu) continue;

            $optionTotal = 0;

            if(isset($item['options'])){
                foreach($item['options'] as $opt){
                    $optionTotal += $opt['price'];
                }
            }

            $subtotal = $menu->price + $optionTotal;

            OrderItem::create([
                'order_id' => $order->id,
                'menu_id' => $menu->id,
                'qty' => 1,
                'price' => $menu->price,
                'subtotal' => $subtotal,
                'notes' => $item['notes'] ?? null
            ]);

            $total += $subtotal;
        }

        $order->update(['total_price' => $total]);

        // 🔥 CASH FLOW
        if($request->method == 'cash'){
            return view('customer.payment.cash', compact('order'));
        }

        return "QRIS nanti";
    }
}