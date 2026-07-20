@extends('layouts.admin')
@section('title', 'Dashboard')
@section('breadcrumb')
<span class="text-slate-400">Dashboard</span> › <span class="text-slate-700 font-medium">Ringkasan</span>
@endsection

@section('content')
{{-- Stat Cards --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    @foreach([
        ['label' => 'Total Produk', 'value' => $totalProducts, 'color' => 'slate', 'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4'],
        ['label' => 'Stok Menipis', 'value' => $lowStockProducts, 'color' => 'amber', 'icon' => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z'],
        ['label' => 'Stok Habis', 'value' => $outOfStock, 'color' => 'red', 'icon' => 'M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636'],
        ['label' => 'Pesanan Pending', 'value' => $pendingOrders, 'color' => 'blue', 'icon' => 'M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z'],
    ] as $stat)
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100/60 hover:shadow-md hover:border-slate-200 transition-all duration-300 group">
        <div class="flex items-center justify-between mb-4">
            <div class="w-10 h-10 rounded-xl bg-{{ $stat['color'] }}-50 flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                <svg class="w-5 h-5 text-{{ $stat['color'] }}-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $stat['icon'] }}"/></svg>
            </div>
            {{-- Tren Placeholder --}}
            <span class="text-[10px] font-bold text-slate-400 bg-slate-50 px-2 py-1 rounded-full">Bulan Ini</span>
        </div>
        <p class="text-3xl font-extrabold text-slate-800 tracking-tight">{{ $stat['value'] }}</p>
        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mt-1">{{ $stat['label'] }}</p>
    </div>
    @endforeach
</div>

{{-- Revenue cards --}}
<div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
    <div class="bg-gradient-to-br bg-sky-50 border border-sky-100 rounded-2xl p-6 text-slate-900 shadow-md relative overflow-hidden group">
        <div class="absolute right-0 top-0 opacity-10 transform translate-x-4 -translate-y-4 group-hover:scale-110 transition-transform duration-500">
            <svg class="w-32 h-32" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
        </div>
        <p class="text-xs font-bold uppercase tracking-widest opacity-80 mb-2">Pendapatan Hari Ini</p>
        <p class="text-3xl font-extrabold tracking-tight">Rp {{ number_format($todayRevenue, 0, ',', '.') }}</p>
    </div>
    <div class="bg-gradient-to-br from-slate-800 to-slate-900 rounded-2xl p-6 text-white shadow-md relative overflow-hidden group">
        <div class="absolute right-0 top-0 opacity-10 transform translate-x-4 -translate-y-4 group-hover:scale-110 transition-transform duration-500">
            <svg class="w-32 h-32" fill="currentColor" viewBox="0 0 24 24"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-2 10h-4v4h-2v-4H7v-2h4V7h2v4h4v2z"/></svg>
        </div>
        <p class="text-xs font-bold uppercase tracking-widest opacity-70 mb-2">Pendapatan Bulan Ini</p>
        <p class="text-3xl font-extrabold tracking-tight text-sky-400">Rp {{ number_format($monthRevenue, 0, ',', '.') }}</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Sales Chart --}}
    <div class="lg:col-span-2 bg-white rounded-2xl p-6 shadow-sm border border-slate-100/60">
        <h2 class="font-bold text-slate-800 mb-4 tracking-tight">Penjualan 30 Hari Terakhir</h2>
        <canvas id="salesChart" height="200"></canvas>
    </div>

    {{-- Recent Sales --}}
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100/60 flex flex-col">
        <div class="flex items-center justify-between mb-4">
            <h2 class="font-bold text-slate-800 tracking-tight">Transaksi Terbaru</h2>
            <a href="{{ route('admin.sales.index') }}" class="text-[10px] font-bold uppercase tracking-wider text-sky-600 hover:text-sky-700 bg-sky-50 hover:bg-sky-100 px-3 py-1.5 rounded-full transition-colors">Lihat Semua</a>
        </div>
        <div class="space-y-4 flex-1">
            @forelse($recentSales as $sale)
            <div class="flex items-center gap-3 group">
                <div class="w-10 h-10 rounded-xl {{ $sale->status === 'completed' ? 'bg-emerald-50 text-emerald-500' : 'bg-red-50 text-red-500' }} flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-bold text-slate-800 truncate group-hover:text-sky-600 transition-colors">{{ $sale->sale_number }}</p>
                    <p class="text-xs font-medium text-slate-400">{{ $sale->created_at->format('d M Y, H:i') }}</p>
                </div>
                <p class="text-sm font-extrabold text-slate-700">Rp {{ number_format($sale->total, 0, ',', '.') }}</p>
            </div>
            @empty
            <div class="flex flex-col items-center justify-center h-full text-center py-8">
                <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mb-3 text-slate-300">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <p class="text-sm font-semibold text-slate-500">Belum ada transaksi</p>
                <p class="text-xs text-slate-400 mt-1">Transaksi baru akan muncul di sini</p>
            </div>
            @endforelse
        </div>
    </div>
</div>

{{-- Quick actions --}}
<div class="mt-6 grid grid-cols-2 sm:grid-cols-4 gap-3">
    @foreach([
        ['label' => 'Transaksi Baru', 'href' => route('admin.sales.create'), 'icon' => 'M12 6v6m0 0v6m0-6h6m-6 0H6'],
        ['label' => 'Barang Masuk', 'href' => route('admin.warehouse.stock-in'), 'icon' => 'M7 16V4m0 0L3 8m4-4l4 4'],
        ['label' => 'Lihat Pesanan', 'href' => route('admin.orders.index'), 'icon' => 'M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z'],
        ['label' => 'Laporan Penjualan', 'href' => route('admin.reports.sales'), 'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z'],
    ] as $action)
    <a href="{{ $action['href'] }}"
       class="flex flex-col items-center justify-center gap-2 bg-white border border-slate-100 rounded-xl p-4 shadow-sm hover:border-sky-300 hover:bg-sky-50 transition duration-200 text-center group">
        <svg class="w-6 h-6 text-slate-400 group-hover:text-sky-600 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $action['icon'] }}"/></svg>
        <span class="text-xs font-medium text-slate-600 group-hover:text-sky-700 transition">{{ $action['label'] }}</span>
    </a>
    @endforeach
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
<script>
const ctx = document.getElementById('salesChart').getContext('2d');
new Chart(ctx, {
    type: 'line',
    data: {
        labels: @json($chartLabels),
        datasets: [{
            label: 'Penjualan (Rp)',
            data: @json($chartValues),
            borderColor: '#0ea5e9',
            backgroundColor: 'rgba(14, 165, 233, 0.1)',
            borderWidth: 2,
            fill: true,
            tension: 0.4,
            pointRadius: 3,
            pointBackgroundColor: '#0ea5e9',
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: { legend: { display: false } },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: v => 'Rp ' + v.toLocaleString('id-ID'),
                    font: { size: 10 }
                },
                grid: { color: '#f1f5f9' }
            },
            x: {
                ticks: { font: { size: 10 } },
                grid: { display: false }
            }
        }
    }
});
</script>
@endpush
