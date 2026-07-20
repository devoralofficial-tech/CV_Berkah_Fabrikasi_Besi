<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — CV Berkah Admin</title>

    {{-- Google Fonts (Hanya Inter) --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>

    @stack('head')

    <style>
        body { font-family: 'Inter', sans-serif; }
        
        /* Custom Scrollbar for Sidebar */
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        .custom-scrollbar:hover::-webkit-scrollbar-thumb { background: #94a3b8; }
    </style>
</head>
<body class="bg-slate-50 antialiased text-slate-800" x-data="{ sidebarOpen: false }">

<div class="flex h-screen overflow-hidden">

    {{-- SIDEBAR --}}
    <aside class="fixed inset-y-0 left-0 z-50 w-64 bg-white border-r border-slate-200 flex flex-col transform transition-transform duration-300 md:translate-x-0"
           :class="sidebarOpen ? 'translate-x-0 shadow-2xl' : '-translate-x-full md:translate-x-0 md:shadow-none'">

        {{-- Header Sidebar (Logo) --}}
        <div class="flex items-center gap-3 px-6 py-5 border-b border-slate-200 bg-white z-10 shadow-sm">
            <div class="w-9 h-9 bg-sky-600 rounded-lg flex items-center justify-center shadow-sm">
                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
            </div>
            <div>
                <p class="text-slate-800 font-bold text-sm tracking-wide leading-tight">CV BERKAH</p>
                <p class="text-slate-400 text-xs mt-0.5 font-medium">Admin Panel</p>
            </div>
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 overflow-y-auto custom-scrollbar py-4 px-3 space-y-1">
            {{-- Ringkasan --}}
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium text-[13px] transition-colors duration-150 mb-4 {{ request()->routeIs('admin.dashboard') ? 'bg-sky-50 text-sky-600 border-l-2 border-sky-600' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-800 border-l-2 border-transparent' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                Ringkasan
            </a>

            {{-- PRODUK --}}
            <div x-data="{ open: {{ request()->routeIs('admin.categories.*') || request()->routeIs('admin.products.*') ? 'true' : 'false' }} }" class="mb-4">
                <button @click="open = !open" class="w-full flex items-center justify-between px-3 mb-1 outline-none group">
                    <span class="text-[10px] font-bold uppercase tracking-[0.1em] text-slate-400 group-hover:text-slate-500 transition-colors">Produk</span>
                    <svg :class="open ? 'rotate-180' : 'rotate-0'" class="w-3.5 h-3.5 text-slate-400 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="open" x-collapse>
                    <a href="{{ route('admin.categories.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium text-[13px] transition-colors duration-150 {{ request()->routeIs('admin.categories.*') ? 'bg-sky-50 text-sky-600 border-l-2 border-sky-600' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-800 border-l-2 border-transparent' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A2 2 0 013 12V7a4 4 0 014-4z"/></svg>
                        Kategori
                    </a>
                    <a href="{{ route('admin.products.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium text-[13px] transition-colors duration-150 {{ request()->routeIs('admin.products.*') ? 'bg-sky-50 text-sky-600 border-l-2 border-sky-600' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-800 border-l-2 border-transparent' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                        Daftar Produk
                    </a>
                </div>
            </div>

            {{-- GUDANG --}}
            <div x-data="{ open: {{ request()->routeIs('admin.warehouse.*') ? 'true' : 'false' }} }" class="mb-4">
                <button @click="open = !open" class="w-full flex items-center justify-between px-3 mb-1 outline-none group">
                    <span class="text-[10px] font-bold uppercase tracking-[0.1em] text-slate-400 group-hover:text-slate-500 transition-colors">Gudang</span>
                    <svg :class="open ? 'rotate-180' : 'rotate-0'" class="w-3.5 h-3.5 text-slate-400 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="open" x-collapse>
                    <a href="{{ route('admin.warehouse.stock-in') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium text-[13px] transition-colors duration-150 {{ request()->routeIs('admin.warehouse.stock-in*') ? 'bg-sky-50 text-sky-600 border-l-2 border-sky-600' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-800 border-l-2 border-transparent' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        Barang Masuk
                    </a>
                    <a href="{{ route('admin.warehouse.stock-out') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium text-[13px] transition-colors duration-150 {{ request()->routeIs('admin.warehouse.stock-out*') ? 'bg-sky-50 text-sky-600 border-l-2 border-sky-600' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-800 border-l-2 border-transparent' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                        Barang Keluar
                    </a>
                    <a href="{{ route('admin.warehouse.opname-index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium text-[13px] transition-colors duration-150 {{ request()->routeIs('admin.warehouse.opname*') ? 'bg-sky-50 text-sky-600 border-l-2 border-sky-600' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-800 border-l-2 border-transparent' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                        Stock Opname
                    </a>
                    <a href="{{ route('admin.warehouse.low-stock') }}" class="flex items-center justify-between px-3 py-2.5 rounded-lg font-medium text-[13px] transition-colors duration-150 {{ request()->routeIs('admin.warehouse.low-stock') ? 'bg-sky-50 text-sky-600 border-l-2 border-sky-600' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-800 border-l-2 border-transparent' }}">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            Kartu Stok
                        </div>
                    </a>
                </div>
            </div>

            {{-- KASIR --}}
            <div x-data="{ open: {{ request()->routeIs('admin.sales.*') ? 'true' : 'false' }} }" class="mb-4">
                <button @click="open = !open" class="w-full flex items-center justify-between px-3 mb-1 outline-none group">
                    <span class="text-[10px] font-bold uppercase tracking-[0.1em] text-slate-400 group-hover:text-slate-500 transition-colors">Kasir</span>
                    <svg :class="open ? 'rotate-180' : 'rotate-0'" class="w-3.5 h-3.5 text-slate-400 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="open" x-collapse>
                    <a href="{{ route('admin.sales.create') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium text-[13px] transition-colors duration-150 {{ request()->routeIs('admin.sales.create') ? 'bg-sky-50 text-sky-600 border-l-2 border-sky-600' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-800 border-l-2 border-transparent' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        Transaksi Baru
                    </a>
                    <a href="{{ route('admin.sales.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium text-[13px] transition-colors duration-150 {{ request()->routeIs('admin.sales.index') || request()->routeIs('admin.sales.show') ? 'bg-sky-50 text-sky-600 border-l-2 border-sky-600' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-800 border-l-2 border-transparent' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Riwayat Transaksi
                    </a>
                </div>
            </div>

            {{-- PESANAN ONLINE --}}
            <a href="{{ route('admin.orders.index') }}" class="flex items-center justify-between px-3 py-2.5 rounded-lg font-medium text-[13px] transition-colors duration-150 mb-4 {{ request()->routeIs('admin.orders.*') ? 'bg-sky-50 text-sky-600 border-l-2 border-sky-600' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-800 border-l-2 border-transparent' }}">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                    Pesanan Online
                </div>
                @php $pendingOrders = \App\Models\Order::where('status', 'pending')->count(); @endphp
                @if($pendingOrders > 0)
                <span class="ml-auto bg-sky-100 text-sky-700 text-[10px] font-bold px-1.5 py-0.5 rounded-md">{{ $pendingOrders }}</span>
                @endif
            </a>

            {{-- LAPORAN --}}
            <div x-data="{ open: {{ request()->routeIs('admin.reports.*') ? 'true' : 'false' }} }" class="mb-4">
                <button @click="open = !open" class="w-full flex items-center justify-between px-3 mb-1 outline-none group">
                    <span class="text-[10px] font-bold uppercase tracking-[0.1em] text-slate-400 group-hover:text-slate-500 transition-colors">Laporan</span>
                    <svg :class="open ? 'rotate-180' : 'rotate-0'" class="w-3.5 h-3.5 text-slate-400 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="open" x-collapse>
                    <a href="{{ route('admin.reports.sales') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium text-[13px] transition-colors duration-150 {{ request()->routeIs('admin.reports.sales') ? 'bg-sky-50 text-sky-600 border-l-2 border-sky-600' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-800 border-l-2 border-transparent' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                        Laporan Penjualan
                    </a>
                    <a href="{{ route('admin.reports.stock') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium text-[13px] transition-colors duration-150 {{ request()->routeIs('admin.reports.stock') ? 'bg-sky-50 text-sky-600 border-l-2 border-sky-600' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-800 border-l-2 border-transparent' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/></svg>
                        Laporan Stok
                    </a>
                    <a href="{{ route('admin.reports.profit-loss') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium text-[13px] transition-colors duration-150 {{ request()->routeIs('admin.reports.profit-loss') ? 'bg-sky-50 text-sky-600 border-l-2 border-sky-600' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-800 border-l-2 border-transparent' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Laba Rugi
                    </a>
                    <a href="{{ route('admin.warehouse.low-stock') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium text-[13px] transition-colors duration-150 {{ request()->routeIs('admin.warehouse.low-stock') ? 'bg-sky-50 text-sky-600 border-l-2 border-sky-600' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-800 border-l-2 border-transparent' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        Stok Menipis
                    </a>
                </div>
            </div>

            {{-- PENGATURAN --}}
            <div x-data="{ open: {{ request()->routeIs('admin.settings.*') ? 'true' : 'false' }} }" class="mb-4">
                <button @click="open = !open" class="w-full flex items-center justify-between px-3 mb-1 outline-none group">
                    <span class="text-[10px] font-bold uppercase tracking-[0.1em] text-slate-400 group-hover:text-slate-500 transition-colors">Pengaturan</span>
                    <svg :class="open ? 'rotate-180' : 'rotate-0'" class="w-3.5 h-3.5 text-slate-400 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="open" x-collapse>
                    <a href="{{ route('admin.settings.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium text-[13px] transition-colors duration-150 {{ request()->routeIs('admin.settings.index') ? 'bg-sky-50 text-sky-600 border-l-2 border-sky-600' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-800 border-l-2 border-transparent' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        Info Perusahaan
                    </a>
                    <a href="{{ route('admin.settings.activity-log') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium text-[13px] transition-colors duration-150 {{ request()->routeIs('admin.settings.activity-log') ? 'bg-sky-50 text-sky-600 border-l-2 border-sky-600' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-800 border-l-2 border-transparent' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        Log Aktivitas
                    </a>
                    <a href="{{ route('admin.settings.profile') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium text-[13px] transition-colors duration-150 {{ request()->routeIs('admin.settings.profile') ? 'bg-sky-50 text-sky-600 border-l-2 border-sky-600' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-800 border-l-2 border-transparent' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        Profil Admin
                    </a>
                </div>
            </div>
        </nav>

        {{-- Profil Admin (Bagian Bawah Sidebar) --}}
        <div class="p-4 mt-auto">
            <div class="bg-sky-50/50 rounded-xl p-3 flex items-center gap-3 border border-sky-100/50 shadow-sm relative group">
                <div class="w-9 h-9 rounded-full bg-sky-500 flex items-center justify-center text-white font-bold text-sm shrink-0 shadow-sm">
                    {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-slate-800 text-sm font-bold truncate">{{ auth()->user()->name ?? 'Admin' }}</p>
                    <p class="text-slate-500 text-[11px] truncate">{{ auth()->user()->email ?? 'admin@cvberkah.id' }}</p>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-slate-400 hover:text-red-500 hover:bg-red-50 transition-colors p-2 rounded-lg" title="Logout">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    {{-- MAIN CONTENT --}}
    <div class="flex-1 flex flex-col overflow-hidden md:ml-64 bg-slate-50">

        {{-- Top bar --}}
        <header class="bg-white border-b border-slate-200 px-6 py-4 flex items-center justify-between sticky top-0 z-30">
            <button @click="sidebarOpen = !sidebarOpen" class="md:hidden text-slate-500 hover:text-sky-600 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
            <div class="flex items-center gap-2 text-sm text-slate-500 ml-4 md:ml-0 font-medium">
                @yield('breadcrumb')
            </div>
            <div class="flex items-center gap-3 md:gap-4">
                <a href="{{ route('admin.warehouse.low-stock') }}" class="relative flex items-center p-2 rounded-full hover:bg-slate-50 transition">
                    @php $alertBadge = \App\Models\Product::whereRaw('stock <= low_stock_threshold')->count(); @endphp
                    <svg class="w-5 h-5 text-slate-500 hover:text-sky-600 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                    @if($alertBadge > 0)
                    <span class="absolute top-1 right-1 bg-red-500 text-white text-[10px] w-4 h-4 rounded-full flex items-center justify-center font-bold shadow-sm border border-white">{{ min($alertBadge, 9) }}</span>
                    @endif
                </a>
                <div class="w-px h-6 bg-slate-200 hidden md:block"></div>
                <a href="{{ route('home') }}" target="_blank" class="flex items-center gap-2 text-sm font-medium text-slate-600 hover:text-sky-700 transition bg-white hover:bg-sky-50 px-3 py-1.5 rounded-lg border border-slate-200 hover:border-sky-200" title="Lihat Situs Publik">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    <span class="hidden md:inline">Lihat Web</span>
                </a>
            </div>
        </header>

        {{-- Toast Notifications (Alpine.js) --}}
        @if(session('success') || session('error'))
        <div class="fixed top-4 right-4 z-50 flex flex-col gap-3 w-80">
            @if(session('success'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" 
                 x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-10" x-transition:enter-end="opacity-100 translate-x-0"
                 x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-x-0" x-transition:leave-end="opacity-0 translate-x-10"
                 class="bg-white border border-slate-200 shadow-lg rounded-lg p-4 flex items-start gap-3 relative overflow-hidden">
                <div class="absolute left-0 top-0 bottom-0 w-1 bg-emerald-500"></div>
                <svg class="w-5 h-5 text-emerald-500 shrink-0 mt-0.5 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <div class="flex-1">
                    <h4 class="text-sm font-bold text-slate-800">Berhasil</h4>
                    <p class="text-xs text-slate-500 mt-0.5">{{ session('success') }}</p>
                </div>
                <button @click="show = false" class="text-slate-400 hover:text-slate-600 transition">&times;</button>
            </div>
            @endif
            @if(session('error'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" 
                 x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-10" x-transition:enter-end="opacity-100 translate-x-0"
                 x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-x-0" x-transition:leave-end="opacity-0 translate-x-10"
                 class="bg-white border border-slate-200 shadow-lg rounded-lg p-4 flex items-start gap-3 relative overflow-hidden">
                <div class="absolute left-0 top-0 bottom-0 w-1 bg-red-500"></div>
                <svg class="w-5 h-5 text-red-500 shrink-0 mt-0.5 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <div class="flex-1">
                    <h4 class="text-sm font-bold text-slate-800">Perhatian</h4>
                    <p class="text-xs text-slate-500 mt-0.5">{{ session('error') }}</p>
                </div>
                <button @click="show = false" class="text-slate-400 hover:text-slate-600 transition">&times;</button>
            </div>
            @endif
        </div>
        @endif

        {{-- Page content --}}
        <main class="flex-1 overflow-y-auto p-6">
            <div class="max-w-7xl mx-auto">
                @yield('content')
            </div>
        </main>
    </div>
</div>

{{-- Mobile sidebar overlay --}}
<div x-show="sidebarOpen" @click="sidebarOpen = false"
     class="fixed inset-0 z-40 bg-slate-900/50 md:hidden backdrop-blur-sm" x-transition></div>

@stack('scripts')
</body>
</html>
