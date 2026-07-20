@extends('layouts.public')
@section('title', 'Keranjang Belanja — CV Berkah')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-2xl font-bold text-slate-800 mb-6">Keranjang Belanja</h1>

    @if(empty($cartItems))
    <div class="flex flex-col items-center justify-center py-20 text-center">
        <svg class="w-20 h-20 text-slate-200 mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
        <h2 class="text-slate-500 font-semibold text-lg mb-2">Keranjang Anda kosong</h2>
        <p class="text-slate-400 text-sm mb-6">Mulai belanja dengan menelusuri katalog produk kami</p>
        <a href="{{ route('product.index') }}" class="bg-amber-500 hover:bg-amber-400 text-slate-900 font-bold px-6 py-3 rounded-xl transition">Lihat Produk</a>
    </div>
    @else
    <div class="space-y-3 mb-6">
        @foreach($cartItems as $item)
        <div class="bg-white border border-slate-100 rounded-xl p-4 flex items-center gap-4 shadow-sm">
            <img src="{{ $item['product']->image_url }}" alt="{{ $item['product']->name }}"
                 class="w-16 h-16 rounded-lg object-cover bg-slate-100 shrink-0">

            <div class="flex-1 min-w-0">
                <h3 class="font-semibold text-slate-800 text-sm truncate">{{ $item['product']->name }}</h3>
                <p class="text-amber-600 font-bold text-sm">Rp {{ number_format($item['product']->sell_price, 0, ',', '.') }}/{{ $item['product']->unit }}</p>
            </div>

            <form action="{{ route('cart.update') }}" method="POST" class="flex items-center gap-2">
                @csrf
                <input type="hidden" name="product_id" value="{{ $item['product']->id }}">
                <input type="number" name="qty" value="{{ $item['qty'] }}" min="0.01" max="{{ $item['product']->stock }}"
                       step="{{ $item['product']->unit === 'pcs' ? '1' : '0.1' }}"
                       class="w-20 text-center text-sm border border-slate-200 rounded-lg py-1.5 focus:outline-none focus:ring-2 focus:ring-amber-400">
                <button type="submit" class="text-xs text-slate-400 hover:text-slate-600 transition px-1">Update</button>
            </form>

            <div class="text-right min-w-[80px]">
                <p class="font-bold text-slate-800 text-sm">Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</p>
                <form action="{{ route('cart.remove', $item['product']->id) }}" method="POST" class="inline">
                    @csrf @method('DELETE')
                    <button type="submit" class="text-xs text-red-400 hover:text-red-600 transition mt-1">Hapus</button>
                </form>
            </div>
        </div>
        @endforeach
    </div>

    <div class="bg-white border border-slate-100 rounded-xl p-5 shadow-sm">
        <div class="flex justify-between items-center mb-4">
            <span class="text-slate-600 font-medium">Total Estimasi:</span>
            <span class="text-2xl font-bold text-amber-600">Rp {{ number_format($total, 0, ',', '.') }}</span>
        </div>
        <p class="text-xs text-slate-400 mb-5">* Harga final dikonfirmasi oleh admin via WhatsApp</p>
        <a href="{{ route('checkout.index') }}"
           class="block w-full text-center bg-amber-500 hover:bg-amber-400 text-slate-900 font-bold py-3.5 rounded-xl transition duration-200">
            Lanjut ke Checkout
        </a>
        <a href="{{ route('product.index') }}" class="block w-full text-center text-slate-500 hover:text-slate-700 text-sm mt-3 transition">← Lanjut Belanja</a>
    </div>
    @endif
</div>
@endsection
