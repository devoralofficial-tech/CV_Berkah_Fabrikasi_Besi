@extends('layouts.public')
@section('title', 'Keranjang Belanja — CV Berkah')
@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="cartPage({{ $total }})" @cart-total-updated.window="updateTotal($event.detail.total)">
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
        <div class="bg-white border border-slate-100 rounded-xl p-4 flex flex-col gap-4 shadow-sm"
             x-data="cartItem({{ $item['product']->id }}, {{ $item['qty'] }}, {{ $item['product']->stock }}, '{{ $item['product']->unit }}', {{ $item['product']->sell_price }})">
            
            <div class="flex items-start gap-4">
                <img src="{{ $item['product']->image_url }}" alt="{{ $item['product']->name }}"
                     class="w-16 h-16 rounded-lg object-cover bg-slate-100 shrink-0">

                <div class="flex-1 min-w-0">
                    <div class="flex justify-between items-start">
                        <h3 class="font-bold text-slate-800 text-sm leading-snug truncate pr-2">{{ $item['product']->name }}</h3>
                        <p class="font-black text-slate-800 text-[15px] shrink-0" x-text="'Rp ' + (qty * price).toLocaleString('id-ID')"></p>
                    </div>
                    <p class="text-amber-600 font-semibold text-xs mt-0.5">@ Rp {{ number_format($item['product']->sell_price, 0, ',', '.') }} / {{ $item['product']->unit }}</p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <div class="flex-1 flex items-center justify-between bg-slate-50 rounded-lg p-1 border border-slate-200">
                    <div class="flex items-center gap-1">
                        <button type="button" @click="adjustQty(-5)" class="w-8 h-8 flex items-center justify-center bg-white border border-slate-100 rounded shadow-sm text-slate-600 hover:text-amber-600 transition text-[11px] font-bold leading-none" :class="isUpdating ? 'opacity-50 cursor-not-allowed' : ''" :disabled="isUpdating">-5</button>
                        <template x-if="unit !== 'pcs'">
                            <button type="button" @click="adjustQty(-0.5)" class="w-8 h-8 flex items-center justify-center bg-white border border-slate-100 rounded shadow-sm text-slate-600 hover:text-amber-600 transition text-[11px] font-bold leading-none" :class="isUpdating ? 'opacity-50 cursor-not-allowed' : ''" :disabled="isUpdating">-.5</button>
                        </template>
                        <button type="button" @click="adjustQty(unit === 'pcs' ? -1 : -0.1)" class="w-8 h-8 flex items-center justify-center bg-white border border-slate-100 rounded shadow-sm text-slate-600 hover:text-amber-600 transition font-bold text-sm leading-none" :class="isUpdating ? 'opacity-50 cursor-not-allowed' : ''" :disabled="isUpdating">−</button>
                    </div>
                    
                    <input type="text" name="qty" x-model.number="qty" readonly class="w-12 text-center text-sm border-none bg-transparent focus:outline-none focus:ring-0 p-0 font-black text-slate-700 pointer-events-none mx-1">
                    
                    <div class="flex items-center gap-1">
                        <button type="button" @click="adjustQty(unit === 'pcs' ? 1 : 0.1)" class="w-8 h-8 flex items-center justify-center bg-white border border-slate-100 rounded shadow-sm text-slate-600 hover:text-amber-600 transition font-bold text-sm leading-none" :class="isUpdating ? 'opacity-50 cursor-not-allowed' : ''" :disabled="isUpdating">+</button>
                        <template x-if="unit !== 'pcs'">
                            <button type="button" @click="adjustQty(0.5)" class="w-8 h-8 flex items-center justify-center bg-white border border-slate-100 rounded shadow-sm text-slate-600 hover:text-amber-600 transition text-[11px] font-bold leading-none" :class="isUpdating ? 'opacity-50 cursor-not-allowed' : ''" :disabled="isUpdating">+.5</button>
                        </template>
                        <button type="button" @click="adjustQty(5)" class="w-8 h-8 flex items-center justify-center bg-white border border-slate-100 rounded shadow-sm text-slate-600 hover:text-amber-600 transition text-[11px] font-bold leading-none" :class="isUpdating ? 'opacity-50 cursor-not-allowed' : ''" :disabled="isUpdating">+5</button>
                    </div>
                </div>
                <form action="{{ route('cart.remove', $item['product']->id) }}" method="POST" class="inline shrink-0">
                    @csrf @method('DELETE')
                    <button type="submit" class="w-11 h-11 flex items-center justify-center bg-red-50 text-red-500 border border-red-100 rounded-lg hover:bg-red-500 hover:text-white transition-colors shadow-sm" title="Hapus Item">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                </form>
            </div>
        </div>
        @endforeach
    </div>

    <div class="bg-white border border-slate-100 rounded-xl p-5 shadow-sm">
        <div class="flex justify-between items-center mb-4">
            <span class="text-slate-600 font-medium">Total Estimasi:</span>
            <span class="text-2xl font-bold text-amber-600" x-text="'Rp ' + total.toLocaleString('id-ID')"></span>
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

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('cartPage', (initialTotal) => ({
        total: initialTotal,
        updateTotal(newTotal) {
            this.total = newTotal;
        }
    }));

    Alpine.data('cartItem', (id, initialQty, maxStock, unit, price) => ({
        id: id,
        qty: initialQty,
        maxStock: maxStock,
        unit: unit,
        price: price,
        isUpdating: false,
        adjustQty(amount) {
            if (this.isUpdating) return;
            let current = parseFloat(this.qty) || 0;
            let newQty = parseFloat((current + amount).toFixed(2));
            const min = this.unit === 'pcs' ? 1 : 0.1;
            
            if (newQty > this.maxStock) newQty = this.maxStock;
            if (newQty < min) newQty = min;
            
            if (newQty !== this.qty) this.updateServer(newQty);
        },
        async updateServer(newQty) {
            this.isUpdating = true;
            this.qty = newQty;
            try {
                let res = await fetch('/cart/update', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ product_id: this.id, qty: this.qty })
                });
                let data = await res.json();
                if (data.success) {
                    this.$dispatch('cart-total-updated', { total: data.total });
                }
            } catch (e) {
                console.error(e);
            }
            this.isUpdating = false;
        }
    }));
});
</script>
@endpush
@endsection
