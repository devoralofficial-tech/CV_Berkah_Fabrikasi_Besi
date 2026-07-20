@extends('layouts.admin')
@section('title', 'Stock Opname')
@section('breadcrumb')
<a href="{{ route('admin.warehouse.opname-index') }}" class="hover:text-slate-600">Stock Opname</a> › <span class="font-medium text-slate-700">Buat Baru</span>
@endsection

@section('content')
<div class="max-w-4xl">
    <h1 class="text-xl font-bold text-slate-800 mb-6">Buat Stock Opname</h1>
    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-6">
        <form action="{{ route('admin.warehouse.opname-store') }}" method="POST" x-data="opnameForm()" @submit.prevent="submitForm($event)" id="opnameForm">
            @csrf
            <div class="grid grid-cols-2 gap-4 mb-6">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Tanggal Opname <span class="text-red-500">*</span></label>
                    <input type="date" name="opname_date" value="{{ old('opname_date', today()->format('Y-m-d')) }}" class="w-full border border-slate-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Catatan</label>
                    <input type="text" name="note" value="{{ old('note') }}" class="w-full border border-slate-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500" placeholder="Opsional">
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm mb-6">
                    <thead class="bg-slate-50">
                        <tr class="text-xs uppercase tracking-wider text-slate-400">
                            <th class="text-left py-3 px-4">Produk</th>
                            <th class="text-left py-3 px-4">Satuan</th>
                            <th class="text-right py-3 px-4">Stok Sistem</th>
                            <th class="text-right py-3 px-4">Stok Fisik <span class="text-red-500">*</span></th>
                            <th class="text-right py-3 px-4">Selisih</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100/60">
                        @foreach($products as $i => $product)
                        <tr x-data="opnameRow({{ $product->stock }}, {{ $i }})" class="hover:bg-slate-50">
                            <input type="hidden" name="items[{{ $i }}][product_id]" value="{{ $product->id }}">
                            <td class="py-2.5 px-4">
                                <p class="font-medium text-slate-700">{{ $product->name }}</p>
                                <p class="text-xs text-slate-400">{{ $product->category?->name }}</p>
                            </td>
                            <td class="py-2.5 px-4 text-slate-500 text-xs">{{ $product->unit }}</td>
                            <td class="py-2.5 px-4 text-right font-semibold text-slate-700">{{ number_format($product->stock, 2, ',', '.') }}</td>
                            <td class="py-2.5 px-4">
                                <input type="number" name="items[{{ $i }}][physical_stock]"
                                       x-model="physical" @input="calcDiff()"
                                       min="0" step="0.01"
                                       class="w-28 text-right border border-slate-200 rounded-lg px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500 ml-auto block physical-input"
                                       data-original="{{ $product->stock }}">
                            </td>
                            <td class="py-2.5 px-4 text-right font-semibold" :class="diff > 0 ? 'text-emerald-600' : (diff < 0 ? 'text-red-500' : 'text-slate-400')" x-text="diff > 0 ? '+' + parseFloat(diff).toFixed(2) : parseFloat(diff).toFixed(2)">0</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="bg-sky-600 hover:bg-sky-700 text-white font-bold px-6 py-2.5 rounded-lg text-sm transition-all shadow-sm">Simpan Opname</button>
                <a href="{{ route('admin.warehouse.opname-index') }}" class="px-6 py-2.5 border border-slate-200 text-slate-600 hover:bg-slate-50 hover:text-slate-800 font-bold rounded-lg text-sm transition-colors">Batal</a>
            </div>

            {{-- Modal Konfirmasi Password --}}
            <div x-show="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" x-cloak style="display: none;">
                <div @click.away="showModal = false" class="bg-white rounded-xl shadow-xl w-full max-w-md p-6">
                    <h3 class="text-lg font-bold text-slate-800 mb-2">Konfirmasi Penyesuaian Stok</h3>
                    <p class="text-sm text-slate-500 mb-4">Sistem mendeteksi adanya selisih stok (stok fisik berbeda dengan sistem). Masukkan password Admin Anda untuk memvalidasi perubahan ini.</p>
                    
                    <div class="mb-4">
                        <input type="password" name="password" x-model="password" placeholder="Password Admin" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500">
                    </div>
                    <div class="flex justify-end gap-2">
                        <button type="button" @click="showModal = false" class="px-4 py-2 text-sm font-medium text-slate-600 bg-slate-100 rounded-lg hover:bg-slate-200">Batal</button>
                        <button type="button" @click="confirmSubmit()" class="px-4 py-2 text-sm font-bold text-slate-900 bg-amber-500 rounded-lg hover:bg-amber-600">Simpan Opname</button>
                    </div>
                </div>
            </div>

        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function opnameForm() {
    return {
        showModal: false,
        password: '',
        
        submitForm(e) {
            let hasDiscrepancy = false;
            document.querySelectorAll('.physical-input').forEach(input => {
                let original = parseFloat(input.dataset.original || 0);
                let current = parseFloat(input.value || 0);
                if (Math.abs(original - current) > 0.001) {
                    hasDiscrepancy = true;
                }
            });

            if (hasDiscrepancy) {
                this.showModal = true;
            } else {
                document.getElementById('opnameForm').submit();
            }
        },

        confirmSubmit() {
            if (!this.password) {
                alert('Silakan masukkan password admin.');
                return;
            }
            document.getElementById('opnameForm').submit();
        }
    }
}

function opnameRow(systemStock, index) {
    return { 
        physical: systemStock, 
        diff: 0, 
        calcDiff() { 
            this.diff = parseFloat(this.physical || 0) - systemStock; 
        } 
    }
}
</script>
@endpush
