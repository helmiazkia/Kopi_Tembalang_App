<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Table;
use App\Models\Category;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    // ================= MENU LIST =================
    public function index(Request $request, Table $table)
    {
        $categories = Category::all();

        $menus = Menu::with('options.items')
            ->when($request->category, function ($q) use ($request) {
                $q->where('category_id', $request->category);
            })
            ->where('is_available', true)
            ->get();

        return view('customer.menu.index', compact('menus', 'table', 'categories'));
    }

    // ================= DETAIL MENU =================
    public function show(Table $table, Menu $menu)
    {
        $menu->load('options.items');

        return view('customer.menu.show', compact('menu', 'table'));
    }
}