@extends('layouts.admin')
@section('title', 'Detail Pesanan')
@section('breadcrumb')
<a href="{{ route('admin.orders.index') }}" class="hover:text-slate-600">Pesanan</a> › <span class="font-medium text-slate-700">{{ $order->order_number }}</span>
@endsection

@section('content')
<div class="flex flex-wrap gap-3 items-center justify-between mb-5">
    <div>
        <h1 class="text-xl font-bold text-slate-800">{{ $order->order_number }}</h1>
        <p class="text-sm text-slate-500">{{ $order->created_at->format('d/m/Y H:i') }}</p>
    </div>
    <x-status-badge :status="$order->status" />
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2">
        <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-x-auto mb-4">
            <table class="w-full text-sm">
                <thead class="border-b border-slate-100">
                    <tr class="text-xs uppercase tracking-wider text-slate-400">
                        <th class="text-left py-3.5 px-4">Produk</th>
                        <th class="text-right py-3.5 px-4">Qty</th>
                        <th class="text-right py-3.5 px-4">Harga Estimasi</th>
                        <th class="text-right py-3.5 px-4">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100/60">
                    @foreach($order->items as $item)
                    <tr class="hover:bg-slate-50">
                        <td class="py-3 px-4 font-medium text-slate-700">{{ $item->product?->name ?? '-' }}</td>
                        <td class="py-3 px-4 text-right text-slate-600">{{ $item->qty }} {{ $item->product?->unit }}</td>
                        <td class="py-3 px-4 text-right text-slate-600">Rp {{ number_format($item->unit_price_snapshot, 0, ',', '.') }}</td>
                        <td class="py-3 px-4 text-right font-semibold text-slate-800">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Status Actions --}}
        @if(in_array($order->status, ['pending', 'contacted']))
        <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-5">
            <h3 class="font-semibold text-slate-700 mb-3">Ubah Status</h3>
            <form action="{{ route('admin.orders.update-status', $order) }}" method="POST" class="flex flex-wrap gap-2">
                @csrf
                @if($order->status === 'pending')
                <button type="submit" name="status" value="contacted" class="bg-blue-500 hover:bg-blue-400 text-white font-semibold px-5 py-2 rounded-lg text-sm transition">
                    ✉ Sudah Dihubungi
                </button>
                <button type="submit" name="status" value="cancelled" class="bg-red-50 hover:bg-red-100 text-red-600 border border-red-200 font-semibold px-5 py-2 rounded-lg text-sm transition"
                        onclick="return confirm('Batalkan pesanan ini?')">
                    Batalkan
                </button>
                @elseif($order->status === 'contacted')
                <button type="submit" name="status" value="completed" class="bg-emerald-500 hover:bg-emerald-400 text-white font-semibold px-5 py-2 rounded-lg text-sm transition"
                        onclick="return confirm('Tandai selesai dan kurangi stok?')">
                    ✓ Selesai & Catat Penjualan
                </button>
                <button type="submit" name="status" value="cancelled" class="bg-red-50 hover:bg-red-100 text-red-600 border border-red-200 font-semibold px-5 py-2 rounded-lg text-sm transition"
                        onclick="return confirm('Batalkan pesanan ini?')">
                    Batalkan
                </button>
                @endif
            </form>
        </div>
        @endif
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-5 h-fit">
        <h3 class="font-semibold text-slate-700 mb-3">Info Pelanggan</h3>
        <div class="space-y-2 text-sm">
            <div class="flex justify-between">
                <span class="text-slate-500">Nama</span>
                <span class="font-medium text-slate-700">{{ $order->customer_name }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-slate-500">No. HP</span>
                <span class="font-medium text-slate-700">{{ $order->customer_phone }}</span>
            </div>
            @if($order->customer_address)
            <div>
                <span class="text-slate-500 block mb-1">Alamat</span>
                <span class="text-slate-700 text-xs">{{ $order->customer_address }}</span>
            </div>
            @endif
            <div class="flex justify-between">
                <span class="text-slate-500">Pembayaran</span>
                <span class="font-medium text-slate-700">{{ $order->payment_method === 'cash' ? 'Tunai' : 'Transfer' }}</span>
            </div>
            <div class="border-t border-slate-100 pt-2 flex justify-between">
                <span class="font-semibold text-slate-700">Total Estimasi</span>
                <span class="font-bold text-xl text-amber-600">Rp {{ number_format($order->total_estimate, 0, ',', '.') }}</span>
            </div>
        </div>
        @php $waNumber = preg_replace('/[^0-9]/', '', $order->customer_phone); @endphp
        <a href="https://wa.me/62{{ ltrim($waNumber, '0') }}" target="_blank"
           class="mt-4 flex items-center justify-center gap-2 bg-emerald-600 hover:bg-emerald-500 text-white font-semibold px-4 py-2 rounded-lg text-sm transition w-full">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
            Hubungi via WhatsApp
        </a>
    </div>
</div>
@endsection
