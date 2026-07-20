@extends('layouts.admin')
@section('title', 'Tambah Produk')
@section('breadcrumb')
<a href="{{ route('admin.products.index') }}" class="hover:text-slate-600">Produk</a> › <span class="font-medium text-slate-700">Tambah</span>
@endsection

@section('content')
<div class="max-w-2xl">
    <h1 class="text-xl font-bold text-slate-800 mb-6">Tambah Produk</h1>
    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-6">
        <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4" x-data="{ loading: false }" @submit="loading = true">
            @csrf

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Nama Produk <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" required class="w-full border border-slate-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500/50 focus:border-sky-500 transition-colors @error('name') border-red-400 bg-red-50 @enderror">
                @error('name')<p class="text-red-500 text-xs font-medium mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-2 gap-5" x-data="categorySelector()">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Kategori Induk <span class="text-red-500">*</span></label>
                    <select @change="loadChildren($event.target.value)" required class="w-full border border-slate-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500/50 focus:border-sky-500 transition-colors">
                        <option value="">— Pilih Induk —</option>
                        @foreach($parentCategories as $parent)
                        <option value="{{ $parent->id }}">{{ $parent->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Sub-Kategori <span class="text-red-500">*</span></label>
                    <select name="category_id" x-model="selectedChild" required class="w-full border border-slate-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500/50 focus:border-sky-500 transition-colors @error('category_id') border-red-400 bg-red-50 @enderror">
                        <option value="">— Pilih Sub-Kategori —</option>
                        <template x-for="child in children" :key="child.id">
                            <option :value="child.id" x-text="child.name"></option>
                        </template>
                    </select>
                    @error('category_id')<p class="text-red-500 text-xs font-medium mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="grid grid-cols-3 gap-5">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Satuan <span class="text-red-500">*</span></label>
                    <select name="unit" required class="w-full border border-slate-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500/50 focus:border-sky-500 transition-colors">
                        @foreach(['pcs' => 'pcs', 'kg' => 'kg', 'm' => 'meter'] as $val => $label)
                        <option value="{{ $val }}" {{ old('unit', 'pcs') === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Harga Jual (Rp) <span class="text-red-500">*</span></label>
                    <input type="number" name="sell_price" value="{{ old('sell_price') }}" min="0" step="0.01" required class="w-full border border-slate-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500/50 focus:border-sky-500 transition-colors @error('sell_price') border-red-400 bg-red-50 @enderror">
                    @error('sell_price')<p class="text-red-500 text-xs font-medium mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Harga Modal (Rp)</label>
                    <input type="number" name="cost_price" value="{{ old('cost_price') }}" min="0" step="0.01" class="w-full border border-slate-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500/50 focus:border-sky-500 transition-colors">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Stok Minimum Alert <span class="text-red-500">*</span></label>
                    <input type="number" name="low_stock_threshold" value="{{ old('low_stock_threshold', 0) }}" min="0" step="0.01" required class="w-full border border-slate-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500/50 focus:border-sky-500 transition-colors">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Stok Awal</label>
                    <input type="number" name="initial_stock" value="{{ old('initial_stock', 0) }}" min="0" step="0.01" class="w-full border border-slate-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500/50 focus:border-sky-500 transition-colors">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Foto Produk</label>
                <input type="file" name="image" accept="image/jpeg,image/jpg,image/png,image/webp"
                       class="w-full text-sm text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-bold file:bg-amber-50 file:text-amber-600 hover:file:bg-amber-100 transition-colors">
                <p class="text-[11px] text-slate-400 mt-1.5">Maks. 2MB. Format: JPG, PNG, WebP. Akan dikompresi otomatis.</p>
                @error('image')<p class="text-red-500 text-xs font-medium mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Deskripsi</label>
                <textarea name="description" rows="3" class="w-full border border-slate-200 rounded-lg px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500/50 focus:border-sky-500 transition-colors resize-none">{{ old('description') }}</textarea>
            </div>

            <div class="flex items-center gap-3 pt-4 mt-4 border-t border-slate-100">
                <button type="submit" :disabled="loading" class="flex items-center justify-center gap-2 bg-emerald-500 hover:bg-emerald-600 text-white font-bold px-8 py-3 rounded-xl text-sm transition-all shadow-lg shadow-emerald-500/30 hover:-translate-y-0.5 disabled:opacity-70 disabled:cursor-not-allowed w-auto">
                    <svg x-show="loading" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    <span x-text="loading ? 'Menyimpan...' : 'Simpan Produk'"></span>
                </button>
                <a href="{{ route('admin.products.index') }}" class="px-6 py-3 border border-slate-200 text-slate-600 hover:bg-slate-50 hover:text-slate-800 font-bold rounded-xl text-sm transition-colors">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function categorySelector() {
    return {
        children: [],
        selectedChild: '{{ old('category_id') }}',
        async loadChildren(parentId) {
            if (!parentId) { this.children = []; return; }
            const res = await fetch(`{{ url('/admin/products/child-categories') }}/${parentId}`);
            this.children = await res.json();
        }
    }
}
</script>
@endpush
