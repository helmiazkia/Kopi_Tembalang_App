<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::latest()->get();

        return view('admin.categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:100',
            'description' => 'nullable|string',
            'image'       => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $this->uploadImage($request->file('image'));
        }

        Category::create($validated);

        return back()->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:100',
            'description' => 'nullable|string',
            'image'       => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $this->deleteImage($category->image);
            $validated['image'] = $this->uploadImage($request->file('image'));
        }

        $category->update($validated);

        return back()->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy(Category $category)
    {
        $this->deleteImage($category->image);
        $category->delete();

        return back()->with('success', 'Kategori berhasil dihapus.');
    }

    // ── Helpers ───────────────────────────────────────────────────────

    private function uploadImage($file): string
    {
        $name = time() . '_' . uniqid() . '.' . $file->extension();
        $file->move(public_path('images/category'), $name);

        return $name;
    }

    private function deleteImage(?string $image): void
    {
        if ($image && file_exists(public_path('images/category/' . $image))) {
            unlink(public_path('images/category/' . $image));
        }
    }
}