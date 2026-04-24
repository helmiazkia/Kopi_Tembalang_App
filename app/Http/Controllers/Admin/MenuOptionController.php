<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MenuOption;
use Illuminate\Http\Request;

class MenuOptionController extends Controller
{
    public function index()
    {
        $menu_options = MenuOption::latest()->get();

        return view('admin.menu_options.index', compact('menu_options'));
    }

    public function store(Request $request)
    {
        try {

            $validated = $request->validate([
                'name' => 'required|string|max:100|unique:menu_options,name',
                'type' => 'required|in:select,checkbox'
            ]);

            MenuOption::create($validated);

            return back()->with('success', 'Option berhasil ditambahkan');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function update(Request $request, MenuOption $menu_option)
    {
        try {

            $validated = $request->validate([
                'name' => 'required|string|max:100|unique:menu_options,name,' . $menu_option->id,
                'type' => 'required|in:select,checkbox'
            ]);

            $menu_option->update($validated);

            return back()->with('success', 'Option berhasil diupdate');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function destroy(MenuOption $menu_option)
    {
        try {

            // hapus relasi pivot
            $menu_option->menus()->detach();

            // hapus items
            $menu_option->items()->delete();

            $menu_option->delete();

            return back()->with('success', 'Option berhasil dihapus');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
