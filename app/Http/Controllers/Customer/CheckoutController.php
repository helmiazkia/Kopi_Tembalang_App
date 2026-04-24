<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Table;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    public function index(Request $request)
    {
        $table = Table::findOrFail($request->table);

        return view('customer.checkout.index', compact('table'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'table_id' => 'required',
            'customer_name' => 'required',
            'phone' => 'required',
        ]);

        // simpan sementara ke session
        session([
            'checkout' => $request->only('table_id', 'customer_name', 'phone')
        ]);

        return redirect()->route('customer.payment', 0); // dummy dulu
    }
}
    