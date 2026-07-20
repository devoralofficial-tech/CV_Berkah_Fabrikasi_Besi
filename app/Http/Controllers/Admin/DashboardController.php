<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Sale;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $totalProducts    = Product::count();
        $lowStockProducts = Product::whereRaw('stock <= low_stock_threshold AND stock > 0')->count();
        $outOfStock       = Product::where('stock', '<=', 0)->count();
        $pendingOrders    = Order::where('status', 'pending')->count();

        $todayRevenue = Sale::where('status', 'completed')
            ->whereDate('created_at', today())
            ->sum('total');

        $monthRevenue = Sale::where('status', 'completed')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total');

        $recentSales = Sale::with('creator')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        // Chart: last 30 days sales
        $chartData = Sale::where('status', 'completed')
            ->where('created_at', '>=', now()->subDays(29)->startOfDay())
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(total) as total'))
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        $chartLabels = [];
        $chartValues = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $chartLabels[] = now()->subDays($i)->format('d/m');
            $chartValues[] = $chartData->get($date)?->total ?? 0;
        }

        return view('admin.dashboard', compact(
            'totalProducts', 'lowStockProducts', 'outOfStock', 'pendingOrders',
            'todayRevenue', 'monthRevenue', 'recentSales', 'chartLabels', 'chartValues'
        ));
    }
}
