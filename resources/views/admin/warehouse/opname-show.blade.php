@extends('layouts.admin')
@section('title', 'Detail Opname')
@section('breadcrumb')
<a href="{{ route('admin.warehouse.opname-index') }}" class="hover:text-slate-600">Stock Opname</a> › <span class="font-medium text-slate-700">Detail</span>
@endsection

@section('content')
<div class="mb-5 flex items-center justify-between">
    <div>
        <h1 class="text-xl font-bold text-slate-800">Detail Stock Opname</h1>
        <p class="text-sm text-slate-500 mt-1">{{ $opname->opname_date->format('d MMMM Y') }} · Oleh: {{ $opname->creator?->name }}</p>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-x-auto">
    <table class="w-full text-sm">
        <thead class="bg-slate-50/80">
            <tr class="border-b border-slate-200/60 text-[10px] font-bold uppercase tracking-widest text-slate-500">
                <th class="text-left py-3.5 px-4">Produk</th>
                <th class="text-right py-3.5 px-4">Stok Sistem</th>
                <th class="text-right py-3.5 px-4">Stok Fisik</th>
                <th class="text-right py-3.5 px-4">Selisih</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100/60">
            @foreach($opname->items as $item)
            <tr class="hover:bg-slate-50">
                <td class="py-3 px-4">
                    <p class="font-medium text-slate-700">{{ $item->product?->name ?? 'Produk dihapus' }}</p>
                    <p class="text-xs text-slate-400">{{ $item->product?->unit }}</p>
                </td>
                <td class="py-3 px-4 text-right text-slate-600">{{ number_format($item->system_stock, 2, ',', '.') }}</td>
                <td class="py-3 px-4 text-right text-slate-600">{{ number_format($item->physical_stock, 2, ',', '.') }}</td>
                <td class="py-3 px-4 text-right font-semibold {{ $item->difference > 0 ? 'text-emerald-600' : ($item->difference < 0 ? 'text-red-500' : 'text-slate-400') }}">
                    {{ $item->difference > 0 ? '+' : '' }}{{ number_format($item->difference, 2, ',', '.') }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@if($opname->note)
<p class="mt-4 text-sm text-slate-500 bg-white border border-slate-100 rounded-xl px-5 py-3"><strong>Catatan:</strong> {{ $opname->note }}</p>
@endif
@endsection
