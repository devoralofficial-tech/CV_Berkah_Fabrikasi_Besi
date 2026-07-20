<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCategoryRequest;
use App\Http\Requests\Admin\UpdateCategoryRequest;
use App\Models\Category;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index()
    {
        $parentCategories = Category::whereNull('parent_id')
            ->with(['children.products'])
            ->withCount('products')
            ->orderBy('name')
            ->get();

        return view('admin.categories.index', compact('parentCategories'));
    }

    public function create()
    {
        $parentCategories = Category::whereNull('parent_id')->orderBy('name')->get();
        return view('admin.categories.create', compact('parentCategories'));
    }

    public function store(StoreCategoryRequest $request)
    {
        $parentId = $request->parent_id ?: null;

        // Ensure the selected parent is not itself a child (max 2 levels)
        if ($parentId) {
            $parent = Category::findOrFail($parentId);
            if ($parent->parent_id !== null) {
                return back()->withErrors(['parent_id' => 'Kategori anak tidak dapat dijadikan induk.'])->withInput();
            }
        }

        $slug = Str::slug($request->name);
        $originalSlug = $slug;
        $counter = 1;
        while (Category::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter++;
        }

        Category::create([
            'name'      => $request->name,
            'slug'      => $slug,
            'parent_id' => $parentId,
        ]);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function edit(Category $category)
    {
        $parentCategories = Category::whereNull('parent_id')
            ->where('id', '!=', $category->id)
            ->orderBy('name')
            ->get();

        return view('admin.categories.edit', compact('category', 'parentCategories'));
    }

    public function update(UpdateCategoryRequest $request, Category $category)
    {
        $parentId = $request->parent_id ?: null;

        if ($parentId) {
            $parent = Category::findOrFail($parentId);
            if ($parent->parent_id !== null) {
                return back()->withErrors(['parent_id' => 'Kategori anak tidak dapat dijadikan induk.'])->withInput();
            }
            if ($parentId === $category->id) {
                return back()->withErrors(['parent_id' => 'Kategori tidak dapat menjadi induk dirinya sendiri.'])->withInput();
            }
        }

        $category->update([
            'name'      => $request->name,
            'parent_id' => $parentId,
        ]);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy(Category $category)
    {
        // Check if this category has children
        if ($category->children()->exists()) {
            return back()->with('error', 'Tidak dapat menghapus kategori yang masih memiliki sub-kategori.');
        }

        // Check if this category has products
        if ($category->products()->exists()) {
            return back()->with('error', 'Tidak dapat menghapus kategori yang masih memiliki produk.');
        }

        $category->delete();
        return redirect()->route('admin.categories.index')
            ->with('success', 'Kategori berhasil dihapus.');
    }
}
