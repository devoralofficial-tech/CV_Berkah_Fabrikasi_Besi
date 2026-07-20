@extends('layouts.admin')
@section('title', 'Kartu Stok')
@section('breadcrumb')
<a href="{{ route('admin.products.index') }}" class="hover:text-slate-600">Produk</a> › <span class="font-medium text-slate-700">Kartu Stok</span>
@endsection

@section('content')
<div class="mb-6 flex flex-wrap items-center justify-between gap-3">
    <div>
        <h1 class="text-xl font-bold text-slate-800">Kartu Stok: {{ $product->name }}</h1>
        <p class="text-sm text-slate-500 mt-1">Stok saat ini: <strong>{{ number_format($product->stock, 2, ',', '.') }} {{ $product->unit }}</strong></p>
    </div>
    <form action="{{ route('admin.warehouse.stock-card', $product) }}" method="GET" class="flex gap-2">
        <input type="date" name="from" value="{{ request('from') }}" class="border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500">
        <input type="date" name="to" value="{{ request('to') }}" class="border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500">
        <button type="submit" class="bg-slate-800 hover:bg-slate-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">Filter</button>
    </form>
</div>

<div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-x-auto">
    <table class="w-full text-sm">
        <thead class="bg-slate-50/80">
            <tr class="border-b border-slate-200/60 text-[10px] font-bold uppercase tracking-widest text-slate-500">
                <th class="text-left py-3.5 px-4">Tanggal</th>
                <th class="text-center py-3.5 px-4">Tipe</th>
                <th class="text-right py-3.5 px-4">Qty</th>
                <th class="text-left py-3.5 px-4">Sumber</th>
                <th class="text-left py-3.5 px-4">Catatan</th>
                <th class="text-right py-3.5 px-4">Saldo</th>
                <th class="text-left py-3.5 px-4">Oleh</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100/60">
            @forelse($logsWithBalance as $log)
            <tr class="hover:bg-slate-50/80 transition-colors group">
                <td class="py-2.5 px-4 text-slate-600 whitespace-nowrap">{{ $log->created_at->format('d/m/Y H:i') }}</td>
                <td class="py-2.5 px-4 text-center">
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium {{ $log->type === 'in' ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-700' }}">
                        {{ $log->type === 'in' ? '↑ Masuk' : '↓ Keluar' }}
                    </span>
                </td>
                <td class="py-2.5 px-4 text-right font-semibold {{ $log->type === 'in' ? 'text-emerald-600' : 'text-red-500' }}">
                    {{ $log->type === 'in' ? '+' : '-' }}{{ number_format($log->qty, 2, ',', '.') }}
                </td>
                <td class="py-2.5 px-4 text-slate-500 text-xs capitalize">{{ $log->source }}</td>
                <td class="py-2.5 px-4 text-slate-500 text-xs">{{ $log->note ?? '-' }}</td>
                <td class="py-2.5 px-4 text-right font-bold text-slate-800">{{ number_format($log->running_balance, 2, ',', '.') }}</td>
                <td class="py-2.5 px-4 text-slate-500 text-xs">{{ $log->creator?->name ?? '-' }}</td>
            </tr>
            @empty
            <tr><td colspan="7" class="py-16 text-center text-slate-400">Belum ada mutasi stok</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
