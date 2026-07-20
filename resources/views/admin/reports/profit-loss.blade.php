@extends('layouts.admin')
@section('title', 'Laporan Laba Rugi')
@section('breadcrumb')
<span class="font-medium text-slate-700">Laporan Laba Rugi</span>
@endsection

@section('content')
<div class="flex flex-wrap items-center justify-between gap-3 mb-5">
    <h1 class="text-xl font-bold text-slate-800">Laporan Laba Rugi</h1>
    <a href="{{ route('admin.reports.profit-loss', array_merge(request()->all(), ['export' => 1])) }}"
       class="flex items-center gap-1.5 border border-slate-200 hover:bg-slate-50 text-slate-700 px-4 py-2 rounded-lg text-sm font-medium transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        Export Excel
    </a>
</div>

<div class="bg-white rounded-xl shadow-sm border border-slate-100 p-4 mb-5">
    <form action="{{ route('admin.reports.profit-loss') }}" method="GET" class="flex flex-wrap gap-3">
        <input type="date" name="from" value="{{ $from }}" class="border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500">
        <input type="date" name="to" value="{{ $to }}" class="border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500">
        <button type="submit" class="bg-slate-800 hover:bg-slate-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">Filter</button>
    </form>
</div>

<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
    @foreach([
        ['label' => 'Total Pendapatan', 'value' => 'Rp ' . number_format($revenue, 0, ',', '.'), 'color' => 'emerald'],
        ['label' => 'HPP (Modal)', 'value' => 'Rp ' . number_format($cogs, 0, ',', '.'), 'color' => 'red'],
        ['label' => 'Laba Kotor', 'value' => 'Rp ' . number_format($grossProfit, 0, ',', '.'), 'color' => $grossProfit >= 0 ? 'amber' : 'red'],
    ] as $stat)
    <div class="bg-white rounded-xl p-5 shadow-sm border border-slate-100">
        <p class="text-xs text-slate-400 uppercase tracking-wider mb-1">{{ $stat['label'] }}</p>
        <p class="text-xl font-bold text-{{ $stat['color'] }}-600">{{ $stat['value'] }}</p>
    </div>
    @endforeach
</div>

<p class="text-xs text-slate-400 mb-4">* Laba kotor = Total Pendapatan − HPP. Catatan: HPP hanya tersedia jika harga modal diisi.</p>
@endsection
