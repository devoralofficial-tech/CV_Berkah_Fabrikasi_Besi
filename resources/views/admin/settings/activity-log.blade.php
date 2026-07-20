@extends('layouts.admin')
@section('title', 'Log Aktivitas Sistem')
@section('breadcrumb')
<a href="{{ route('admin.settings.index') }}" class="hover:text-slate-600">Pengaturan</a> › <span class="font-medium text-slate-700">Log Aktivitas</span>
@endsection

@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-xl font-bold text-slate-800">Log Aktivitas & Audit</h1>
</div>

<div class="bg-white rounded-xl shadow-sm border border-slate-100 p-6 mb-6">
    <form action="{{ route('admin.settings.activity-log') }}" method="GET" class="flex flex-col md:flex-row gap-4 items-end">
        <div>
            <label class="block text-xs font-medium text-slate-500 mb-1">Dari Tanggal</label>
            <input type="date" name="date_from" value="{{ request('date_from') }}" class="border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500">
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-500 mb-1">Sampai Tanggal</label>
            <input type="date" name="date_to" value="{{ request('date_to') }}" class="border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500">
        </div>
        <div class="flex gap-2">
            <button type="submit" class="bg-sky-600 hover:bg-sky-700 text-white px-4 py-2 rounded-lg font-semibold text-sm transition">Filter</button>
            <a href="{{ route('admin.settings.activity-log') }}" class="bg-slate-100 hover:bg-slate-200 text-slate-600 px-4 py-2 rounded-lg font-medium text-sm transition">Reset</a>
        </div>
    </form>
</div>

<div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm whitespace-nowrap">
            <thead class="bg-slate-50 text-slate-500">
                <tr>
                    <th class="px-6 py-4 font-medium">Waktu</th>
                    <th class="px-6 py-4 font-medium">Pengguna (Admin)</th>
                    <th class="px-6 py-4 font-medium">Aksi / Catatan</th>
                    <th class="px-6 py-4 font-medium">Produk Terpengaruh</th>
                    <th class="px-6 py-4 font-medium text-right">Mutasi Stok</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-slate-700">
                @forelse($logs as $log)
                <tr class="hover:bg-slate-50/80 transition-colors group">
                    <td class="px-6 py-4">{{ $log->created_at->format('d M Y H:i:s') }}</td>
                    <td class="px-6 py-4 font-medium text-slate-800">{{ $log->creator->name ?? 'Sistem' }}</td>
                    <td class="px-6 py-4">
                        <span class="inline-block px-2 py-1 rounded text-xs font-bold mb-1
                            {{ $log->source === 'sale' ? 'bg-blue-50 text-blue-600' : '' }}
                            {{ $log->source === 'void' ? 'bg-red-50 text-red-600' : '' }}
                            {{ $log->source === 'purchase' ? 'bg-emerald-50 text-emerald-600' : '' }}
                            {{ $log->source === 'opname' ? 'bg-purple-50 text-purple-600' : '' }}
                            {{ $log->source === 'manual' ? 'bg-sky-50 text-sky-600' : '' }}
                            {{ $log->source === 'initial' ? 'bg-slate-100 text-slate-600' : '' }}
                        ">
                            {{ strtoupper($log->source) }}
                        </span><br>
                        <span class="text-slate-500 truncate block max-w-xs" title="{{ $log->note }}">{{ $log->note ?? '-' }}</span>
                    </td>
                    <td class="px-6 py-4 font-medium">{{ $log->product->name ?? 'Produk Dihapus' }}</td>
                    <td class="px-6 py-4 text-right font-bold {{ $log->type === 'in' ? 'text-emerald-600' : 'text-red-500' }}">
                        {{ $log->type === 'in' ? '+' : '-' }}{{ $log->qty }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-slate-400">
                        Belum ada aktivitas tercatat.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($logs->hasPages())
    <div class="px-6 py-4 border-t border-slate-100">
        {{ $logs->links() }}
    </div>
    @endif
</div>
@endsection
