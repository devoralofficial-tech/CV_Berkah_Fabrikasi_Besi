@extends('layouts.public')
@section('title', $product->name . ' — CV Berkah')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-2 text-sm text-slate-400 mb-6">
        <a href="{{ route('product.index') }}" class="hover:text-amber-600 transition">Produk</a>
        <span>›</span>
        @if($product->category?->parent)
        <a href="{{ route('product.index', ['kategori' => $product->category->parent->slug]) }}" class="hover:text-amber-600 transition">{{ $product->category->parent->name }}</a>
        <span>›</span>
        @endif
        @if($product->category)
        <a href="{{ route('product.index', ['kategori' => $product->category->slug]) }}" class="hover:text-amber-600 transition">{{ $product->category->name }}</a>
        <span>›</span>
        @endif
        <span class="text-slate-600 font-medium">{{ $product->name }}</span>
    </nav>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 lg:gap-12">
        {{-- Image --}}
        <div class="aspect-square bg-slate-100 rounded-2xl overflow-hidden">
            <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
        </div>

        {{-- Details --}}
        <div x-data="productDetail({{ $product->stock }}, '{{ $product->unit }}')">
            <x-status-badge :status="$product->stock_status" />
            <h1 class="text-2xl sm:text-3xl font-bold text-slate-800 mt-3 mb-1" style="font-family: 'Sora', sans-serif;">{{ $product->name }}</h1>

            @if($product->category)
            <p class="text-slate-400 text-sm mb-4">
                {{ $product->category->parent?->name }} › {{ $product->category->name }}
            </p>
            @endif

            <div class="text-3xl font-bold text-amber-600 mb-2">
                Rp {{ number_format($product->sell_price, 0, ',', '.') }}
                <span class="text-slate-400 font-normal text-sm">/ {{ $product->unit }}</span>
            </div>

            @if($product->description)
            <p class="text-slate-500 text-sm leading-relaxed mb-6 border-t border-slate-100 pt-4">{{ $product->description }}</p>
            @endif

            @if($product->stock_status !== 'habis')
            <form action="{{ route('cart.add') }}" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Jumlah ({{ $product->unit }})</label>
                    <div class="flex items-center gap-2">
                        <button type="button" @click="decrease()" class="w-10 h-10 flex items-center justify-center bg-slate-100 hover:bg-slate-200 rounded-lg font-bold text-slate-700 transition">−</button>
                        <input type="number" name="qty" x-model="qty"
                               min="{{ $product->unit === 'pcs' ? '1' : '0.1' }}"
                               max="{{ $product->stock }}"
                               step="{{ $product->unit === 'pcs' ? '1' : '0.1' }}"
                               class="w-24 text-center border border-slate-200 rounded-lg py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-400">
                        <button type="button" @click="increase()" class="w-10 h-10 flex items-center justify-center bg-slate-100 hover:bg-slate-200 rounded-lg font-bold text-slate-700 transition">+</button>
                    </div>
                    @error('qty')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="bg-slate-50 rounded-xl p-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-slate-500">Subtotal:</span>
                        <span class="font-bold text-slate-800" x-text="'Rp ' + (qty * {{ $product->sell_price }}).toLocaleString('id-ID')"></span>
                    </div>
                </div>

                <button type="submit"
                        class="w-full bg-amber-500 hover:bg-amber-400 text-slate-900 font-bold py-3.5 rounded-xl transition duration-200 flex items-center justify-center gap-2 shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    Tambah ke Keranjang
                </button>
            </form>
            @else
            <div class="bg-red-50 border border-red-200 rounded-xl p-4 text-center">
                <p class="text-red-600 font-medium text-sm">Stok habis — hubungi kami untuk info restok</p>
            </div>
            @endif

            @if(session('success'))
            <div class="mt-3 text-center text-emerald-600 text-sm font-medium">{{ session('success') }}</div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
function productDetail(maxStock, unit) {
    return {
        qty: unit === 'pcs' ? 1 : 1,
        increase() {
            const step = unit === 'pcs' ? 1 : 0.1;
            this.qty = Math.min(parseFloat((this.qty + step).toFixed(2)), maxStock);
        },
        decrease() {
            const step = unit === 'pcs' ? 1 : 0.1;
            const min = unit === 'pcs' ? 1 : 0.1;
            this.qty = Math.max(parseFloat((this.qty - step).toFixed(2)), min);
        }
    }
}
</script>
@endpush
@endsection
