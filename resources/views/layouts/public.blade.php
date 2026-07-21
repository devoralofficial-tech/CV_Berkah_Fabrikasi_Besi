<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'CV Berkah — Penjualan Besi Terpercaya')</title>
    <meta name="description" content="@yield('meta_description', 'CV Berkah menyediakan berbagai jenis besi dan pipa stainless berkualitas. Pesan mudah, langsung via WhatsApp.')">

    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600&display=swap" rel="stylesheet">

    {{-- Vite Assets (Tailwind CSS) --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Alpine.js --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        h1, h2, h3, h4, h5 { font-family: 'Sora', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 text-slate-700 antialiased">

    {{-- NAVBAR --}}
    <nav class="bg-slate-900 shadow-lg sticky top-0 z-50" x-data="{ mobileOpen: false }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                {{-- Logo --}}
                <a href="{{ route('home') }}" class="hidden md:flex items-center gap-2">
                    <div class="w-8 h-8 bg-amber-500 rounded-sm flex items-center justify-center">
                        <svg class="w-5 h-5 text-slate-900" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
                    </div>
                    <span class="text-white font-bold text-lg tracking-tight" style="font-family: 'Sora', sans-serif;">CV BERKAH</span>
                </a>

                {{-- Desktop Nav --}}
                <div class="hidden md:flex items-center gap-6">
                    <a href="{{ route('home') }}" class="text-slate-300 hover:text-amber-400 transition duration-200 font-medium text-sm {{ request()->routeIs('home') ? 'text-amber-400' : '' }}">Beranda</a>
                    <a href="{{ route('product.index') }}" class="text-slate-300 hover:text-amber-400 transition duration-200 font-medium text-sm {{ request()->routeIs('product.*') ? 'text-amber-400' : '' }}">Produk</a>
                    <a href="{{ route('about') }}" class="text-slate-300 hover:text-amber-400 transition duration-200 font-medium text-sm {{ request()->routeIs('about') ? 'text-amber-400' : '' }}">Tentang Kami</a>

                    {{-- Cart --}}
                    <a href="{{ route('cart.index') }}" class="relative flex items-center gap-1.5 bg-amber-500 hover:bg-amber-400 text-slate-900 font-semibold text-sm px-4 py-2 rounded-lg transition duration-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        Keranjang
                        @php $cartCount = count(session('cart', [])); @endphp
                        @if($cartCount > 0)
                        <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs font-bold w-5 h-5 rounded-full flex items-center justify-center">{{ $cartCount }}</span>
                        @endif
                    </a>
                </div>

                {{-- Mobile Nav Top --}}
                <div class="flex md:hidden items-center justify-between w-full">
                    <a href="{{ route('home') }}" class="flex items-center gap-2">
                        <div class="w-7 h-7 bg-amber-500 rounded-sm flex items-center justify-center">
                            <svg class="w-4 h-4 text-slate-900" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
                        </div>
                        <span class="text-white font-bold text-base tracking-tight" style="font-family: 'Sora', sans-serif;">CV BERKAH</span>
                    </a>
                    
                    <div class="flex items-center gap-4">
                        @php $cartCount = count(session('cart', [])); @endphp
                        <a href="{{ route('cart.index') }}" class="relative text-slate-300 hover:text-white">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            @if($cartCount > 0)
                            <span class="absolute -top-1 -right-1 bg-red-500 text-white text-[10px] font-bold w-4 h-4 rounded-full flex items-center justify-center">{{ $cartCount }}</span>
                            @endif
                        </a>
                        <button @click="mobileOpen = !mobileOpen" class="text-slate-300 hover:text-white">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path x-show="!mobileOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                                <path x-show="mobileOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Mobile Menu --}}
        <div x-show="mobileOpen" x-transition class="md:hidden bg-slate-800 border-t border-slate-700 px-4 py-4 space-y-3">
            <a href="{{ route('home') }}" class="block text-slate-300 hover:text-amber-400 font-medium py-1">Beranda</a>
            <a href="{{ route('product.index') }}" class="block text-slate-300 hover:text-amber-400 font-medium py-1">Produk</a>
            <a href="{{ route('about') }}" class="block text-slate-300 hover:text-amber-400 font-medium py-1">Tentang Kami</a>
            <a href="{{ route('cart.index') }}" class="flex items-center gap-2 bg-amber-500 text-slate-900 font-semibold px-4 py-2 rounded-lg w-full justify-center mt-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                Keranjang ({{ count(session('cart', [])) }})
            </a>
        </div>
    </nav>

    {{-- Flash Messages --}}
    @if(session('success') || session('error'))
    <div x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 4000)"
         class="fixed top-20 right-4 z-50 max-w-sm">
        @if(session('success'))
        <div class="bg-emerald-500 text-white px-5 py-3 rounded-lg shadow-lg flex items-center gap-2">
            <svg class="w-5 h-5 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            {{ session('success') }}
        </div>
        @endif
        @if(session('error'))
        <div class="bg-red-500 text-white px-5 py-3 rounded-lg shadow-lg flex items-center gap-2">
            <svg class="w-5 h-5 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
            {{ session('error') }}
        </div>
        @endif
    </div>
    @endif

    {{-- Page Content --}}
    <main class="pb-16 md:pb-0">
        @yield('content')
    </main>

    {{-- FOOTER --}}
    @php $footerSetting = \App\Models\Setting::getSetting(); @endphp
    <footer class="bg-slate-900 text-slate-400 mt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <div class="flex items-center gap-2 mb-4">
                        <div class="w-7 h-7 bg-amber-500 rounded-sm flex items-center justify-center">
                            <svg class="w-4 h-4 text-slate-900" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2L2 7l10 5 10-5-10-5z"/></svg>
                        </div>
                        <span class="text-white font-bold" style="font-family: 'Sora', sans-serif;">CV BERKAH</span>
                    </div>
                    <p class="text-sm leading-relaxed">{{ $footerSetting->company_description ?? 'Penyedia besi dan logam berkualitas terpercaya.' }}</p>
                </div>
                <div>
                    <h4 class="text-white font-semibold mb-4 text-sm uppercase tracking-wider">Kontak</h4>
                    <ul class="space-y-2 text-sm">
                        <li class="flex items-start gap-2"><svg class="w-4 h-4 text-amber-500 mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/></svg>{{ $footerSetting->address ?? '-' }}</li>
                        <li class="flex items-center gap-2"><svg class="w-4 h-4 text-amber-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/><path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/></svg>{{ $footerSetting->email ?? '-' }}</li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-white font-semibold mb-4 text-sm uppercase tracking-wider">Jam Operasional</h4>
                    <p class="text-sm">{{ $footerSetting->operating_hours ?? 'Senin–Sabtu, 08.00–17.00' }}</p>
                    @if($footerSetting->wa_number)
                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $footerSetting->wa_number) }}"
                       target="_blank"
                       class="inline-flex items-center gap-2 mt-4 bg-emerald-600 hover:bg-emerald-500 text-white px-4 py-2 rounded-lg text-sm font-medium transition duration-200">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                        Chat WhatsApp
                    </a>
                    @endif
                </div>
            </div>
            <div class="border-t border-slate-800 mt-8 pt-6 text-center text-xs">
                <p>&copy; {{ date('Y') }} CV Berkah. Seluruh hak cipta dilindungi.</p>
            </div>
        </div>
    </footer>

    {{-- Bottom Navigation Bar for Mobile --}}
    <div class="md:hidden fixed bottom-0 left-0 right-0 bg-white border-t border-slate-200 z-40 flex justify-around items-center h-16 pb-env-safe shadow-[0_-2px_10px_rgba(0,0,0,0.05)]">
        <a href="{{ route('home') }}" class="flex flex-col items-center justify-center w-full h-full space-y-1 {{ request()->routeIs('home') ? 'text-amber-500' : 'text-slate-500' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            <span class="text-[10px] font-medium">Beranda</span>
        </a>
        <a href="{{ route('product.index') }}" class="flex flex-col items-center justify-center w-full h-full space-y-1 {{ request()->routeIs('product.*') ? 'text-amber-500' : 'text-slate-500' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
            <span class="text-[10px] font-medium">Produk</span>
        </a>
        @php $footerSetting = \App\Models\Setting::getSetting(); @endphp
        <a href="{{ $footerSetting->wa_number ? 'https://wa.me/'.preg_replace('/[^0-9]/', '', $footerSetting->wa_number) : '#' }}" target="_blank" class="flex flex-col items-center justify-center w-full h-full space-y-1 text-slate-500">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
            <span class="text-[10px] font-medium">Chat CS</span>
        </a>
        <a href="{{ route('cart.index') }}" class="relative flex flex-col items-center justify-center w-full h-full space-y-1 {{ request()->routeIs('cart.*') ? 'text-amber-500' : 'text-slate-500' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            <span class="text-[10px] font-medium">Keranjang</span>
            @php $cartCount = count(session('cart', [])); @endphp
            @if($cartCount > 0)
            <span class="absolute top-1 right-2 bg-red-500 text-white text-[9px] font-bold w-3.5 h-3.5 rounded-full flex items-center justify-center">{{ $cartCount }}</span>
            @endif
        </a>
    </div>

    {{-- Chatbot Widget --}}
    <x-chatbot-widget />

    @stack('scripts')
</body>
</html>
