@extends('layouts.admin')
@section('title', 'Barang Masuk')
@section('breadcrumb')
<a href="{{ route('admin.dashboard') }}" class="hover:text-slate-600">Dashboard</a> › <span class="font-medium text-slate-700">Barang Masuk</span>
@endsection

@section('content')
<div class="max-w-lg">
    <h1 class="text-xl font-bold text-slate-800 mb-6">Catat Barang Masuk</h1>
    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-6">
        <form action="{{ route('admin.warehouse.stock-in.store') }}" method="POST" class="space-y-4" x-data="{ unit: 'pcs' }">
            @csrf
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Produk <span class="text-red-500">*</span></label>
                <select name="product_id" @change="unit = $event.target.options[$event.target.selectedIndex].dataset.unit || 'pcs'" class="w-full border border-slate-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500 @error('product_id') border-red-400 @enderror">
                    <option value="" data-unit="pcs">— Pilih Produk —</option>
                    @foreach($products as $product)
                    <option value="{{ $product->id }}" data-unit="{{ $product->unit }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>
                        {{ $product->name }} (Stok: {{ $product->stock }} {{ $product->unit }})
                    </option>
                    @endforeach
                </select>
                @error('product_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Jumlah <span class="text-red-500">*</span></label>
                    <input type="number" name="qty" value="{{ old('qty') }}" :min="unit === 'pcs' ? '1' : '0.1'" :step="unit === 'pcs' ? '1' : '0.1'" class="w-full border border-slate-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500 @error('qty') border-red-400 @enderror">
                    @error('qty')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Harga Modal Baru (Rp)</label>
                    <input type="number" name="cost_price" value="{{ old('cost_price') }}" min="0" step="0.01" class="w-full border border-slate-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500">
                    <p class="text-xs text-slate-400 mt-0.5">Opsional — perbarui harga modal</p>
                </div>
            </div>
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Supplier / Sumber</label>
                <input type="text" name="supplier" value="{{ old('supplier') }}" class="w-full border border-slate-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500" placeholder="Nama supplier (opsional)">
            </div>
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Catatan</label>
                <textarea name="note" rows="2" class="w-full border border-slate-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500 resize-none">{{ old('note') }}</textarea>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="bg-sky-600 hover:bg-sky-700 text-white font-bold px-6 py-2.5 rounded-lg text-sm transition-all shadow-sm">Catat Masuk</button>
                <a href="{{ route('admin.products.index') }}" class="px-6 py-2.5 border border-slate-200 text-slate-600 hover:bg-slate-50 hover:text-slate-800 font-bold rounded-lg text-sm transition-colors">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
