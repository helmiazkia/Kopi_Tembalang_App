<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Category;
use App\Models\MenuOption;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function index()
    {
        $menus = Menu::with(['category', 'options'])->latest()->get();
        $categories = Category::all();
        $options = MenuOption::all();

        return view('admin.menus.index', compact('menus', 'categories', 'options'));
    }

    public function store(Request $request)
    {
        try {

            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'category_id' => 'required|exists:categories,id',
                'price' => 'required|numeric',
                'description' => 'nullable|string',
                'is_available' => 'required|in:0,1',
                'image' => 'nullable|image|max:2048',
                'menu_option_ids' => 'nullable|array'
            ]);

            // upload gambar
            if ($request->hasFile('image')) {
                $imageName = time() . '_' . uniqid() . '.' . $request->image->extension();
                $request->image->move(public_path('images/menu'), $imageName);
                $validatedData['image'] = $imageName;
            }

            $menu = Menu::create($validatedData);

            // 🔥 pivot option
            $menu->options()->sync($request->menu_option_ids ?? []);

            return back()->with('success', 'Menu berhasil ditambahkan');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function update(Request $request, Menu $menu)
    {
        try {

            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'category_id' => 'required|exists:categories,id',
                'price' => 'required|numeric',
                'description' => 'nullable|string',
                'is_available' => 'required|in:0,1',
                'image' => 'nullable|image|max:2048',
                'menu_option_ids' => 'nullable|array'
            ]);

            if ($request->hasFile('image')) {

                if ($menu->image && file_exists(public_path('images/menu/' . $menu->image))) {
                    unlink(public_path('images/menu/' . $menu->image));
                }

                $imageName = time() . '_' . uniqid() . '.' . $request->image->extension();
                $request->image->move(public_path('images/menu'), $imageName);

                $validatedData['image'] = $imageName;
            }

            $menu->update($validatedData);

            // 🔥 update pivot
            $menu->options()->sync($request->menu_option_ids ?? []);

            return back()->with('success', 'Menu berhasil diupdate');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function destroy(Menu $menu)
    {
        try {

            // hapus pivot dulu
            $menu->options()->detach();

            if ($menu->image && file_exists(public_path('images/menu/' . $menu->image))) {
                unlink(public_path('images/menu/' . $menu->image));
            }

            $menu->delete();

            return back()->with('success', 'Menu berhasil dihapus');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
