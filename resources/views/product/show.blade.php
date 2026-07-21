@extends('layouts.public')
@section('title', $product->name . ' — CV Berkah')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 pt-4 pb-40 md:py-8">
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
        <div class="-mx-4 sm:mx-0 sm:rounded-2xl bg-slate-100 overflow-hidden aspect-square sm:aspect-auto">
            <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-full h-full object-cover sm:object-contain">
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
            <div class="mt-8 border-t border-slate-100 pt-6">
                <h3 class="text-lg font-bold text-slate-800 mb-3" style="font-family: 'Sora', sans-serif;">Deskripsi Produk</h3>
                <div class="prose prose-sm sm:prose-base text-slate-600 max-w-none break-words whitespace-pre-wrap">{{ $product->description }}</div>
            </div>
            @endif

            @if($product->stock_status !== 'habis')
            <form action="{{ route('cart.add') }}" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                
                {{-- Sticky Bottom Bar (Mobile) / Static Form (Desktop) --}}
                <div class="fixed bottom-[68px] md:bottom-0 left-0 right-0 bg-white border-t border-slate-200 p-4 shadow-[0_-4px_20px_rgba(0,0,0,0.08)] md:static md:border-none md:shadow-none md:bg-transparent md:p-0 z-40">
                    <div class="flex flex-row md:flex-col gap-4">
                        {{-- Qty Selector --}}
                        <div class="flex-none">
                            <label class="hidden md:block text-sm font-medium text-slate-700 mb-2">Jumlah ({{ $product->unit }})</label>
                            <div class="flex items-center gap-2">
                                <button type="button" @click="decrease()" class="w-10 h-10 flex items-center justify-center bg-slate-100 hover:bg-slate-200 rounded-lg font-bold text-slate-700 transition">−</button>
                                <input type="number" name="qty" x-model="qty"
                                       min="{{ $product->unit === 'pcs' ? '1' : '0.1' }}"
                                       max="{{ $product->stock }}"
                                       step="{{ $product->unit === 'pcs' ? '1' : '0.1' }}"
                                       class="w-16 md:w-24 text-center border border-slate-200 rounded-lg py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-400">
                                <button type="button" @click="increase()" class="w-10 h-10 flex items-center justify-center bg-slate-100 hover:bg-slate-200 rounded-lg font-bold text-slate-700 transition">+</button>
                            </div>
                            @error('qty')<p class="text-red-500 text-xs mt-1 absolute md:static">{{ $message }}</p>@enderror
                        </div>

                        {{-- Subtotal (Desktop only) --}}
                        <div class="hidden md:block bg-slate-50 rounded-xl p-3 text-sm">
                            <div class="flex justify-between items-center">
                                <span class="text-slate-500">Subtotal:</span>
                                <span class="font-bold text-slate-800 text-lg" x-text="'Rp ' + (qty * {{ $product->sell_price }}).toLocaleString('id-ID')"></span>
                            </div>
                        </div>

                        {{-- Submit Button --}}
                        <button type="submit"
                                class="flex-1 w-full bg-amber-500 hover:bg-amber-400 text-slate-900 font-bold py-2 md:py-3.5 rounded-xl transition duration-200 flex items-center justify-center gap-2 shadow-sm">
                            <svg class="w-5 h-5 hidden md:block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            <span class="md:hidden">Beli</span>
                            <span class="hidden md:inline">Tambah ke Keranjang</span>
                        </button>
                    </div>
                </div>
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
