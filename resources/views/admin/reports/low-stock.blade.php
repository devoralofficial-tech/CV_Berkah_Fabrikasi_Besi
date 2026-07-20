@extends('layouts.admin')
@section('title', 'Stok Menipis')
@section('breadcrumb')
<span class="font-medium text-slate-700">Laporan Stok Menipis</span>
@endsection

@section('content')
<div class="flex flex-wrap items-center justify-between gap-3 mb-5">
    <h1 class="text-xl font-bold text-slate-800">Laporan Stok Menipis</h1>
    <a href="{{ route('admin.reports.low-stock', ['export' => 1]) }}"
       class="flex items-center gap-1.5 border border-slate-200 hover:bg-slate-50 text-slate-700 px-4 py-2 rounded-lg text-sm font-medium transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        Export Excel
    </a>
</div>

<div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-x-auto">
    <table class="w-full text-sm">
        <thead class="bg-slate-50/80">
            <tr class="border-b border-slate-200/60 text-[10px] font-bold uppercase tracking-widest text-slate-500">
                <th class="text-left py-3.5 px-4">Produk</th>
                <th class="text-left py-3.5 px-4">Kategori</th>
                <th class="text-right py-3.5 px-4">Stok Saat Ini</th>
                <th class="text-right py-3.5 px-4">Threshold</th>
                <th class="text-center py-3.5 px-4">Status</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100/60">
            @forelse($products as $product)
            <tr class="hover:bg-slate-50">
                <td class="py-3 px-4 font-medium text-slate-700">{{ $product->name }}</td>
                <td class="py-3 px-4 text-xs text-slate-500">{{ $product->category?->parent?->name }} › {{ $product->category?->name }}</td>
                <td class="py-3 px-4 text-right font-bold {{ $product->stock <= 0 ? 'text-red-500' : 'text-amber-600' }}">
                    {{ number_format($product->stock, 2, ',', '.') }} {{ $product->unit }}
                </td>
                <td class="py-3 px-4 text-right text-slate-400">{{ number_format($product->low_stock_threshold, 2, ',', '.') }}</td>
                <td class="py-3 px-4 text-center"><x-status-badge :status="$product->stock_status" /></td>
            </tr>
            @empty
            <tr><td colspan="5" class="py-16 text-center text-emerald-500 font-medium">Semua stok aman!</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
