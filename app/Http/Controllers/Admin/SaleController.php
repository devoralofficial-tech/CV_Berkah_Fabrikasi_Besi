<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreSaleRequest;
use App\Models\Product;
use App\Models\Sale;
use App\Services\SalesService;
use Illuminate\Http\Request;

class SaleController extends Controller
{
    public function __construct(private SalesService $salesService) {}

    public function create()
    {
        $products = Product::where('stock', '>', 0)
            ->with('category')
            ->orderBy('name')
            ->get();

        return view('admin.sales.create', compact('products'));
    }

    public function store(StoreSaleRequest $request)
    {
        try {
            $sale = $this->salesService->createWalkIn(
                $request->items,
                $request->customer_name,
                $request->payment_method,
                (float) $request->amount_paid,
                auth()->id()
            );
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }

        return redirect()->route('admin.sales.show', $sale)
            ->with('success', 'Transaksi berhasil disimpan.');
    }

    public function index(Request $request)
    {
        $query = Sale::with(['creator', 'order'])->orderByDesc('created_at');

        if ($request->filled('source')) {
            $query->where('source', $request->source);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }
        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->to);
        }

        $sales = $query->paginate(20)->withQueryString();

        return view('admin.sales.index', compact('sales'));
    }

    public function show(Sale $sale)
    {
        $sale->load(['items.product', 'creator', 'order']);
        return view('admin.sales.show', compact('sale'));
    }

    public function void(Request $request, Sale $sale)
    {
        $request->validate(['password' => 'required|string']);
        
        if (!\Illuminate\Support\Facades\Hash::check($request->password, auth()->user()->password)) {
            return back()->with('error', 'Password salah. Aksi void dibatalkan.');
        }

        try {
            $this->salesService->voidSale($sale, auth()->id());
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()->route('admin.sales.show', $sale)
            ->with('success', 'Transaksi berhasil di-void. Stok telah dikembalikan.');
    }

    public function searchProducts(Request $request)
    {
        $products = Product::where('stock', '>', 0)
            ->where('name', 'like', '%' . $request->q . '%')
            ->with('category')
            ->limit(10)
            ->get(['id', 'name', 'unit', 'sell_price', 'stock', 'category_id']);

        return response()->json($products);
    }
}
