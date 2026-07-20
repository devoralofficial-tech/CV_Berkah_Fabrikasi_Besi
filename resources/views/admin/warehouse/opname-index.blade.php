@extends('layouts.admin')
@section('title', 'Riwayat Stock Opname')
@section('breadcrumb')
<a href="{{ route('admin.dashboard') }}" class="hover:text-slate-600">Dashboard</a> › <span class="font-medium text-slate-700">Stock Opname</span>
@endsection

@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-xl font-bold text-slate-800">Riwayat Stock Opname</h1>
    <a href="{{ route('admin.warehouse.opname-create') }}" class="flex items-center gap-2 bg-sky-600 hover:bg-sky-700 text-white font-semibold px-4 py-2 rounded-lg text-sm transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
        Opname Baru
    </a>
</div>

<div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-x-auto">
    <table class="w-full text-sm">
        <thead class="bg-slate-50/80">
            <tr class="border-b border-slate-200/60 text-[10px] font-bold uppercase tracking-widest text-slate-500">
                <th class="text-left py-3.5 px-4">Tanggal</th>
                <th class="text-left py-3.5 px-4">Catatan</th>
                <th class="text-left py-3.5 px-4">Dilakukan Oleh</th>
                <th class="text-center py-3.5 px-4">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100/60">
            @forelse($opnames as $opname)
            <tr class="hover:bg-slate-50/80 transition-colors group">
                <td class="py-3 px-4 font-medium text-slate-700">{{ $opname->opname_date->format('d/m/Y') }}</td>
                <td class="py-3 px-4 text-slate-500">{{ $opname->note ?? '-' }}</td>
                <td class="py-3 px-4 text-slate-500">{{ $opname->creator?->name ?? '-' }}</td>
                <td class="py-3 px-4 text-center">
                    <a href="{{ route('admin.warehouse.opname-show', $opname) }}" class="text-sky-600 hover:text-sky-700 font-medium text-xs">Lihat Detail</a>
                </td>
            </tr>
            @empty
            <tr><td colspan="4" class="py-16 text-center text-slate-400">Belum ada stock opname</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@if($opnames->hasPages())
<div class="mt-5">{{ $opnames->links() }}</div>
@endif
@endsection
