<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Table;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index(Request $request)
    {
        $table = Table::findOrFail($request->table);
        return view('customer.cart.index', compact('table'));
    }

    /**
     * 🔥 SINKRONISASI DATA DARI LOCALSTORAGE KE SESSION PHP
     */
    public function sync(Request $request)
    {
        $cartData = json_decode($request->cart_data, true);

        if (!$cartData || count($cartData) == 0) {
            return back()->with('error', 'Keranjang kosong!');
        }

        $total = 0;
        foreach ($cartData as $item) {
            $optionTotal = 0;
            if (isset($item['options'])) {
                foreach ($item['options'] as $opt) {
                    $optionTotal += $opt['price'];
                }
            }
            $total += ($item['price'] + $optionTotal);
        }

        // Simpan ke session agar CheckoutController bisa baca
        session([
            'cart' => $cartData,
            'cart_total' => $total
        ]);

        return redirect()->route('customer.checkout.index', ['table' => $request->table_id]);
    }
}
