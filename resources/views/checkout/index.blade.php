@extends('layouts.public')
@section('title', 'Checkout — CV Berkah')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-2xl font-bold text-slate-800 mb-6">Konfirmasi Pesanan</h1>

    <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">
        {{-- ORDER FORM --}}
        <div class="lg:col-span-3">
            <div class="bg-white border border-slate-100 rounded-xl p-6 shadow-sm">
                <h2 class="font-semibold text-slate-700 mb-4">Data Pemesan</h2>

                <form action="{{ route('checkout.store') }}" method="POST" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
                        <input type="text" name="customer_name" value="{{ old('customer_name') }}"
                               class="w-full border border-slate-200 rounded-lg px-4 py-3 text-base focus:outline-none focus:ring-2 focus:ring-amber-400 @error('customer_name') border-red-400 @enderror"
                               placeholder="Masukkan nama lengkap">
                        @error('customer_name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">No. HP (WhatsApp) <span class="text-red-500">*</span></label>
                        <input type="tel" inputmode="numeric" name="customer_phone" value="{{ old('customer_phone') }}"
                               class="w-full border border-slate-200 rounded-lg px-4 py-3 text-base focus:outline-none focus:ring-2 focus:ring-amber-400 @error('customer_phone') border-red-400 @enderror"
                               placeholder="contoh: 08123456789">
                        @error('customer_phone')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Alamat Pengiriman</label>
                        <textarea name="customer_address" rows="3"
                                  class="w-full border border-slate-200 rounded-lg px-4 py-3 text-base focus:outline-none focus:ring-2 focus:ring-amber-400 resize-none"
                                  placeholder="Opsional — untuk estimasi ongkos kirim">{{ old('customer_address') }}</textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Metode Pembayaran <span class="text-red-500">*</span></label>
                        <div class="flex gap-3">
                            <label class="flex-1 flex items-center gap-2 border border-slate-200 rounded-lg px-4 py-3 cursor-pointer hover:border-amber-400 has-[:checked]:border-amber-400 has-[:checked]:bg-amber-50 transition">
                                <input type="radio" name="payment_method" value="cash" {{ old('payment_method', 'cash') === 'cash' ? 'checked' : '' }} class="text-amber-500">
                                <span class="text-sm font-medium text-slate-700">Tunai</span>
                            </label>
                            <label class="flex-1 flex items-center gap-2 border border-slate-200 rounded-lg px-4 py-3 cursor-pointer hover:border-amber-400 has-[:checked]:border-amber-400 has-[:checked]:bg-amber-50 transition">
                                <input type="radio" name="payment_method" value="transfer" {{ old('payment_method') === 'transfer' ? 'checked' : '' }} class="text-amber-500">
                                <span class="text-sm font-medium text-slate-700">Transfer Bank</span>
                            </label>
                        </div>
                        @error('payment_method')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="bg-emerald-50 border border-emerald-100 rounded-lg p-3 text-xs text-emerald-700">
                        <svg class="inline w-3.5 h-3.5 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
                        Setelah submit, Anda akan diarahkan ke WhatsApp admin untuk konfirmasi pesanan dan harga final.
                    </div>

                    <button type="submit"
                            class="w-full bg-amber-500 hover:bg-amber-400 text-slate-900 font-bold py-3.5 rounded-xl transition duration-200 flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                        Pesan via WhatsApp
                    </button>
                </form>
            </div>
        </div>

        {{-- ORDER SUMMARY --}}
        <div class="lg:col-span-2">
            <div class="bg-white border border-slate-100 rounded-xl p-5 shadow-sm sticky top-20">
                <h2 class="font-semibold text-slate-700 mb-4">Ringkasan Pesanan</h2>
                <div class="space-y-3 mb-4">
                    @foreach($cartItems as $item)
                    <div class="flex items-center gap-3">
                        <img src="{{ $item['product']->image_url }}" alt="{{ $item['product']->name }}" class="w-10 h-10 rounded-lg object-cover bg-slate-100 shrink-0">
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-medium text-slate-700 truncate">{{ $item['product']->name }}</p>
                            <p class="text-xs text-slate-400">{{ $item['qty'] }} {{ $item['product']->unit }}</p>
                        </div>
                        <p class="text-xs font-semibold text-slate-700">Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</p>
                    </div>
                    @endforeach
                </div>
                <div class="border-t border-slate-100 pt-3 flex justify-between items-center">
                    <span class="font-medium text-slate-600">Total Estimasi</span>
                    <span class="font-bold text-xl text-amber-600">Rp {{ number_format($total, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
