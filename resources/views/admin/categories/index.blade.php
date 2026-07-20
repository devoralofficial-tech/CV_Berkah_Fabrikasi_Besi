@extends('layouts.admin')
@section('title', 'Kategori')
@section('breadcrumb')
<a href="{{ route('admin.dashboard') }}" class="hover:text-slate-600">Dashboard</a> › <span class="font-medium text-slate-700">Kategori</span>
@endsection

@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-xl font-bold text-slate-800">Manajemen Kategori</h1>
    <a href="{{ route('admin.categories.create') }}" class="flex items-center gap-2 bg-sky-600 hover:bg-sky-700 text-white font-semibold px-4 py-2 rounded-lg text-sm transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
        Tambah Kategori
    </a>
</div>

<div class="space-y-4">
    @forelse($parentCategories as $parent)
    <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="flex items-center justify-between px-5 py-3.5 border-b border-slate-50">
            <div>
                <h3 class="font-semibold text-slate-800">{{ $parent->name }}</h3>
                <p class="text-xs text-slate-400">{{ $parent->children->count() }} sub-kategori</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('admin.categories.edit', $parent) }}" class="text-xs text-slate-500 hover:text-amber-600 transition px-2 py-1 rounded hover:bg-amber-50">Edit</a>
                <form action="{{ route('admin.categories.destroy', $parent) }}" method="POST" onsubmit="return confirm('Hapus kategori {{ $parent->name }}?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="text-xs text-red-400 hover:text-red-600 transition px-2 py-1 rounded hover:bg-red-50">Hapus</button>
                </form>
            </div>
        </div>
        @if($parent->children->count())
        <div class="px-5 py-3 flex flex-wrap gap-2">
            @foreach($parent->children as $child)
            <div class="flex items-center gap-1 bg-slate-50 border border-slate-100 rounded-full px-3 py-1 text-xs">
                <span class="text-slate-600 font-medium">{{ $child->name }}</span>
                <span class="text-slate-400">({{ $child->products->count() }} produk)</span>
                <a href="{{ route('admin.categories.edit', $child) }}" class="text-slate-5000 hover:text-amber-700 ml-1 transition">✎</a>
                <form action="{{ route('admin.categories.destroy', $child) }}" method="POST" class="inline" onsubmit="return confirm('Hapus sub-kategori?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="text-red-400 hover:text-red-600 ml-0.5 transition">×</button>
                </form>
            </div>
            @endforeach
        </div>
        @else
        <p class="px-5 py-3 text-xs text-slate-400 italic">Belum ada sub-kategori</p>
        @endif
    </div>
    @empty
    <div class="text-center py-16 text-slate-400">Belum ada kategori. <a href="{{ route('admin.categories.create') }}" class="text-amber-600 hover:underline">Tambah sekarang</a></div>
    @endforelse
</div>
@endsection
