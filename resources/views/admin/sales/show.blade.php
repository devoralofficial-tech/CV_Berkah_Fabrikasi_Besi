@extends('layouts.admin')
@section('title', 'Nota: ' . $sale->sale_number)
@section('breadcrumb')
<a href="{{ route('admin.sales.index') }}" class="hover:text-slate-600">Riwayat</a> › <span class="font-medium text-slate-700">{{ $sale->sale_number }}</span>
@endsection

@section('content')
<div class="flex flex-wrap gap-3 justify-between mb-5">
    <div>
        <h1 class="text-xl font-bold text-slate-800">{{ $sale->sale_number }}</h1>
        <p class="text-sm text-slate-500">{{ $sale->created_at->format('d/m/Y H:i') }} · Kasir: {{ $sale->creator?->name }}</p>
    </div>
    <div class="flex gap-2">
        <button onclick="printNota()" class="flex items-center gap-1.5 border border-slate-200 hover:bg-slate-50 text-slate-700 px-4 py-2 rounded-lg text-sm font-medium transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
            Print Nota
        </button>
        @if($sale->status === 'completed')
        <div x-data="{ openVoid: false }">
            <button @click="openVoid = true" class="flex items-center gap-1.5 bg-red-50 hover:bg-red-100 text-red-600 border border-red-200 px-4 py-2 rounded-lg text-sm font-medium transition">
                Void Transaksi
            </button>

            {{-- Modal Konfirmasi Password --}}
            <div x-show="openVoid" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" x-cloak style="display: none;">
                <div @click.away="openVoid = false" class="bg-white rounded-xl shadow-xl w-full max-w-md p-6">
                    <h3 class="text-lg font-bold text-slate-800 mb-2">Konfirmasi Void Transaksi</h3>
                    <p class="text-sm text-slate-500 mb-4">Aksi ini akan membatalkan transaksi dan mengembalikan stok. Masukkan password Anda untuk melanjutkan.</p>
                    
                    <form action="{{ route('admin.sales.void', $sale) }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <input type="password" name="password" required placeholder="Password Admin" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-400">
                        </div>
                        <div class="flex justify-end gap-2">
                            <button type="button" @click="openVoid = false" class="px-4 py-2 text-sm font-medium text-slate-600 bg-slate-100 rounded-lg hover:bg-slate-200">Batal</button>
                            <button type="submit" class="px-4 py-2 text-sm font-bold text-white bg-red-500 rounded-lg hover:bg-red-600">Proses Void</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

{{-- Nota Print Area --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2">
        <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="border-b border-slate-100">
                    <tr class="text-xs uppercase tracking-wider text-slate-400">
                        <th class="text-left py-3.5 px-4">Produk</th>
                        <th class="text-right py-3.5 px-4">Qty</th>
                        <th class="text-right py-3.5 px-4">Harga</th>
                        <th class="text-right py-3.5 px-4">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100/60">
                    @foreach($sale->items as $item)
                    <tr class="hover:bg-slate-50">
                        <td class="py-3 px-4 font-medium text-slate-700">{{ $item->product?->name ?? 'Produk dihapus' }}</td>
                        <td class="py-3 px-4 text-right text-slate-600">{{ $item->qty }} {{ $item->product?->unit }}</td>
                        <td class="py-3 px-4 text-right text-slate-600">Rp {{ number_format($item->unit_price_snapshot, 0, ',', '.') }}</td>
                        <td class="py-3 px-4 text-right font-semibold text-slate-800">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-5 h-fit">
        <div class="space-y-2 text-sm">
            <div class="flex justify-between">
                <span class="text-slate-500">Status</span>
                <x-status-badge :status="$sale->status" />
            </div>
            <div class="flex justify-between">
                <span class="text-slate-500">Pelanggan</span>
                <span class="font-medium text-slate-700">{{ $sale->customer_name }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-slate-500">Sumber</span>
                <span class="font-medium text-slate-700">{{ $sale->source_label }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-slate-500">Pembayaran</span>
                <span class="font-medium text-slate-700">{{ $sale->payment_method === 'cash' ? 'Tunai' : 'Transfer' }}</span>
            </div>
            <div class="border-t border-slate-100 pt-2 flex justify-between">
                <span class="font-semibold text-slate-700">Total</span>
                <span class="font-bold text-xl text-amber-600">Rp {{ number_format($sale->total, 0, ',', '.') }}</span>
            </div>
            @if($sale->payment_method === 'cash')
            <div class="flex justify-between text-xs">
                <span class="text-slate-500">Bayar</span>
                <span class="text-slate-700">Rp {{ number_format($sale->amount_paid, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between text-xs">
                <span class="text-slate-500">Kembalian</span>
                <span class="font-semibold text-emerald-600">Rp {{ number_format($sale->change, 0, ',', '.') }}</span>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function printNota() {
    window.print();
}
</script>
<style media="print">
    aside, header, .no-print, button, form { display: none !important; }
    body { background: white !important; }
    .lg\:col-span-2 { grid-column: 1 / -1; }
</style>
@endpush
