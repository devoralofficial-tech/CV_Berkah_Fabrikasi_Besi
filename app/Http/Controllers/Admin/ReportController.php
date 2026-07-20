<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InventoryLog;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Exports\SalesReportExport;
use App\Exports\StockReportExport;
use App\Exports\ProfitLossExport;
use App\Exports\LowStockExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    private function dateRange(Request $request): array
    {
        $from = $request->from ?? now()->startOfMonth()->format('Y-m-d');
        $to   = $request->to   ?? now()->format('Y-m-d');
        return [$from, $to];
    }

    public function sales(Request $request)
    {
        [$from, $to] = $this->dateRange($request);

        $sales = Sale::with(['items.product'])
            ->where('status', 'completed')
            ->whereDate('created_at', '>=', $from)
            ->whereDate('created_at', '<=', $to)
            ->orderByDesc('created_at')
            ->get();

        $totalRevenue     = $sales->sum('total');
        $totalTransactions = $sales->count();
        $onlineCount      = $sales->where('source', 'online')->count();
        $walkinCount      = $sales->where('source', 'walk-in')->count();

        // Top products
        $topProducts = SaleItem::with('product')
            ->whereHas('sale', fn($q) => $q->where('status', 'completed')
                ->whereDate('created_at', '>=', $from)
                ->whereDate('created_at', '<=', $to))
            ->selectRaw('product_id, SUM(qty) as total_qty, SUM(subtotal) as total_revenue')
            ->groupBy('product_id')
            ->orderByDesc('total_qty')
            ->limit(10)
            ->get();

        if ($request->has('export')) {
            return Excel::download(new SalesReportExport($from, $to), "laporan-penjualan-{$from}-{$to}.xlsx");
        }

        return view('admin.reports.sales', compact(
            'sales', 'totalRevenue', 'totalTransactions',
            'onlineCount', 'walkinCount', 'topProducts', 'from', 'to'
        ));
    }

    public function stock(Request $request)
    {
        [$from, $to] = $this->dateRange($request);

        $query = InventoryLog::with(['product', 'creator'])
            ->whereDate('created_at', '>=', $from)
            ->whereDate('created_at', '<=', $to);

        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $logs = $query->orderByDesc('created_at')->paginate(50)->withQueryString();
        $products = Product::withTrashed()->orderBy('name')->get(['id', 'name']);

        if ($request->has('export')) {
            return Excel::download(new StockReportExport($from, $to, $request->product_id), "laporan-stok-{$from}-{$to}.xlsx");
        }

        return view('admin.reports.stock', compact('logs', 'products', 'from', 'to'));
    }

    public function profitLoss(Request $request)
    {
        [$from, $to] = $this->dateRange($request);

        $items = SaleItem::with(['product', 'sale'])
            ->whereHas('sale', fn($q) => $q->where('status', 'completed')
                ->whereDate('created_at', '>=', $from)
                ->whereDate('created_at', '<=', $to))
            ->get();

        $revenue    = $items->sum('subtotal');
        $cogs       = $items->sum(fn($i) => ($i->product?->cost_price ?? 0) * $i->qty);
        $grossProfit = $revenue - $cogs;

        if ($request->has('export')) {
            return Excel::download(new ProfitLossExport($from, $to), "laporan-laba-rugi-{$from}-{$to}.xlsx");
        }

        return view('admin.reports.profit-loss', compact('revenue', 'cogs', 'grossProfit', 'from', 'to', 'items'));
    }

    public function lowStock(Request $request)
    {
        $products = Product::with('category.parent')
            ->whereRaw('stock <= low_stock_threshold')
            ->orderBy('stock')
            ->get();

        if ($request->has('export')) {
            return Excel::download(new LowStockExport(), 'laporan-stok-menipis.xlsx');
        }

        return view('admin.reports.low-stock', compact('products'));
    }
}
