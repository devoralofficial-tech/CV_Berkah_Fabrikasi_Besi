@extends('layouts.admin')
@section('title', 'Laporan Penjualan')
@section('breadcrumb')
<span class="font-medium text-slate-700">Laporan Penjualan</span>
@endsection

@section('content')
<div class="flex flex-wrap items-center justify-between gap-3 mb-5">
    <h1 class="text-xl font-bold text-slate-800">Laporan Penjualan</h1>
    <a href="{{ route('admin.reports.sales', array_merge(request()->all(), ['export' => 1])) }}"
       class="flex items-center gap-1.5 border border-slate-200 hover:bg-slate-50 text-slate-700 px-4 py-2 rounded-lg text-sm font-medium transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        Export Excel
    </a>
</div>

<div class="bg-white rounded-xl shadow-sm border border-slate-100 p-4 mb-5">
    <form action="{{ route('admin.reports.sales') }}" method="GET" class="flex flex-wrap gap-3">
        <div class="flex items-center gap-2">
            <label class="text-sm text-slate-600">Dari</label>
            <input type="date" name="from" value="{{ $from }}" class="border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500">
        </div>
        <div class="flex items-center gap-2">
            <label class="text-sm text-slate-600">Sampai</label>
            <input type="date" name="to" value="{{ $to }}" class="border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500">
        </div>
        <button type="submit" class="bg-slate-800 hover:bg-slate-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">Filter</button>
    </form>
</div>

{{-- Summary --}}
<div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
    @foreach([
        ['label' => 'Total Transaksi', 'value' => $totalTransactions],
        ['label' => 'Total Pendapatan', 'value' => 'Rp ' . number_format($totalRevenue, 0, ',', '.')],
        ['label' => 'Online', 'value' => $onlineCount],
        ['label' => 'Walk-in', 'value' => $walkinCount],
    ] as $stat)
    <div class="bg-white rounded-xl p-4 shadow-sm border border-slate-100 text-center">
        <p class="text-xs text-slate-400 uppercase tracking-wider mb-1">{{ $stat['label'] }}</p>
        <p class="text-xl font-bold text-slate-800">{{ $stat['value'] }}</p>
    </div>
    @endforeach
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    {{-- Top Products --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-5">
        <h3 class="font-semibold text-slate-700 mb-3">Produk Terlaris</h3>
        <div class="space-y-2">
            @foreach($topProducts->take(5) as $tp)
            <div class="flex items-center justify-between text-sm">
                <span class="text-slate-600 truncate">{{ $tp->product?->name ?? '-' }}</span>
                <div class="text-right ml-4">
                    <p class="font-semibold text-slate-800">{{ number_format($tp->total_qty, 2, ',', '.') }} {{ $tp->product?->unit }}</p>
                    <p class="text-xs text-slate-400">Rp {{ number_format($tp->total_revenue, 0, ',', '.') }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Sales List --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-5">
        <h3 class="font-semibold text-slate-700 mb-3">Transaksi Selesai ({{ $totalTransactions }})</h3>
        <div class="space-y-2 max-h-56 overflow-y-auto">
            @foreach($sales->take(15) as $sale)
            <div class="flex justify-between items-center text-sm">
                <div>
                    <p class="font-medium text-slate-700">{{ $sale->sale_number }}</p>
                    <p class="text-xs text-slate-400">{{ $sale->created_at->format('d/m H:i') }}</p>
                </div>
                <p class="font-bold text-slate-800">Rp {{ number_format($sale->total, 0, ',', '.') }}</p>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
