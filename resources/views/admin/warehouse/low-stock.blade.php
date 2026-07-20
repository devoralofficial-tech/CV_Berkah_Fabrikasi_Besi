@extends('layouts.admin')
@section('title', 'Stok Menipis')
@section('breadcrumb')
<span class="font-medium text-slate-700">Peringatan Stok Menipis</span>
@endsection

@section('content')
<h1 class="text-xl font-bold text-slate-800 mb-5">⚠ Produk dengan Stok Menipis / Habis</h1>

<div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-x-auto">
    <table class="w-full text-sm">
        <thead class="bg-slate-50/80">
            <tr class="border-b border-slate-200/60 text-[10px] font-bold uppercase tracking-widest text-slate-500">
                <th class="text-left py-3.5 px-4">Produk</th>
                <th class="text-left py-3.5 px-4">Kategori</th>
                <th class="text-right py-3.5 px-4">Stok</th>
                <th class="text-right py-3.5 px-4">Threshold</th>
                <th class="text-center py-3.5 px-4">Status</th>
                <th class="text-center py-3.5 px-4">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100/60">
            @forelse($products as $product)
            <tr class="hover:bg-slate-50/80 transition-colors group">
                <td class="py-3 px-4 font-medium text-slate-700">{{ $product->name }}</td>
                <td class="py-3 px-4 text-xs text-slate-500">{{ $product->category?->parent?->name }} › {{ $product->category?->name }}</td>
                <td class="py-3 px-4 text-right font-bold {{ $product->stock <= 0 ? 'text-red-500' : 'text-amber-600' }}">
                    {{ number_format($product->stock, 2, ',', '.') }} {{ $product->unit }}
                </td>
                <td class="py-3 px-4 text-right text-slate-400">{{ number_format($product->low_stock_threshold, 2, ',', '.') }}</td>
                <td class="py-3 px-4 text-center">
                    <x-status-badge :status="$product->stock_status" />
                </td>
                <td class="py-3 px-4 text-center">
                    <a href="{{ route('admin.warehouse.stock-in') }}?product_id={{ $product->id }}" class="text-xs text-sky-600 hover:text-sky-700 font-medium transition">Tambah Stok →</a>
                </td>
            </tr>
            @empty
            <tr><td colspan="6" class="py-16 text-center text-emerald-500 font-medium">🎉 Semua stok dalam kondisi aman!</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
