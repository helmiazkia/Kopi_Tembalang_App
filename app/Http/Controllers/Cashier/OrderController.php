<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Table;
use App\Models\Menu;
use App\Models\Payment;
use App\Models\OrderLog;
use Illuminate\Http\Request;

class OrderController extends Controller
{

    // ===============================
    // LIST ORDER MASUK
    // ===============================
    public function index()
    {
        $orders = Order::with([
            'table',
            'items.menu',
            'items.options.optionItem.menuOption',
            'payment'
        ])
        ->latest()
        ->get();

        return view('cashier.orders.index', compact('orders'));
    }

    // ===============================
    // FORM INPUT ORDER MANUAL
    // ===============================
    public function create()
    {
        $tables = Table::all();
        $menus = Menu::with('category')->get();

        return view('cashier.orders.create', compact('tables','menus'));
    }

    // ===============================
    // SIMPAN ORDER + PAYMENT
    // ===============================
    public function store(Request $request)
    {
        $order = Order::create([
            'table_id' => $request->table_id,
            'customer_name' => $request->customer_name,
            'total_price' => $request->total,
            'status' => $request->method == 'cash' ? 'pending' : 'paid',
            'cashier_id' => auth()->id()
        ]);

        // =========================
        // PAYMENT
        // =========================
        $payment = Payment::create([
            'order_id' => $order->id,
            'method' => $request->method,
            'channel' => $request->channel,
            'amount' => $order->total_price,
            'status' => $request->method == 'cash' ? 'pending' : 'paid',
            'paid_at' => $request->method == 'cash' ? null : now()
        ]);

        // =========================
        // LOG
        // =========================
        OrderLog::create([
            'order_id' => $order->id,
            'status' => $order->status,
            'user_id' => auth()->id()
        ]);

        // =========================
        // AUTO STRUK (NON CASH)
        // =========================
        if($request->method != 'cash'){
            return redirect()->route('receipt.show',$order->id);
        }

        return redirect()->route('cashier.orders.index')
            ->with('success','Order berhasil dibuat');
    }

    // ===============================
    // SCAN CASH (BARCODE)
    // ===============================
    public function payCash($id)
    {
        $order = Order::findOrFail($id);

        $payment = $order->payment;

        $payment->update([
            'status' => 'paid',
            'paid_at' => now()
        ]);

        $order->update([
            'status' => 'paid'
        ]);

        OrderLog::create([
            'order_id' => $order->id,
            'status' => 'paid',
            'user_id' => auth()->id()
        ]);

        return redirect()->route('receipt.show',$order->id);
    }

}