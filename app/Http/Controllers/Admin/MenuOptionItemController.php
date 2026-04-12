<?php

namespace App\Http\Controllers\Admin;

use App\Models\MenuOptionItem;
use App\Models\MenuOption;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MenuOptionItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $menu_option_items = MenuOptionItem::with('option')->latest()->get();
        $menu_options = MenuOption::all(); 
        return view('admin.menu_option_items.index', compact('menu_option_items','menu_options'));
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
                'menu_option_id' => 'required',
                'name' => 'required',
                'price' => 'required|numeric'
            ]);


            MenuOptionItem::create($validatedData);
            return back()->with('success', 'Menu Option Item berhasil ditambahkan');
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
    public function update(Request $request, MenuOptionItem $menu_option_item)
    {
        try {
            $validatedData = $request->validate([
                'menu_option_id' => 'required',
                'name' => 'required',
                'price' => 'required|numeric'
            ]);


            $menu_option_item->update($validatedData);
            return back()->with('success', 'Menu Option Item berhasil ditambahkan');
        } catch (\Exception $e) {
            return back()->withErrors([
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MenuOptionItem $menu_option_item)
    {
        try {
            $menu_option_item->delete();

            return back()->with('success', 'Menu option item berhasil dihapus');
        } catch (\Exception $e) {
            return back()->withErrors([
                'error' => $e->getMessage()
            ]);
        }
    }
}
