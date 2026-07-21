@extends('layouts.public')
@section('title', 'Katalog Produk — CV Berkah')

@section('content')
<div class="bg-slate-50 py-12 border-b border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold text-slate-800" style="font-family: 'Sora', sans-serif;">Katalog Produk</h1>
        <p class="text-slate-500 mt-2">Temukan berbagai material besi dan baja berkualitas untuk proyek Anda.</p>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12" x-data="{ mobileFiltersOpen: false }">
    <div class="flex flex-col lg:flex-row gap-8">
        
        {{-- Mobile filter button --}}
        <div class="lg:hidden">
            <button @click="mobileFiltersOpen = true" class="flex items-center gap-2 bg-white border border-slate-200 text-slate-700 px-4 py-2 rounded-lg text-sm font-medium shadow-sm w-full justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                Filter Kategori
            </button>
        </div>

        {{-- Mobile Backdrop --}}
        <div x-show="mobileFiltersOpen" x-transition.opacity class="fixed inset-0 z-[60] bg-slate-900/50 backdrop-blur-sm lg:hidden" @click="mobileFiltersOpen = false" style="display: none;"></div>

        {{-- Sidebar / Bottom Sheet Filters --}}
        <aside class="fixed inset-x-0 bottom-0 z-[70] bg-white rounded-t-2xl shadow-xl max-h-[85vh] overflow-y-auto lg:static lg:bg-transparent lg:rounded-none lg:shadow-none lg:overflow-visible lg:max-h-none lg:z-auto w-full lg:w-64 shrink-0 transition-transform duration-300 lg:translate-y-0"
               :class="mobileFiltersOpen ? 'translate-y-0' : 'translate-y-full lg:translate-y-0'">
            <div class="bg-white lg:rounded-xl lg:shadow-sm lg:border lg:border-slate-100 p-5 lg:sticky lg:top-24">
                <div class="flex justify-between items-center lg:hidden mb-4">
                    <h3 class="font-bold text-slate-800">Filter</h3>
                    <button @click="mobileFiltersOpen = false" class="bg-slate-100 text-slate-500 hover:text-slate-700 p-2 rounded-full">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                
                <h3 class="font-semibold text-slate-800 mb-4 uppercase tracking-wider text-sm">Kategori</h3>
                
                <form action="{{ route('product.index') }}" method="GET" id="filter-form">
                    <input type="hidden" name="search" value="{{ request('search') }}">
                    
                    <ul class="space-y-2">
                        <li>
                            <label class="flex items-center gap-2 text-sm cursor-pointer hover:text-amber-600 transition">
                                <input type="radio" name="kategori" value="" onchange="this.form.submit()" class="text-amber-500 focus:ring-amber-500" {{ !request('kategori') ? 'checked' : '' }}>
                                <span class="{{ !request('kategori') ? 'font-semibold text-amber-600' : 'text-slate-600' }}">Semua Kategori</span>
                            </label>
                        </li>
                        @foreach($parentCategories as $parent)
                        <li class="pt-2">
                            <label class="flex items-center gap-2 text-sm cursor-pointer hover:text-amber-600 transition">
                                <input type="radio" name="kategori" value="{{ $parent->slug }}" onchange="this.form.submit()" class="text-amber-500 focus:ring-amber-500" {{ request('kategori') === $parent->slug ? 'checked' : '' }}>
                                <span class="{{ request('kategori') === $parent->slug ? 'font-semibold text-amber-600' : 'text-slate-700 font-medium' }}">{{ $parent->name }}</span>
                            </label>
                            
                            @if($parent->children->count() > 0)
                            <ul class="ml-5 mt-2 space-y-2 border-l border-slate-100 pl-3">
                                @foreach($parent->children as $child)
                                <li>
                                    <label class="flex items-center gap-2 text-sm cursor-pointer hover:text-amber-600 transition">
                                        <input type="radio" name="kategori" value="{{ $child->slug }}" onchange="this.form.submit()" class="text-amber-500 focus:ring-amber-500" {{ request('kategori') === $child->slug ? 'checked' : '' }}>
                                        <span class="{{ request('kategori') === $child->slug ? 'font-semibold text-amber-600' : 'text-slate-500' }}">{{ $child->name }}</span>
                                    </label>
                                </li>
                                @endforeach
                            </ul>
                            @endif
                        </li>
                        @endforeach
                    </ul>
                </form>
            </div>
        </aside>

        {{-- Product Grid --}}
        <main class="flex-1">
            {{-- Search Bar --}}
            <div class="mb-6 flex gap-2">
                <form action="{{ route('product.index') }}" method="GET" class="flex-1 flex gap-2">
                    <input type="hidden" name="kategori" value="{{ request('kategori') }}">
                    <div class="relative flex-1">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama produk..." class="w-full pl-10 pr-4 py-2.5 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-400 shadow-sm">
                    </div>
                    <button type="submit" class="bg-slate-900 hover:bg-slate-800 text-white px-6 py-2.5 rounded-lg text-sm font-semibold transition shadow-sm">Cari</button>
                </form>
            </div>

            @if($products->isEmpty())
                <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-12 text-center">
                    <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-800">Tidak ada produk ditemukan</h3>
                    <p class="text-slate-500 mt-1">Coba gunakan kata kunci lain atau hapus filter kategori.</p>
                    <a href="{{ route('product.index') }}" class="inline-block mt-4 text-amber-600 font-semibold hover:text-amber-700">Reset Pencarian</a>
                </div>
            @else
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-3 xl:grid-cols-4 gap-4 lg:gap-6">
                    @foreach($products as $product)
                        <x-product-card :product="$product" />
                    @endforeach
                </div>
                
                <div class="mt-8">
                    {{ $products->links() }}
                </div>
            @endif
        </main>
    </div>
</div>
@endsection
