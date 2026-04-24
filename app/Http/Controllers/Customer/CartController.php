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
}