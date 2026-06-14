<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Models\Menu;

class MenuController extends Controller
{
    public function index()
    {
        $menus = Menu::with('category')->latest()->get();

        return view('cashier.menus.index', compact('menus'));
    }

    public function toggleAvailability(Menu $menu)
    {
        $menu->update(['is_available' => !$menu->is_available]);

        $status = $menu->is_available ? 'tersedia' : 'habis';

        return back()->with('success', "Menu \"{$menu->name}\" ditandai sebagai {$status}.");
    }
}