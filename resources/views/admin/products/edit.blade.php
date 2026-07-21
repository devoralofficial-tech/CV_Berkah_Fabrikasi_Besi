@extends('layouts.admin')
@section('title', 'Edit Produk')
@section('breadcrumb')
<a href="{{ route('admin.products.index') }}" class="hover:text-slate-600">Produk</a> › <span class="font-medium text-slate-700">Edit</span>
@endsection

@section('content')
<div class="max-w-2xl">
    <h1 class="text-xl font-bold text-slate-800 mb-6">Edit Produk: {{ $product->name }}</h1>
    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-6">
        <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf @method('PUT')

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Nama Produk <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name', $product->name) }}" class="w-full border border-slate-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500 @error('name') border-red-400 @enderror">
                @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Kategori <span class="text-red-500">*</span></label>
                <select name="category_id" class="w-full border border-slate-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500 @error('category_id') border-red-400 @enderror">
                    @foreach($parentCategories as $parent)
                    <optgroup label="{{ $parent->name }}">
                        @foreach($parent->children as $child)
                        <option value="{{ $child->id }}" {{ old('category_id', $product->category_id) == $child->id ? 'selected' : '' }}>{{ $child->name }}</option>
                        @endforeach
                    </optgroup>
                    @endforeach
                </select>
                @error('category_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Satuan</label>
                    <select name="unit" class="w-full border border-slate-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500">
                        @foreach(['pcs' => 'pcs', 'kg' => 'kg', 'm' => 'meter'] as $val => $label)
                        <option value="{{ $val }}" {{ old('unit', $product->unit) === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Harga Jual (Rp)</label>
                    <input type="number" name="sell_price" value="{{ old('sell_price', $product->sell_price) }}" min="0" step="0.01" class="w-full border border-slate-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Harga Modal (Rp)</label>
                    <input type="number" name="cost_price" value="{{ old('cost_price', $product->cost_price) }}" min="0" step="0.01" class="w-full border border-slate-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Stok Minimum Alert</label>
                <input type="number" name="low_stock_threshold" value="{{ old('low_stock_threshold', $product->low_stock_threshold) }}" min="0" step="0.01" class="w-full border border-slate-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500">
                <p class="text-xs text-slate-400 mt-1">Stok saat ini: <strong>{{ $product->stock }} {{ $product->unit }}</strong> — untuk mengubah stok, gunakan menu Barang Masuk/Keluar</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-4 bg-amber-50 rounded-lg border border-amber-100">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-amber-700 mb-1">Tampilkan di Produk Unggulan?</label>
                    <select name="is_featured" class="w-full border border-amber-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                        <option value="0" {{ old('is_featured', $product->is_featured) == '0' ? 'selected' : '' }}>Tidak</option>
                        <option value="1" {{ old('is_featured', $product->is_featured) == '1' ? 'selected' : '' }}>Ya</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-amber-700 mb-1">Urutan Tampil (Opsional)</label>
                    <input type="number" name="featured_order" value="{{ old('featured_order', $product->featured_order) }}" class="w-full border border-amber-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Foto Produk</label>
                @if($product->image)
                <div class="mb-2">
                    <img src="{{ $product->image_url }}" alt="Foto saat ini" class="w-20 h-20 rounded-lg object-cover">
                    <p class="text-xs text-slate-400 mt-1">Foto saat ini. Unggah baru untuk mengganti.</p>
                </div>
                @endif
                <input type="file" name="image" accept="image/jpeg,image/jpg,image/png,image/webp"
                       class="w-full text-sm text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-amber-50 file:text-amber-700 hover:file:bg-amber-100">
                @error('image')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Deskripsi</label>
                <textarea name="description" rows="3" class="w-full border border-slate-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500 resize-none">{{ old('description', $product->description) }}</textarea>
            </div>

            <div class="flex items-center gap-3 p-4 mt-6 bg-emerald-50 rounded-xl border border-emerald-100">
                <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-6 py-2.5 rounded-lg text-sm transition-all shadow-sm flex-1 sm:flex-none text-center">Simpan Perubahan</button>
                <a href="{{ route('admin.products.index') }}" class="px-6 py-2.5 bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 hover:text-slate-800 font-bold rounded-lg text-sm transition-colors flex-1 sm:flex-none text-center">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
