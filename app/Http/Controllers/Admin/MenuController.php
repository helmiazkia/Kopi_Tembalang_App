<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Category;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function index()
    {
        $menus = Menu::with('category')->latest()->get();
        $categories = Category::all();
        return view('admin.menus.index', compact('menus', 'categories'));
    }


    public function create() {}

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
            ]);

            if ($request->hasFile('image')) {
                $imageName = time() . '_' . uniqid() . '.' . $request->image->extension();
                $request->image->move(public_path('images/menu'), $imageName);
                $validatedData['image'] = $imageName;
            }

            Menu::create($validatedData);

            return back()->with('success', 'Menu berhasil ditambahkan');
        } catch (\Exception $e) {
            return back()->withErrors([
                'error' => $e->getMessage()
            ]);
        }
    }
    public function edit(Menu $menu) {}

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

            return back()->with('success', 'Menu berhasil diupdate');
        } catch (\Exception $e) {
            return back()->withErrors([
                'error' => $e->getMessage()
            ]);
        }
    }
    public function destroy(Menu $menu)
    {
        try {
            if ($menu->image && file_exists(public_path('images/menu/' . $menu->image))) {
                unlink(public_path('images/menu/' . $menu->image));
            }

            $menu->delete();

            return back()->with('success', 'Menu berhasil dihapus');
        } catch (\Exception $e) {
            return back()->withErrors([
                'error' => $e->getMessage()
            ]);
        }
    }
}
