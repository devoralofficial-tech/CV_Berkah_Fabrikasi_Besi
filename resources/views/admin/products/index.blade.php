@extends('layouts.admin')
@section('title', 'Produk')
@section('breadcrumb')
<a href="{{ route('admin.dashboard') }}" class="hover:text-slate-600">Dashboard</a> › <span class="font-medium text-slate-700">Produk</span>
@endsection

@section('content')
<div class="flex flex-wrap items-center justify-between gap-3 mb-5">
    <h1 class="text-xl font-bold text-slate-800">Daftar Produk</h1>
    <a href="{{ route('admin.products.create') }}" class="flex items-center gap-2 bg-emerald-500 hover:bg-emerald-600 text-white font-bold px-5 py-2.5 rounded-xl text-sm transition-all shadow-lg shadow-emerald-500/30 hover:-translate-y-0.5">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
        Tambah Produk
    </a>
</div>

{{-- Filter bar --}}
<div class="bg-white rounded-xl border border-slate-100 shadow-sm p-4 mb-5">
    <form action="{{ route('admin.products.index') }}" method="GET" class="flex flex-wrap gap-3">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama produk..." class="flex-1 min-w-[160px] border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500">
        <select name="category_id" class="border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500">
            <option value="">Semua Kategori</option>
            @foreach($categories as $parent)
            <optgroup label="{{ $parent->name }}">
                @foreach($parent->children as $child)
                <option value="{{ $child->id }}" {{ request('category_id') == $child->id ? 'selected' : '' }}>{{ $child->name }}</option>
                @endforeach
            </optgroup>
            @endforeach
        </select>
        <select name="status" class="border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500">
            <option value="">Semua Status</option>
            <option value="tersedia" {{ request('status') === 'tersedia' ? 'selected' : '' }}>Tersedia</option>
            <option value="menipis" {{ request('status') === 'menipis' ? 'selected' : '' }}>Menipis ({{ $lowStockCount }})</option>
            <option value="habis" {{ request('status') === 'habis' ? 'selected' : '' }}>Habis ({{ $outOfStockCount }})</option>
        </select>
        <button type="submit" class="bg-slate-800 hover:bg-slate-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">Cari</button>
        @if(request()->hasAny(['search', 'category_id', 'status', 'trashed']))
        <a href="{{ route('admin.products.index') }}" class="bg-slate-100 hover:bg-slate-200 text-slate-700 px-4 py-2 rounded-lg text-sm font-medium transition">Reset</a>
        @endif
    </form>
</div>

<div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-x-auto">
    <table class="w-full text-sm">
        <thead class="bg-slate-50/80">
            <tr class="border-b border-slate-200/60 text-[10px] font-bold uppercase tracking-widest text-slate-500">
                <th class="text-left py-4 px-6">Produk</th>
                <th class="text-left py-4 px-6">Kategori</th>
                <th class="text-right py-4 px-6">Harga Jual</th>
                <th class="text-right py-4 px-6">Stok</th>
                <th class="text-center py-4 px-6">Status</th>
                <th class="text-center py-4 px-6">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100/60">
            @forelse($products as $product)
            <tr class="{{ $product->trashed() ? 'opacity-50' : '' }} hover:bg-slate-50/80 transition-colors group">
                <td class="py-4 px-6">
                    <div class="flex items-center gap-3">
                        <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-10 h-10 rounded-xl object-cover border border-slate-200/60 shrink-0">
                        <div>
                            <p class="font-bold text-slate-800">{{ $product->name }}</p>
                            @if($product->trashed())
                            <span class="text-[10px] uppercase tracking-wider text-red-500 font-bold bg-red-50 px-2 py-0.5 rounded-full mt-1 inline-block">Nonaktif</span>
                            @endif
                        </div>
                    </div>
                </td>
                <td class="py-4 px-6 text-slate-500 text-xs font-medium">
                    {{ $product->category?->parent?->name ? $product->category->parent->name . ' › ' : '' }}{{ $product->category?->name }}
                </td>
                <td class="py-4 px-6 text-right font-bold text-slate-700">
                    Rp {{ number_format($product->sell_price, 0, ',', '.') }}
                </td>
                <td class="py-4 px-6 text-right font-bold text-slate-700">
                    {{ number_format($product->stock, 2, ',', '.') }} <span class="text-xs text-slate-400 font-medium ml-1">{{ $product->unit }}</span>
                </td>
                <td class="py-4 px-6 text-center">
                    <x-status-badge :status="$product->trashed() ? 'cancelled' : $product->stock_status" />
                </td>
                <td class="py-4 px-6">
                    <div class="flex items-center justify-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                        @if(!$product->trashed())
                        <a href="{{ route('admin.warehouse.stock-card', $product) }}" class="p-1.5 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Kartu Stok">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        </a>
                        <a href="{{ route('admin.products.edit', $product) }}" class="p-1.5 text-slate-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-colors" title="Edit">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </a>
                        <form action="{{ route('admin.products.destroy', $product) }}" method="POST" class="inline" onsubmit="return confirm('Nonaktifkan produk ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="p-1.5 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Nonaktifkan">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                            </button>
                        </form>
                        @else
                        <form action="{{ route('admin.products.restore', $product->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="text-xs text-emerald-600 hover:text-emerald-700 font-bold tracking-wide transition border border-emerald-200 bg-emerald-50 hover:bg-emerald-100 rounded-md px-3 py-1.5">Aktifkan</button>
                        </form>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="py-16 text-center">
                    <div class="flex flex-col items-center justify-center">
                        <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mb-3 text-slate-300">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                        </div>
                        <p class="text-sm font-semibold text-slate-500">Produk tidak ditemukan</p>
                        <p class="text-xs text-slate-400 mt-1">Coba sesuaikan filter pencarian atau tambah produk baru.</p>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($products->hasPages())
<div class="mt-5">{{ $products->links() }}</div>
@endif
@endsection
