<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['category.parent'])
            ->whereNull('deleted_at');

        // Filter by category slug (parent or child)
        if ($request->filled('kategori')) {
            $category = Category::where('slug', $request->kategori)->first();
            if ($category) {
                if ($category->isParent()) {
                    // Show all products from all children of this parent
                    $childIds = $category->children()->pluck('id');
                    $query->whereIn('category_id', $childIds);
                } else {
                    $query->where('category_id', $category->id);
                }
            }
        }

        // Search by product name
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $products = $query->orderBy('name')->paginate(12)->withQueryString();

        $parentCategories = Category::whereNull('parent_id')
            ->with('children')
            ->orderBy('name')
            ->get();

        $activeCategory = $request->filled('kategori')
            ? Category::where('slug', $request->kategori)->first()
            : null;

        return view('product.index', compact('products', 'parentCategories', 'activeCategory'));
    }

    public function show(string $slug)
    {
        $product = Product::with(['category.parent'])
            ->where('slug', $slug)
            ->firstOrFail();

        return view('product.show', compact('product'));
    }
}
