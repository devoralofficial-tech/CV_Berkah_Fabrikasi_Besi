@extends('layouts.admin')
@section('title', 'POS Kasir')
@section('breadcrumb')
<span class="font-medium text-slate-700">Point of Sale (Kasir)</span>
@endsection

@section('content')
@php
    $posProducts = $products->map(function($p) {
        return [
            'id' => $p->id,
            'name' => $p->name,
            'unit' => $p->unit,
            'price' => $p->sell_price,
            'stock' => $p->stock,
            'category' => $p->category?->name
        ];
    })->values()->toArray();
@endphp
<div class="-m-6 h-[calc(100vh-73px)] flex flex-col bg-slate-50"
     x-data="posApp()"
     x-init='initProducts(@json($posProducts))'>

    {{-- Tab Navigation for Mobile --}}
    <div class="flex lg:hidden bg-white border-b border-slate-200 shrink-0">
        <button @click="activeTab = 'products'" :class="activeTab === 'products' ? 'text-sky-600 border-b-2 border-sky-600' : 'text-slate-500 hover:bg-slate-50'" class="flex-1 py-3 text-sm font-bold text-center transition-colors">
            Pilih Produk
        </button>
        <button @click="activeTab = 'cart'" :class="activeTab === 'cart' ? 'text-sky-600 border-b-2 border-sky-600' : 'text-slate-500 hover:bg-slate-50'" class="flex-1 py-3 text-sm font-bold text-center transition-colors flex items-center justify-center gap-2">
            Keranjang
            <span x-show="items.length > 0" x-text="items.length" class="bg-red-500 text-white text-[10px] px-1.5 py-0.5 rounded-full min-w-[20px]" style="display: none;"></span>
        </button>
    </div>

    <div class="flex-1 flex flex-col lg:flex-row overflow-hidden relative">
        
        {{-- Left Panel: Products --}}
        <div class="flex-1 flex-col min-w-0" :class="activeTab === 'products' ? 'flex' : 'hidden lg:flex'">
            {{-- Search Bar --}}
            <div class="bg-white px-6 py-4 border-b border-slate-200 shadow-sm z-10 flex items-center gap-4">
                <div class="relative flex-1">
                    <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input type="text" x-model="search" placeholder="Cari nama produk..." 
                           class="w-full pl-12 pr-4 py-3 bg-slate-100 border-transparent rounded-xl text-sm focus:bg-white focus:border-sky-500 focus:ring-2 focus:ring-amber-200 transition-all font-medium text-slate-700">
                </div>
            </div>

            {{-- Product Grid --}}
            <div class="flex-1 overflow-y-auto p-6 scroll-smooth">
                <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-4">
                    <template x-for="p in filteredProducts" :key="p.id">
                        <div @click="addItem(p)"
                             class="bg-white border border-slate-200 rounded-2xl p-4 cursor-pointer hover:border-sky-400 hover:shadow-lg hover:shadow-sky-100 transition-all duration-300 relative group flex flex-col h-full">
                            
                            {{-- Stock Badge --}}
                            <div class="absolute top-3 right-3 bg-slate-100 text-slate-600 text-[10px] font-bold px-2 py-1 rounded-md" 
                                 :class="p.stock <= 5 ? 'bg-red-50 text-red-600 border border-red-200' : ''"
                                 x-text="p.stock + ' ' + p.unit"></div>
                            
                            <div class="w-12 h-12 bg-sky-50 rounded-xl flex items-center justify-center text-sky-500 mb-4 group-hover:scale-110 transition-transform">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                            </div>
                            
                            <h3 class="font-bold text-slate-800 text-sm mb-1 line-clamp-2" x-text="p.name"></h3>
                            <p class="text-xs text-slate-400 mb-3" x-text="p.category"></p>
                            
                            <div class="mt-auto">
                                <p class="font-black text-sky-600 text-base" x-text="'Rp ' + p.price.toLocaleString('id-ID') + ' / ' + p.unit"></p>
                            </div>
                        </div>
                    </template>
                </div>
                
                {{-- Empty State --}}
                <div x-show="filteredProducts.length === 0" class="flex flex-col items-center justify-center h-full text-slate-400">
                    <svg class="w-16 h-16 mb-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <p class="font-medium text-slate-500">Produk tidak ditemukan</p>
                    <p class="text-sm">Coba kata kunci pencarian yang lain.</p>
                </div>
            </div>
        </div>

        {{-- Right Panel: Cart / Receipt --}}
        <div class="w-full lg:w-[400px] xl:w-[450px] bg-white border-t lg:border-t-0 lg:border-l border-slate-200 shadow-2xl z-20 flex-col" :class="activeTab === 'cart' ? 'flex absolute inset-0 lg:static' : 'hidden lg:flex'">
            {{-- Receipt Header --}}
            <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50">
                <h2 class="text-lg font-bold text-slate-800" style="font-family: 'Sora', sans-serif;">Keranjang Kasir</h2>
                <p class="text-xs text-slate-500 mt-1" x-text="new Date().toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' })"></p>
            </div>

            {{-- Cart Items --}}
            <div class="flex-1 overflow-y-auto p-2">
                <div x-show="items.length === 0" class="flex flex-col items-center justify-center h-full text-slate-400 py-12">
                    <div class="w-24 h-24 bg-slate-50 rounded-full flex items-center justify-center mb-4 border-2 border-dashed border-slate-200">
                        <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                    </div>
                    <p class="font-medium text-slate-500 text-sm">Belum ada item</p>
                    <p class="text-xs text-slate-400">Pilih produk dari grid di sebelah kiri</p>
                </div>

                <div class="space-y-1">
                    <template x-for="(item, i) in items" :key="item.id">
                        <div class="flex flex-col p-4 bg-white rounded-xl border border-slate-100 hover:border-slate-200 transition">
                            <div class="flex justify-between items-start mb-2">
                                <div class="pr-3">
                                    <h4 class="font-bold text-slate-800 text-sm leading-snug" x-text="item.name"></h4>
                                    <p class="text-xs text-sky-600 font-semibold mt-0.5" x-text="'@ Rp ' + item.price.toLocaleString('id-ID') + ' / ' + item.unit"></p>
                                </div>
                                <button @click="removeItem(i)" class="text-slate-300 hover:text-red-500 transition-colors p-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>
                            <div class="flex items-center justify-between mt-2">
                                <div class="flex items-center bg-slate-100 rounded-lg p-1 border border-slate-200">
                                    <button @click="decreaseQty(i)" class="w-7 h-7 flex items-center justify-center bg-white rounded shadow-sm text-slate-600 hover:text-sky-600 transition">−</button>
                                    <input type="number" x-model="item.qty" @input="validateQty(i)"
                                           class="w-12 text-center text-sm bg-transparent border-none focus:ring-0 font-bold text-slate-700 p-0">
                                    <span class="text-xs text-slate-500 font-semibold mr-1" x-text="item.unit"></span>
                                    <button @click="increaseQty(i)" class="w-7 h-7 flex items-center justify-center bg-white rounded shadow-sm text-slate-600 hover:text-sky-600 transition">+</button>
                                </div>
                                <div class="text-right">
                                    <p class="font-black text-slate-800 text-[15px]" x-text="'Rp ' + (item.price * item.qty).toLocaleString('id-ID')"></p>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            {{-- Checkout Panel --}}
            <form @submit.prevent="submitSale" class="bg-slate-50 border-t border-slate-200 p-6 shadow-[0_-10px_40px_rgba(0,0,0,0.05)] relative z-30">
                {{-- Payment Method --}}
                <div class="flex gap-2 mb-4 bg-slate-200/50 p-1 rounded-xl">
                    <label class="flex-1 text-center py-2 text-sm font-bold rounded-lg cursor-pointer transition-all" 
                           :class="paymentMethod === 'cash' ? 'bg-white shadow-sm text-slate-800' : 'text-slate-500 hover:text-slate-700'">
                        <input type="radio" x-model="paymentMethod" value="cash" class="sr-only"> Tunai
                    </label>
                    <label class="flex-1 text-center py-2 text-sm font-bold rounded-lg cursor-pointer transition-all" 
                           :class="paymentMethod === 'transfer' ? 'bg-white shadow-sm text-slate-800' : 'text-slate-500 hover:text-slate-700'">
                        <input type="radio" x-model="paymentMethod" value="transfer" class="sr-only"> Transfer
                    </label>
                </div>

                <div class="mb-4">
                    <input type="text" x-model="customerName" placeholder="Nama Pelanggan (opsional)" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500">
                </div>

                {{-- Cash Amount Shortcuts --}}
                <div x-show="paymentMethod === 'cash'" x-transition class="mb-4 space-y-3">
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 font-bold">Rp</span>
                        <input type="number" x-model="amountPaid" min="0" step="1000" placeholder="0" class="w-full pl-10 pr-4 py-3 bg-white border border-slate-200 rounded-xl text-lg font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-sky-500 shadow-inner">
                    </div>
                    <div class="flex justify-between items-center bg-emerald-50 border border-emerald-100 rounded-xl px-4 py-3" x-show="amountPaid >= total && total > 0">
                        <span class="text-emerald-700 text-sm font-semibold">Kembalian</span>
                        <span class="font-black text-emerald-600 text-lg" x-text="'Rp ' + (amountPaid - total).toLocaleString('id-ID')"></span>
                    </div>

                    <div class="flex justify-between items-center bg-red-50 border border-red-100 rounded-xl px-4 py-3" x-show="amountPaid !== null && amountPaid !== '' && amountPaid < total">
                        <span class="text-red-700 text-sm font-semibold">Uang Kurang</span>
                        <span class="font-black text-red-600 text-lg" x-text="'Rp ' + (total - amountPaid).toLocaleString('id-ID')"></span>
                    </div>
                </div>

                <div class="border-t border-slate-200 border-dashed pt-4 mb-4">
                    <div class="flex justify-between items-end">
                        <span class="text-slate-500 font-medium">Total Tagihan</span>
                        <span class="font-black text-slate-900 text-3xl tracking-tight" style="font-family: 'Sora', sans-serif;" x-text="'Rp ' + total.toLocaleString('id-ID')"></span>
                    </div>
                </div>

                <button type="submit"
                        :disabled="items.length === 0 || (paymentMethod === 'cash' && (amountPaid === null || amountPaid === '' || amountPaid < total))"
                        class="w-full bg-emerald-500 hover:bg-emerald-600 disabled:bg-slate-200 disabled:text-slate-400 disabled:cursor-not-allowed text-white font-bold py-4 rounded-xl shadow-lg shadow-emerald-500/30 transition-all flex items-center justify-center gap-2 text-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    Bayar Sekarang
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function posApp() {
    return {
        activeTab: 'products',
        products: [],
        search: '',
        items: [],
        customerName: '',
        paymentMethod: 'cash',
        amountPaid: null,
        get filteredProducts() {
            let filtered = this.products.filter(p => p.stock > 0);
            if (this.search) {
                const q = this.search.toLowerCase();
                filtered = filtered.filter(p => p.name.toLowerCase().includes(q) || p.category?.toLowerCase().includes(q));
            }
            return filtered;
        },
        get total() {
            return this.items.reduce((s, i) => s + (i.price * parseFloat(i.qty || 0)), 0);
        },
        initProducts(prods) { this.products = prods; },
        addItem(p) {
            const existing = this.items.find(i => i.id === p.id);
            if (existing) {
                existing.qty = Math.min(parseFloat(existing.qty) + 1, p.stock);
            } else {
                this.items.push({...p, qty: p.unit === 'pcs' ? 1 : 1});
            }
        },
        removeItem(i) { 
            this.items.splice(i, 1); 
        },
        increaseQty(i) {
            const item = this.items[i];
            const step = item.unit === 'pcs' ? 1 : 0.1;
            item.qty = Math.min(parseFloat((parseFloat(item.qty) + step).toFixed(2)), item.stock);
        },
        decreaseQty(i) {
            const item = this.items[i];
            const step = item.unit === 'pcs' ? 1 : 0.1;
            const min = item.unit === 'pcs' ? 1 : 0.1;
            item.qty = Math.max(parseFloat((parseFloat(item.qty) - step).toFixed(2)), min);
        },
        validateQty(i) {
            const item = this.items[i];
            item.qty = Math.min(Math.max(parseFloat(item.qty) || 0.1, 0.01), item.stock);
        },
        submitSale() {
            if (this.items.length === 0) return;
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route('admin.sales.store') }}';
            const csrf = document.createElement('input');
            csrf.type = 'hidden'; csrf.name = '_token'; csrf.value = '{{ csrf_token() }}';
            form.appendChild(csrf);
            
            const cn = document.createElement('input'); cn.type='hidden'; cn.name='customer_name'; cn.value=this.customerName; form.appendChild(cn);
            const pm = document.createElement('input'); pm.type='hidden'; pm.name='payment_method'; pm.value=this.paymentMethod; form.appendChild(pm);
            const ap = document.createElement('input'); ap.type='hidden'; ap.name='amount_paid'; ap.value=this.paymentMethod==='cash'?this.amountPaid:0; form.appendChild(ap);
            
            this.items.forEach((item, i) => {
                ['product_id','qty'].forEach(k => {
                    const el = document.createElement('input'); el.type='hidden';
                    el.name=`items[${i}][${k}]`; el.value = k==='product_id'?item.id:item.qty; form.appendChild(el);
                });
            });
            document.body.appendChild(form); form.submit();
        }
    }
}
</script>
@endpush
