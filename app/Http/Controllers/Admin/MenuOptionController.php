<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\MenuOption;
use Illuminate\Http\Request;

class MenuOptionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $menu_options = MenuOption::with('menu')->latest()->get();
        $menus = Menu::all();
        return view('admin.menu_options.index', compact('menu_options', 'menus'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {

            $validatedData = $request->validate([
                'menu_id' => 'required|exists:menus,id',
                'name' => 'required|string|max:100',
                'type' => 'required|in:select,checkbox'
            ]);

            MenuOption::create($validatedData);

            return back()->with('success', 'Menu Option berhasil ditambahkan');
        } catch (\Exception $e) {

            return back()->withErrors([
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MenuOption $menu_option)
    {
        try {

            $validatedData = $request->validate([
                'menu_id' => 'required|exists:menus,id',
                'name' => 'required|string|max:100',
                'type' => 'required|in:select,checkbox'
            ]);

            $menu_option->update($validatedData);
            return back()->with('success', 'Menu Option berhasil ditambahkan');
        } catch (\Exception $e) {

            return back()->withErrors([
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MenuOption $menu_option)
    {
        try {
            $menu_option->delete();

            return back()->with('success', 'Menu option berhasil dihapus');
        } catch (\Exception $e) {
            return back()->withErrors([
                'error' => $e->getMessage()
            ]);
        }
    }
}
