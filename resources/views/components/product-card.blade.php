@props(['product'])

@php
$statusConfig = match($product->stock_status) {
    'tersedia' => ['label' => 'Tersedia', 'class' => 'bg-emerald-100 text-emerald-700 border-emerald-200'],
    'menipis'  => ['label' => 'Stok Menipis', 'class' => 'bg-amber-100 text-amber-700 border-amber-200'],
    'habis'    => ['label' => 'Habis', 'class' => 'bg-red-100 text-red-700 border-red-200'],
    default    => ['label' => 'Tidak Diketahui', 'class' => 'bg-slate-100 text-slate-600 border-slate-200'],
};
@endphp

<div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden group hover:shadow-md hover:-translate-y-0.5 transition duration-200">
    {{-- Product image --}}
    <a href="{{ route('product.show', $product->slug) }}" class="block overflow-hidden aspect-square bg-slate-100">
        <img src="{{ $product->image_url }}"
             alt="{{ $product->name }}"
             loading="lazy"
             class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
    </a>

    <div class="p-4">
        {{-- Category breadcrumb --}}
        @if($product->category)
        <p class="text-xs text-slate-400 mb-1">
            {{ $product->category->parent?->name ?? '' }}
            @if($product->category->parent) › @endif
            {{ $product->category->name }}
        </p>
        @endif

        {{-- Name --}}
        <h3 class="font-semibold text-slate-800 text-sm leading-tight mb-2 line-clamp-2">
            <a href="{{ route('product.show', $product->slug) }}" class="hover:text-amber-600 transition">
                {{ $product->name }}
            </a>
        </h3>

        {{-- Price & unit --}}
        <p class="text-amber-600 font-bold text-base mb-3">
            Rp {{ number_format($product->sell_price, 0, ',', '.') }}
            <span class="text-slate-400 font-normal text-xs">/ {{ $product->unit }}</span>
        </p>

        {{-- Status badge + action --}}
        <div class="flex items-center justify-between">
            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium border {{ $statusConfig['class'] }}">
                {{ $statusConfig['label'] }}
            </span>

            @if($product->stock_status !== 'habis')
            <a href="{{ route('product.show', $product->slug) }}"
               class="text-xs font-semibold text-amber-600 hover:text-amber-700 transition">
                Lihat →
            </a>
            @else
            <span class="text-xs text-slate-400">Tidak tersedia</span>
            @endif
        </div>
    </div>
</div>
