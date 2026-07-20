@extends('layouts.admin')
@section('title', 'Pengaturan')
@section('breadcrumb')
<span class="font-medium text-slate-700">Info Perusahaan</span>
@endsection

@section('content')
<div class="max-w-lg">
    <h1 class="text-xl font-bold text-slate-800 mb-6">Pengaturan Perusahaan</h1>
    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-6">
        <form action="{{ route('admin.settings.update') }}" method="POST" class="space-y-4">
            @csrf @method('PUT')
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Nomor WhatsApp <span class="text-red-500">*</span></label>
                <input type="text" name="wa_number" value="{{ old('wa_number', $setting->wa_number) }}"
                       class="w-full border border-slate-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500 @error('wa_number') border-red-400 @enderror"
                       placeholder="628123456789">
                <p class="text-xs text-slate-400 mt-1">Format internasional tanpa +, contoh: 628123456789</p>
                @error('wa_number')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Alamat</label>
                <textarea name="address" rows="2" class="w-full border border-slate-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500 resize-none">{{ old('address', $setting->address) }}</textarea>
            </div>
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email', $setting->email) }}"
                       class="w-full border border-slate-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500">
            </div>
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Deskripsi Perusahaan</label>
                <textarea name="company_description" rows="3" class="w-full border border-slate-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500 resize-none">{{ old('company_description', $setting->company_description) }}</textarea>
            </div>
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Jam Operasional</label>
                <input type="text" name="operating_hours" value="{{ old('operating_hours', $setting->operating_hours) }}"
                       class="w-full border border-slate-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500"
                       placeholder="Senin–Sabtu, 08.00–17.00">
            </div>
            <div class="pt-2">
                <button type="submit" class="bg-sky-600 hover:bg-sky-700 text-white font-bold px-6 py-2.5 rounded-lg text-sm transition-all shadow-sm">Simpan Pengaturan</button>
            </div>
        </form>
    </div>
</div>
@endsection
