@extends('layouts.admin')
@section('title', 'Riwayat Transaksi')
@section('breadcrumb')
<span class="font-medium text-slate-700">Riwayat Transaksi</span>
@endsection

@section('content')
<div class="flex flex-wrap items-center justify-between gap-3 mb-5">
    <h1 class="text-xl font-bold text-slate-800">Riwayat Transaksi</h1>
    <a href="{{ route('admin.sales.create') }}" class="flex items-center gap-1.5 bg-sky-600 hover:bg-sky-700 text-white font-semibold px-4 py-2 rounded-lg text-sm transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
        Transaksi Baru
    </a>
</div>

<div class="bg-white rounded-xl shadow-sm border border-slate-100 p-4 mb-5">
    <form action="{{ route('admin.sales.index') }}" method="GET" class="flex flex-wrap gap-3">
        <select name="source" class="border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500">
            <option value="">Semua Sumber</option>
            <option value="walk-in" {{ request('source') === 'walk-in' ? 'selected' : '' }}>Walk-in</option>
            <option value="online" {{ request('source') === 'online' ? 'selected' : '' }}>Online</option>
        </select>
        <select name="status" class="border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500">
            <option value="">Semua Status</option>
            <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Selesai</option>
            <option value="voided" {{ request('status') === 'voided' ? 'selected' : '' }}>Voided</option>
        </select>
        <input type="date" name="from" value="{{ request('from') }}" class="border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500">
        <input type="date" name="to" value="{{ request('to') }}" class="border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500">
        <button type="submit" class="bg-slate-800 hover:bg-slate-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">Filter</button>
        @if(request()->hasAny(['source','status','from','to']))
        <a href="{{ route('admin.sales.index') }}" class="bg-slate-100 hover:bg-slate-200 text-slate-700 px-4 py-2 rounded-lg text-sm font-medium transition">Reset</a>
        @endif
    </form>
</div>

<div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-x-auto">
    <table class="w-full text-sm">
        <thead class="bg-slate-50/80">
            <tr class="border-b border-slate-200/60 text-[10px] font-bold uppercase tracking-widest text-slate-500">
                <th class="text-left py-3.5 px-4">No. Nota</th>
                <th class="text-left py-3.5 px-4">Tanggal</th>
                <th class="text-left py-3.5 px-4">Pelanggan</th>
                <th class="text-center py-3.5 px-4">Sumber</th>
                <th class="text-right py-3.5 px-4">Total</th>
                <th class="text-center py-3.5 px-4">Status</th>
                <th class="text-center py-3.5 px-4">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100/60">
            @forelse($sales as $sale)
            <tr class="hover:bg-slate-50/80 transition-colors group {{ $sale->status === 'voided' ? 'opacity-60' : '' }}">
                <td class="py-3 px-4 font-medium text-slate-700">{{ $sale->sale_number }}</td>
                <td class="py-3 px-4 text-slate-500 text-xs whitespace-nowrap">{{ $sale->created_at->format('d/m/Y H:i') }}</td>
                <td class="py-3 px-4 text-slate-600">{{ $sale->customer_name }}</td>
                <td class="py-3 px-4 text-center">
                    <span class="inline-flex px-2 py-0.5 text-xs rounded-full {{ $sale->source === 'online' ? 'bg-blue-50 text-blue-600' : 'bg-slate-100 text-slate-600' }}">{{ $sale->source_label }}</span>
                </td>
                <td class="py-3 px-4 text-right font-semibold {{ $sale->status === 'voided' ? 'line-through text-slate-400' : 'text-slate-800' }}">Rp {{ number_format($sale->total, 0, ',', '.') }}</td>
                <td class="py-3 px-4 text-center"><x-status-badge :status="$sale->status" /></td>
                <td class="py-3 px-4 text-center">
                    <a href="{{ route('admin.sales.show', $sale) }}" class="text-sky-600 hover:text-sky-700 font-medium text-xs">Lihat →</a>
                </td>
            </tr>
            @empty
            <tr><td colspan="7" class="py-16 text-center text-slate-400">Belum ada transaksi</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@if($sales->hasPages())
<div class="mt-5">{{ $sales->links() }}</div>
@endif
@endsection
