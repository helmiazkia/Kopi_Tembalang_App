<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::latest()->get();
        return view('admin.categories.index', compact('categories'));
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
                'name' => 'required|string|max:100',
                'image' => 'nullable|image|max:2048',
                'description' => 'nullable|string',
            ]);

            if ($request->hasFile('image')) {
                $imageName = time() . '_' . uniqid() . '.' . $request->image->extension();
                $request->image->move(public_path('images/category'), $imageName);
                $validatedData['image'] = $imageName;
            }

            Category::create($validatedData);

            return back()->with('success', 'Kategori berhasil ditambahkan');
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
    public function edit(Category $category)
    {
        return response()->json($category);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:100',
                'description' => 'nullable|string',
                'image' => 'nullable|image|max:2048',
            ]);

            if ($request->hasFile('image')) {

                if ($category->image && file_exists(public_path('images/category/' . $category->image))) {
                    unlink(public_path('images/category/' . $category->image));
                }

                $imageName = time() . '_' . uniqid() . '.' . $request->image->extension();
                $request->image->move(public_path('images/category'), $imageName);

                $validatedData['image'] = $imageName;
            }


            $category->update($validatedData);

            return back()->with('success', 'Kategori berhasil diupdate');
        } catch (\Exception $e) {
            return back()->withErrors([
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        try {
            if ($category->image && file_exists(public_path('images/category/' . $category->image))) {
                unlink(public_path('images/category/' . $category->image));
            }

            $category->delete();

            return back()->with('success', 'Kategori berhasil dihapus');
        } catch (\Exception $e) {
            return back()->withErrors([
                'error' => $e->getMessage()
            ]);
        }
    }
}
