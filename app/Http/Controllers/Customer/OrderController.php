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
            // Filter berdasarkan kategori jika ada
            ->when($request->category, function ($q) use ($request) {
                $q->where('category_id', $request->category);
            })
            ->when($request->stock === 'available', fn($q) => $q->where('is_available', true))
            ->when($request->stock === 'empty', fn($q) => $q->where('is_available', false))
            ->get();

        return view('customer.menu.index', compact('menus', 'table', 'categories'));
    }

    // ================= DETAIL MENU =================
    public function show(Table $table, Menu $menu)
    {
        // Proteksi: Jika user mencoba akses menu habis via URL langsung
        if (!$menu->is_available) {
            return redirect()->route('customer.menu', $table->id)
                ->with('error', 'Maaf, menu ini sedang tidak tersedia.');
        }

        $menu->load('options.items');
        return view('customer.menu.show', compact('menu', 'table'));
    }
}
