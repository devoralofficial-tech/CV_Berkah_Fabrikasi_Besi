@extends('layouts.admin')
@section('title', 'Laporan Stok')
@section('breadcrumb')
<span class="font-medium text-slate-700">Laporan Stok</span>
@endsection

@section('content')
<div class="flex flex-wrap items-center justify-between gap-3 mb-5">
    <h1 class="text-xl font-bold text-slate-800">Laporan Mutasi Stok</h1>
    <a href="{{ route('admin.reports.stock', array_merge(request()->all(), ['export' => 1])) }}"
       class="flex items-center gap-1.5 border border-slate-200 hover:bg-slate-50 text-slate-700 px-4 py-2 rounded-lg text-sm font-medium transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        Export Excel
    </a>
</div>

<div class="bg-white rounded-xl shadow-sm border border-slate-100 p-4 mb-5">
    <form action="{{ route('admin.reports.stock') }}" method="GET" class="flex flex-wrap gap-3">
        <input type="date" name="from" value="{{ $from }}" class="border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500">
        <input type="date" name="to" value="{{ $to }}" class="border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500">
        <select name="type" class="border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500">
            <option value="">Semua Tipe</option>
            <option value="in" {{ request('type') === 'in' ? 'selected' : '' }}>Masuk</option>
            <option value="out" {{ request('type') === 'out' ? 'selected' : '' }}>Keluar</option>
        </select>
        <select name="product_id" class="border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500">
            <option value="">Semua Produk</option>
            @foreach($products as $p)
            <option value="{{ $p->id }}" {{ request('product_id') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
            @endforeach
        </select>
        <button type="submit" class="bg-slate-800 hover:bg-slate-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">Filter</button>
    </form>
</div>

<div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-x-auto">
    <table class="w-full text-sm">
        <thead class="bg-slate-50/80">
            <tr class="border-b border-slate-200/60 text-[10px] font-bold uppercase tracking-widest text-slate-500">
                <th class="text-left py-3.5 px-4">Tanggal</th>
                <th class="text-left py-3.5 px-4">Produk</th>
                <th class="text-center py-3.5 px-4">Tipe</th>
                <th class="text-right py-3.5 px-4">Qty</th>
                <th class="text-left py-3.5 px-4">Sumber</th>
                <th class="text-left py-3.5 px-4">Catatan</th>
                <th class="text-left py-3.5 px-4">Oleh</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100/60">
            @forelse($logs as $log)
            <tr class="hover:bg-slate-50">
                <td class="py-2.5 px-4 text-xs text-slate-500 whitespace-nowrap">{{ $log->created_at->format('d/m/Y H:i') }}</td>
                <td class="py-2.5 px-4 font-medium text-slate-700">{{ $log->product?->name ?? '-' }}</td>
                <td class="py-2.5 px-4 text-center">
                    <span class="inline-flex px-2 py-0.5 text-xs rounded-full {{ $log->type === 'in' ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-700' }}">
                        {{ $log->type === 'in' ? '↑ Masuk' : '↓ Keluar' }}
                    </span>
                </td>
                <td class="py-2.5 px-4 text-right font-semibold {{ $log->type === 'in' ? 'text-emerald-600' : 'text-red-500' }}">{{ $log->qty }}</td>
                <td class="py-2.5 px-4 text-xs text-slate-500 capitalize">{{ $log->source }}</td>
                <td class="py-2.5 px-4 text-xs text-slate-400">{{ $log->note ?? '-' }}</td>
                <td class="py-2.5 px-4 text-xs text-slate-500">{{ $log->creator?->name ?? '-' }}</td>
            </tr>
            @empty
            <tr><td colspan="7" class="py-16 text-center text-slate-400">Tidak ada data</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@if($logs->hasPages())
<div class="mt-5">{{ $logs->links() }}</div>
@endif
@endsection
