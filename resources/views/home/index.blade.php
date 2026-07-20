@extends('layouts.public')
@section('title', 'CV Berkah — Penjualan Besi & Logam Terpercaya')
@section('meta_description', 'CV Berkah menyediakan berbagai jenis besi siku, besi beton, plat baja, dan pipa stainless berkualitas. Pesan mudah via WhatsApp.')

@section('content')

{{-- HERO SECTION --}}
<section class="relative bg-slate-900 overflow-hidden min-h-[520px] flex items-center">
    {{-- Industrial grid pattern background --}}
    <div class="absolute inset-0 opacity-10" style="background-image: linear-gradient(rgba(251,191,36,0.3) 1px, transparent 1px), linear-gradient(90deg, rgba(251,191,36,0.3) 1px, transparent 1px); background-size: 40px 40px;"></div>
    {{-- Gradient overlay --}}
    <div class="absolute inset-0 bg-gradient-to-r from-slate-900 via-slate-900/95 to-slate-800/60"></div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 lg:py-28">
        <div class="max-w-2xl">
            <div class="inline-flex items-center gap-2 bg-amber-500/20 border border-amber-500/30 text-amber-400 text-xs font-semibold px-3 py-1.5 rounded-full mb-6">
                <span class="w-1.5 h-1.5 bg-amber-400 rounded-full animate-pulse"></span>
                Stok Tersedia — Order Sekarang
            </div>
            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-white leading-tight mb-6" style="font-family: 'Sora', sans-serif;">
                Material Besi &<br>
                <span class="text-amber-400">Logam Berkualitas</span>
            </h1>
            <p class="text-slate-300 text-lg leading-relaxed mb-8 max-w-lg">
                Supplier besi siku, beton, plat baja, dan pipa stainless untuk kebutuhan konstruksi, industri, dan bengkel. Harga bersaing, stok lengkap.
            </p>
            <div class="flex flex-wrap gap-4">
                <a href="{{ route('product.index') }}"
                   class="inline-flex items-center gap-2 bg-amber-500 hover:bg-amber-400 text-slate-900 font-bold px-7 py-3.5 rounded-xl transition duration-200 shadow-lg shadow-amber-500/20">
                    Lihat Katalog
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
                @php $setting = \App\Models\Setting::getSetting(); @endphp
                @if($setting->wa_number)
                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $setting->wa_number) }}" target="_blank"
                   class="inline-flex items-center gap-2 bg-white/10 hover:bg-white/20 border border-white/20 text-white font-semibold px-7 py-3.5 rounded-xl transition duration-200">
                    <svg class="w-4 h-4 text-emerald-400" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                    WhatsApp Kami
                </a>
                @endif
            </div>
        </div>
    </div>
</section>

{{-- CATEGORY HIGHLIGHTS --}}
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-10">
            <h2 class="text-2xl sm:text-3xl font-bold text-slate-800 mb-2">Kategori Produk</h2>
            <p class="text-slate-500">Temukan produk sesuai kebutuhan Anda</p>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-{{ min(count($parentCategories), 4) }} gap-4">
            @forelse($parentCategories as $category)
            <a href="{{ route('product.index', ['kategori' => $category->slug]) }}"
               class="group flex flex-col items-center p-6 bg-slate-50 hover:bg-amber-50 border border-slate-100 hover:border-amber-200 rounded-xl transition duration-200 text-center">
                <div class="w-14 h-14 bg-slate-200 group-hover:bg-amber-100 rounded-xl flex items-center justify-center mb-4 transition duration-200">
                    <svg class="w-7 h-7 text-slate-500 group-hover:text-amber-600 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                </div>
                <h3 class="font-semibold text-slate-700 group-hover:text-amber-700 text-sm transition">{{ $category->name }}</h3>
                <p class="text-xs text-slate-400 mt-1">{{ $category->children_count ?? 0 }} sub-kategori</p>
            </a>
            @empty
            <p class="col-span-full text-center text-slate-400">Belum ada kategori</p>
            @endforelse
        </div>
    </div>
</section>

{{-- LATEST PRODUCTS --}}
<section class="py-16 bg-white border-t border-slate-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-end justify-between mb-10">
            <div>
                <h2 class="text-2xl sm:text-3xl font-bold text-slate-800 mb-2">Produk Terbaru</h2>
                <p class="text-slate-500">Lihat koleksi material terbaru yang baru saja ditambahkan ke katalog kami.</p>
            </div>
            <a href="{{ route('product.index') }}" class="hidden sm:inline-flex items-center gap-2 text-amber-600 hover:text-amber-700 font-semibold transition">
                Lihat Semua <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
            </a>
        </div>

        @if(isset($latestProducts) && $latestProducts->isNotEmpty())
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($latestProducts as $product)
                <x-product-card :product="$product" />
            @endforeach
        </div>
        @else
        <div class="text-center py-10 bg-slate-50 rounded-xl border border-slate-100">
            <p class="text-slate-500">Belum ada produk yang ditambahkan.</p>
        </div>
        @endif
        
        <div class="mt-8 text-center sm:hidden">
            <a href="{{ route('product.index') }}" class="inline-flex items-center gap-2 text-amber-600 font-semibold">
                Lihat Semua Produk <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
            </a>
        </div>
    </div>
</section>

{{-- WHY US --}}
<section class="py-16 bg-slate-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-10">
            <h2 class="text-2xl sm:text-3xl font-bold text-slate-800 mb-2">Kenapa Pilih CV Berkah?</h2>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
            @foreach([
                ['icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z', 'title' => 'Kualitas Terjamin', 'desc' => 'Material besi dan logam pilihan dengan standar kualitas industri.'],
                ['icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'title' => 'Harga Bersaing', 'desc' => 'Dapatkan harga terbaik langsung dari sumber tanpa perantara berlebihan.'],
                ['icon' => 'M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z', 'title' => 'Order via WhatsApp', 'desc' => 'Pesan mudah dan cepat langsung via WhatsApp, tanpa proses panjang.'],
            ] as $item)
            <div class="bg-white p-6 rounded-xl border border-slate-100 shadow-sm">
                <div class="w-12 h-12 bg-amber-50 rounded-xl flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] }}"/></svg>
                </div>
                <h3 class="font-bold text-slate-800 mb-2">{{ $item['title'] }}</h3>
                <p class="text-slate-500 text-sm leading-relaxed">{{ $item['desc'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- CTA --}}
<section class="bg-amber-500 py-14">
    <div class="max-w-3xl mx-auto px-4 text-center">
        <h2 class="text-2xl sm:text-3xl font-bold text-slate-900 mb-4">Siap Memesan?</h2>
        <p class="text-slate-800 mb-8">Lihat katalog lengkap kami dan masukkan produk ke keranjang. Checkout mudah, diteruskan ke WhatsApp admin.</p>
        <a href="{{ route('product.index') }}"
           class="inline-flex items-center gap-2 bg-slate-900 hover:bg-slate-800 text-white font-bold px-8 py-3.5 rounded-xl transition duration-200 shadow-lg">
            Lihat Semua Produk
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </a>
    </div>
</section>

@endsection
