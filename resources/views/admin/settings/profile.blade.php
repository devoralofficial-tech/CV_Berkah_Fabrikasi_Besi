@extends('layouts.admin')
@section('title', 'Profil Admin')
@section('breadcrumb')
<span class="font-medium text-slate-700">Profil Admin</span>
@endsection

@section('content')
<div class="max-w-lg">
    <h1 class="text-xl font-bold text-slate-800 mb-6">Ubah Profil</h1>
    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-6 space-y-4">
        <form action="{{ route('admin.settings.profile.update') }}" method="POST" class="space-y-4">
            @csrf @method('PUT')
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Nama <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name', auth()->user()->name) }}"
                       class="w-full border border-slate-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500 @error('name') border-red-400 @enderror">
                @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Email</label>
                <input type="email" value="{{ auth()->user()->email }}" disabled
                       class="w-full border border-slate-200 rounded-lg px-3 py-2.5 text-sm bg-slate-50 text-slate-500 cursor-not-allowed">
            </div>
            <button type="submit" class="bg-sky-600 hover:bg-sky-700 text-white font-bold px-6 py-2.5 rounded-lg text-sm transition-all shadow-sm">Perbarui Profil</button>
        </form>
    </div>
</div>
@endsection
