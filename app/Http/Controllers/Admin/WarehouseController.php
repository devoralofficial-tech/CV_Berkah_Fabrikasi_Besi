<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreStockInRequest;
use App\Http\Requests\Admin\StoreStockOutRequest;
use App\Http\Requests\Admin\StoreOpnameRequest;
use App\Models\Category;
use App\Models\InventoryLog;
use App\Models\Product;
use App\Models\StockOpname;
use App\Services\InventoryService;
use Illuminate\Http\Request;

class WarehouseController extends Controller
{
    public function __construct(private InventoryService $inventoryService) {}

    // ——— Barang Masuk ———
    public function stockInCreate()
    {
        $products = Product::orderBy('name')->get();
        return view('admin.warehouse.stock-in', compact('products'));
    }

    public function stockInStore(StoreStockInRequest $request)
    {
        $product = Product::findOrFail($request->product_id);
        $note = $request->note;
        if ($request->filled('supplier')) {
            $note = "Sumber: {$request->supplier}" . ($note ? " | {$note}" : '');
        }

        $this->inventoryService->stockIn(
            $product,
            (float) $request->qty,
            'purchase',
            $note,
            auth()->id(),
            $request->filled('cost_price') ? (float) $request->cost_price : null
        );

        return redirect()->route('admin.warehouse.stock-in')
            ->with('success', "Barang masuk berhasil dicatat. Stok {$product->name} bertambah {$request->qty} {$product->unit}.");
    }

    // ——— Barang Keluar ———
    public function stockOutCreate()
    {
        $products = Product::where('stock', '>', 0)->orderBy('name')->get();
        return view('admin.warehouse.stock-out', compact('products'));
    }

    public function stockOutStore(StoreStockOutRequest $request)
    {
        $product = Product::findOrFail($request->product_id);
        $note = "Alasan: {$request->reason}" . ($request->note ? " | {$request->note}" : '');

        try {
            $this->inventoryService->stockOut(
                $product,
                (float) $request->qty,
                'manual',
                $note,
                auth()->id()
            );
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }

        return redirect()->route('admin.warehouse.stock-out')
            ->with('success', "Barang keluar berhasil dicatat.");
    }

    // ——— Stock Opname ———
    public function opnameCreate(Request $request)
    {
        $query = Product::with('category');
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        $products = $query->orderBy('name')->get();
        $categories = Category::whereNull('parent_id')->with('children')->orderBy('name')->get();

        return view('admin.warehouse.opname-create', compact('products', 'categories'));
    }

    public function opnameStore(StoreOpnameRequest $request)
    {
        $hasDiscrepancy = false;
        foreach ($request->items as $item) {
            $product = Product::find($item['product_id']);
            if ($product && (float)$product->stock != (float)$item['physical_stock']) {
                $hasDiscrepancy = true;
                break;
            }
        }

        if ($hasDiscrepancy) {
            $request->validate(['password' => 'required|string']);
            if (!\Illuminate\Support\Facades\Hash::check($request->password, auth()->user()->password)) {
                return back()->with('error', 'Terdapat selisih stok! Password salah, Stock Opname dibatalkan.')->withInput();
            }
        }

        try {
            $this->inventoryService->processOpname(
                $request->items,
                $request->opname_date,
                $request->note,
                auth()->id()
            );
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }

        return redirect()->route('admin.warehouse.opname-index')
            ->with('success', 'Stock opname berhasil disimpan.');
    }

    public function opnameIndex()
    {
        $opnames = StockOpname::with('creator')
            ->orderByDesc('opname_date')
            ->paginate(20);

        return view('admin.warehouse.opname-index', compact('opnames'));
    }

    public function opnameShow(StockOpname $opname)
    {
        $opname->load(['items.product', 'creator']);
        return view('admin.warehouse.opname-show', compact('opname'));
    }

    // ——— Kartu Stok ———
    public function stockCard(Request $request, Product $product)
    {
        $query = InventoryLog::where('product_id', $product->id)->with('creator');

        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->to);
        }

        $logs = $query->orderBy('created_at')->get();

        // Calculate running balance
        $runningBalance = 0;
        $logsWithBalance = $logs->map(function ($log) use (&$runningBalance) {
            if ($log->type === 'in') {
                $runningBalance += $log->qty;
            } else {
                $runningBalance -= $log->qty;
            }
            $log->running_balance = $runningBalance;
            return $log;
        });

        return view('admin.warehouse.stock-card', compact('product', 'logsWithBalance'));
    }

    // ——— Low Stock Alert List ———
    public function lowStock()
    {
        $products = Product::with('category.parent')
            ->whereRaw('stock <= low_stock_threshold')
            ->orderBy('stock')
            ->get();

        return view('admin.warehouse.low-stock', compact('products'));
    }
}
