<!DOCTYPE html>
<html lang="id" class="h-full bg-zinc-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - Invoice Maker</title>
    
    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Vite CSS & JS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            letter-spacing: -0.012em;
        }
    </style>
    @stack('styles')
</head>
<body class="h-full antialiased text-zinc-900 bg-zinc-50 flex flex-col md:flex-row min-h-screen">

    <!-- Mobile Header (no-print) -->
    <header class="no-print bg-zinc-950 border-b border-zinc-800 text-white p-4 flex items-center justify-between md:hidden shadow-sm">
        <div class="flex items-center space-x-3">
            <!-- Payment Receipt / Struk Pembayaran SVG Icon (Monochrome White Badge) -->
            <div class="w-9 h-9 rounded-xl bg-white text-zinc-950 flex items-center justify-center font-black text-xs shadow-sm border border-zinc-200">
                <svg class="w-5 h-5 text-zinc-950" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l2 2 4-4m-5 5h6M9 9h6m-6-3h6M5 3v18l2.5-1.5L10 21l2.5-1.5L15 21l2.5-1.5L20 21V3l-2.5 1.5L15 3l-2.5 1.5L10 3 7.5 4.5 5 3z"/>
                </svg>
            </div>
            <span class="font-extrabold text-base tracking-tight text-white">Invoice Maker</span>
        </div>
        <button x-data @click="$dispatch('toggle-mobile-sidebar')" class="p-2 text-zinc-400 hover:text-white rounded-lg focus:outline-none">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>
    </header>

    <!-- Sidebar Navigation (no-print) -->
    <aside x-data="{ open: false }" 
           @toggle-mobile-sidebar.window="open = !open"
           :class="open ? 'translate-x-0' : '-translate-x-full md:translate-x-0'"
           class="no-print fixed inset-y-0 left-0 z-40 w-64 bg-zinc-950 text-zinc-400 transform transition-transform duration-200 ease-in-out md:static md:translate-x-0 flex flex-col justify-between border-r border-zinc-800/80 shadow-2xl">
        
        <div>
            <!-- Sidebar Header / Receipt Struk Pembayaran Monogram Logo -->
            <div class="h-20 flex items-center px-6 border-b border-zinc-800/80">
                <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 group">
                    <div class="w-10 h-10 rounded-2xl bg-white text-zinc-950 flex items-center justify-center font-black shadow-md border border-zinc-200 group-hover:scale-105 group-hover:bg-zinc-100 transition-all">
                        <!-- Receipt / Struk Pembayaran Icon -->
                        <svg class="w-6 h-6 text-zinc-950" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l2 2 4-4m-5 5h6M9 9h6m-6-3h6M5 3v18l2.5-1.5L10 21l2.5-1.5L15 21l2.5-1.5L20 21V3l-2.5 1.5L15 3l-2.5 1.5L10 3 7.5 4.5 5 3z"/>
                        </svg>
                    </div>
                    <div>
                        <span class="font-black text-white text-base tracking-tight block leading-none">Invoice Maker</span>
                    </div>
                </a>
            </div>

            <!-- Navigation Links -->
            <nav class="p-4 space-y-1.5">
                <a href="{{ route('dashboard') }}" 
                   class="flex items-center space-x-3 px-4 py-3 rounded-2xl text-xs font-extrabold transition-all duration-150 {{ request()->routeIs('dashboard') ? 'bg-white text-zinc-950 shadow-sm' : 'hover:bg-zinc-900 text-zinc-400 hover:text-white' }}">
                    <svg class="w-4 h-4 opacity-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    <span>Dashboard</span>
                </a>

                <a href="{{ route('invoices.index') }}" 
                   class="flex items-center space-x-3 px-4 py-3 rounded-2xl text-xs font-extrabold transition-all duration-150 {{ request()->routeIs('invoices.*') ? 'bg-white text-zinc-950 shadow-sm' : 'hover:bg-zinc-900 text-zinc-400 hover:text-white' }}">
                    <svg class="w-4 h-4 opacity-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l2 2 4-4m-5 5h6M9 9h6m-6-3h6M5 3v18l2.5-1.5L10 21l2.5-1.5L15 21l2.5-1.5L20 21V3l-2.5 1.5L15 3l-2.5 1.5L10 3 7.5 4.5 5 3z"/>
                    </svg>
                    <span>Invoices</span>
                </a>

                <a href="{{ route('customers.index') }}" 
                   class="flex items-center space-x-3 px-4 py-3 rounded-2xl text-xs font-extrabold transition-all duration-150 {{ request()->routeIs('customers.*') ? 'bg-white text-zinc-950 shadow-sm' : 'hover:bg-zinc-900 text-zinc-400 hover:text-white' }}">
                    <svg class="w-4 h-4 opacity-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <span>Customers</span>
                </a>

                <a href="{{ route('settings.index') }}" 
                   class="flex items-center space-x-3 px-4 py-3 rounded-2xl text-xs font-extrabold transition-all duration-150 {{ request()->routeIs('settings.*') ? 'bg-white text-zinc-950 shadow-sm' : 'hover:bg-zinc-900 text-zinc-400 hover:text-white' }}">
                    <svg class="w-4 h-4 opacity-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <span>Settings</span>
                </a>
            </nav>
        </div>

        <!-- Sidebar Footer -->
        <div class="p-4 border-t border-zinc-900">
            <div class="bg-zinc-900/90 rounded-2xl p-3 flex items-center space-x-3 border border-zinc-800/80">
                <div class="w-8 h-8 rounded-xl bg-gradient-to-tr from-zinc-800 to-zinc-700 text-white flex items-center justify-center font-bold text-xs border border-zinc-600">
                    {{ strtoupper(substr(\App\Models\Setting::get('business_name', 'B'), 0, 1)) }}
                </div>
                <div class="overflow-hidden">
                    <p class="text-xs font-bold text-white truncate">{{ \App\Models\Setting::get('business_name', 'Bisnis Anda') }}</p>
                    <p class="text-[10px] text-zinc-400 truncate">{{ \App\Models\Setting::get('email', 'admin@invoice.com') }}</p>
                </div>
            </div>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col min-w-0 overflow-y-auto">
        <!-- Topbar (no-print) -->
        <header class="no-print bg-white border-b border-zinc-200 h-16 flex items-center justify-between px-6 sticky top-0 z-30 shadow-xs">
            <div>
                <h1 class="text-base font-black text-zinc-950 tracking-tight">@yield('page_title', 'Dashboard')</h1>
            </div>
            
            <div class="flex items-center space-x-3">
                <a href="{{ route('invoices.create') }}" 
                   class="inline-flex items-center justify-center space-x-2 bg-gradient-to-r from-zinc-950 via-zinc-900 to-zinc-950 hover:scale-[1.02] text-white font-bold px-4.5 py-2.5 rounded-2xl text-xs shadow-sm transition-all border border-zinc-800">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span>Buat Invoice</span>
                </a>
            </div>
        </header>

        <!-- Main Body -->
        <main class="flex-1 p-6 md:p-8">
            <!-- Alert Notifications (no-print) -->
            @if(session('success'))
                <div class="no-print mb-6 bg-zinc-950 text-white border border-zinc-800 rounded-2xl p-4 flex items-start space-x-3 shadow-md" role="alert">
                    <div class="w-5 h-5 rounded-full bg-emerald-500 text-zinc-950 flex items-center justify-center shrink-0 font-bold text-xs mt-0.5">
                        <svg class="w-3.5 h-3.5 text-zinc-950" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h4 class="font-extrabold text-xs uppercase tracking-wider text-zinc-300">Berhasil</h4>
                        <p class="text-xs mt-0.5 font-medium text-white">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="no-print mb-6 bg-rose-950 border border-rose-800 text-white rounded-2xl p-4 flex items-start space-x-3 shadow-md" role="alert">
                    <div class="w-5 h-5 rounded-full bg-rose-500 text-white flex items-center justify-center shrink-0 font-bold text-xs mt-0.5">
                        ✕
                    </div>
                    <div class="flex-1">
                        <h4 class="font-extrabold text-xs uppercase tracking-wider text-rose-300">Terjadi Kesalahan</h4>
                        <p class="text-xs mt-0.5 font-medium">{{ session('error') }}</p>
                    </div>
                </div>
            @endif

            @if ($errors->any())
                <div class="no-print mb-6 bg-zinc-950 border border-zinc-800 text-white rounded-2xl p-4 shadow-md" role="alert">
                    <div class="flex items-center space-x-2 font-bold text-xs uppercase tracking-wider text-zinc-400 mb-2">
                        <span>Mohon periksa kembali input form Anda:</span>
                    </div>
                    <ul class="list-disc list-inside text-xs space-y-1 text-zinc-300">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    @stack('scripts')
</body>
</html>
