<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProductRequest;
use App\Http\Requests\Admin\UpdateProductRequest;
use App\Models\Category;
use App\Models\Product;
use App\Services\InventoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;

class ProductController extends Controller
{
    public function __construct(private InventoryService $inventoryService) {}

    public function index(Request $request)
    {
        $query = Product::with(['category.parent'])->withTrashed();

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('status')) {
            match ($request->status) {
                'habis'    => $query->where('stock', '<=', 0),
                'menipis'  => $query->whereRaw('stock <= low_stock_threshold AND stock > 0'),
                'tersedia' => $query->whereRaw('stock > low_stock_threshold'),
                default    => null,
            };
        }
        if ($request->filled('trashed')) {
            $query->onlyTrashed();
        } else {
            $query->whereNull('deleted_at');
        }

        $products = $query->orderBy('name')->paginate(20)->withQueryString();
        $categories = Category::whereNull('parent_id')->with('children')->orderBy('name')->get();

        $lowStockCount = Product::whereRaw('stock <= low_stock_threshold AND stock > 0')->count();
        $outOfStockCount = Product::where('stock', '<=', 0)->count();

        return view('admin.products.index', compact('products', 'categories', 'lowStockCount', 'outOfStockCount'));
    }

    public function create()
    {
        $parentCategories = Category::whereNull('parent_id')->with('children')->orderBy('name')->get();
        return view('admin.products.create', compact('parentCategories'));
    }

    public function store(StoreProductRequest $request)
    {
        $data = $request->validated();

        // Verify category is a child (leaf) category
        $category = Category::findOrFail($data['category_id']);
        if ($category->parent_id === null) {
            return back()->withErrors(['category_id' => 'Produk harus dikaitkan ke kategori anak, bukan kategori induk.'])->withInput();
        }

        // Generate slug
        $slug = Str::slug($data['name']);
        $originalSlug = $slug;
        $counter = 1;
        while (Product::withTrashed()->where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter++;
        }

        // Handle image upload
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $this->uploadImage($request->file('image'));
        }

        $product = Product::create([
            'category_id'         => $data['category_id'],
            'name'                => $data['name'],
            'slug'                => $slug,
            'image'               => $imagePath,
            'unit'                => $data['unit'],
            'sell_price'          => $data['sell_price'],
            'cost_price'          => $data['cost_price'] ?? null,
            'stock'               => 0,
            'low_stock_threshold' => $data['low_stock_threshold'],
            'description'         => $data['description'] ?? null,
            'is_featured'         => $data['is_featured'] ?? false,
            'featured_order'      => $data['featured_order'] ?? 0,
        ]);

        // Create initial stock log if initial_stock > 0
        $initialStock = (float) ($data['initial_stock'] ?? 0);
        if ($initialStock > 0) {
            $this->inventoryService->stockIn(
                $product,
                $initialStock,
                'initial',
                'Stok awal produk',
                auth()->id()
            );
        }

        return redirect()->route('admin.products.index')
            ->with('success', 'Produk berhasil ditambahkan.');
    }

    public function show(Product $product)
    {
        $product->load(['category.parent']);
        return view('admin.products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $parentCategories = Category::whereNull('parent_id')->with('children')->orderBy('name')->get();
        return view('admin.products.edit', compact('product', 'parentCategories'));
    }

    public function update(UpdateProductRequest $request, Product $product)
    {
        $data = $request->validated();

        $category = Category::findOrFail($data['category_id']);
        if ($category->parent_id === null) {
            return back()->withErrors(['category_id' => 'Produk harus dikaitkan ke kategori anak.'])->withInput();
        }

        $imagePath = $product->image;
        if ($request->hasFile('image')) {
            // Delete old image
            if ($product->image && file_exists(storage_path('app/public/' . $product->image))) {
                unlink(storage_path('app/public/' . $product->image));
            }
            $imagePath = $this->uploadImage($request->file('image'));
        }

        $product->update([
            'category_id'         => $data['category_id'],
            'name'                => $data['name'],
            'image'               => $imagePath,
            'unit'                => $data['unit'],
            'sell_price'          => $data['sell_price'],
            'cost_price'          => $data['cost_price'] ?? $product->cost_price,
            'low_stock_threshold' => $data['low_stock_threshold'],
            'description'         => $data['description'] ?? null,
            'is_featured'         => $data['is_featured'] ?? false,
            'featured_order'      => $data['featured_order'] ?? 0,
        ]);

        return redirect()->route('admin.products.index')
            ->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(Product $product)
    {
        $product->delete(); // Soft delete
        return redirect()->route('admin.products.index')
            ->with('success', 'Produk berhasil dinonaktifkan.');
    }

    public function restore(int $id)
    {
        $product = Product::withTrashed()->findOrFail($id);
        $product->restore();
        return redirect()->route('admin.products.index')
            ->with('success', 'Produk berhasil diaktifkan kembali.');
    }

    private function uploadImage($file): string
    {
        $filename = uniqid('product_') . '.webp';
        $path = 'products/' . $filename;
        $fullPath = storage_path('app/public/' . $path);
        
        $directory = dirname($fullPath);
        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }

        $image = Image::decode($file);
        $image->scaleDown(800, 800);
        $image->save($fullPath, 80);

        return $path;
    }

    public function getChildCategories(int $parentId)
    {
        $children = Category::where('parent_id', $parentId)->orderBy('name')->get(['id', 'name']);
        return response()->json($children);
    }
}
