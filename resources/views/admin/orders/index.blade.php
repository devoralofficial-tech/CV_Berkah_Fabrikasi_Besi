@extends('layouts.admin')
@section('title', 'Pesanan Online')
@section('breadcrumb')
<span class="font-medium text-slate-700">Pesanan Online</span>
@endsection

@section('content')
<div class="flex flex-wrap items-center justify-between gap-3 mb-5">
    <h1 class="text-xl font-bold text-slate-800">Pesanan Online</h1>
</div>

<div class="bg-white rounded-xl shadow-sm border border-slate-100 p-4 mb-5">
    <form action="{{ route('admin.orders.index') }}" method="GET" class="flex flex-wrap gap-3">
        <select name="status" class="border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500">
            <option value="">Semua Status</option>
            @foreach(['pending' => 'Pending', 'contacted' => 'Dihubungi', 'completed' => 'Selesai', 'cancelled' => 'Dibatalkan'] as $val => $label)
            <option value="{{ $val }}" {{ request('status') === $val ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
        <input type="date" name="from" value="{{ request('from') }}" class="border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500">
        <input type="date" name="to" value="{{ request('to') }}" class="border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500">
        <button type="submit" class="bg-slate-800 hover:bg-slate-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">Filter</button>
        @if(request()->hasAny(['status','from','to']))
        <a href="{{ route('admin.orders.index') }}" class="bg-slate-100 hover:bg-slate-200 text-slate-700 px-4 py-2 rounded-lg text-sm font-medium transition">Reset</a>
        @endif
    </form>
</div>

<div class="bg-transparent md:bg-white md:rounded-xl md:shadow-sm md:border border-slate-100 overflow-hidden">
    <table class="w-full text-sm block md:table">
        <thead class="bg-slate-50/80 hidden md:table-header-group">
            <tr class="border-b border-slate-200/60 text-[10px] font-bold uppercase tracking-widest text-slate-500">
                <th class="text-left py-3.5 px-4">Order #</th>
                <th class="text-left py-3.5 px-4">Pelanggan</th>
                <th class="text-left py-3.5 px-4">No. HP</th>
                <th class="text-right py-3.5 px-4">Total Estimasi</th>
                <th class="text-center py-3.5 px-4">Status</th>
                <th class="text-left py-3.5 px-4">Tanggal</th>
                <th class="text-center py-3.5 px-4">Aksi</th>
            </tr>
        </thead>
        <tbody class="block md:table-row-group space-y-4 md:space-y-0 md:divide-y md:divide-slate-100/60">
            @forelse($orders as $order)
            <tr class="block md:table-row bg-white rounded-xl shadow-sm border border-slate-100 md:border-none md:shadow-none p-4 md:p-0 hover:bg-slate-50/80 transition-colors group">
                <td class="block md:table-cell py-1 md:py-3 px-0 md:px-4">
                    <div class="flex justify-between items-start md:block">
                        <div>
                            <span class="font-medium text-slate-700 block">{{ $order->order_number }}</span>
                            <span class="text-xs text-slate-500 md:hidden">{{ $order->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                        <div class="md:hidden">
                            <x-status-badge :status="$order->status" />
                        </div>
                    </div>
                </td>
                <td class="block md:table-cell py-2 md:py-3 px-0 md:px-4 text-slate-700">
                    <span class="font-bold md:font-normal">{{ $order->customer_name }}</span>
                    <span class="text-xs text-slate-500 block md:hidden">{{ $order->customer_phone }}</span>
                </td>
                <td class="hidden md:table-cell py-3 px-4 text-slate-500 text-xs">{{ $order->customer_phone }}</td>
                <td class="flex md:table-cell justify-between items-center py-2 md:py-3 px-0 md:px-4 border-t border-slate-100 md:border-none mt-2 md:mt-0 text-right font-semibold text-slate-700">
                    <span class="md:hidden text-xs text-slate-500 font-medium">Total:</span>
                    Rp {{ number_format($order->total_estimate, 0, ',', '.') }}
                </td>
                <td class="hidden md:table-cell py-3 px-4 text-center"><x-status-badge :status="$order->status" /></td>
                <td class="hidden md:table-cell py-3 px-4 text-slate-500 text-xs whitespace-nowrap">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                <td class="block md:table-cell py-3 md:py-3 px-0 md:px-4 text-center border-t border-slate-100 md:border-none mt-2 md:mt-0">
                    <a href="{{ route('admin.orders.show', $order) }}" class="flex md:inline-block w-full justify-center text-sky-600 hover:text-sky-700 font-bold md:font-medium text-sm md:text-xs bg-sky-50 md:bg-transparent rounded-lg py-2.5 md:py-0 border border-sky-100 md:border-none">Lihat Detail →</a>
                </td>
            </tr>
            @empty
            <tr class="block md:table-row bg-white rounded-xl shadow-sm border border-slate-100 md:border-none md:shadow-none p-4 md:p-0"><td colspan="7" class="block md:table-cell py-16 text-center text-slate-400">Belum ada pesanan</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@if($orders->hasPages())
<div class="mt-5">{{ $orders->links() }}</div>
@endif
@endsection
