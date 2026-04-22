<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>GenTix SuperAdmin - {{ $title ?? 'Dashboard' }}</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        .sidebar-item-active {
            background: linear-gradient(135deg, rgba(124, 58, 237, 0.1), rgba(124, 58, 237, 0.05));
            border-left: 4px solid #7c3aed;
            color: #7c3aed;
        }
        .font-outfit { font-family: 'Outfit', sans-serif; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 dark:bg-[#0B0F1A] text-slate-900 dark:text-slate-100">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <aside class="w-64 bg-white dark:bg-slate-900 border-r border-black/5 dark:border-white/5 flex flex-col">
            <div class="p-6">
                <a href="{{ route('superadmin.dashboard') }}" class="flex items-center gap-2">
                    <div class="w-8 h-8 bg-gentix-600 rounded-lg flex items-center justify-center shadow-lg">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                        </svg>
                    </div>
                    <span class="text-xl font-bold font-outfit uppercase">Gen<span class="text-gentix-600">Tix</span></span>
                </a>
            </div>
            
            <nav class="flex-1 px-4 space-y-2 mt-4">
                <a href="{{ route('superadmin.dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition {{ request()->routeIs('superadmin.dashboard') ? 'sidebar-item-active' : 'hover:bg-slate-50 dark:hover:bg-white/5' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" /></svg>
                    <span class="font-semibold text-sm">Dashboard</span>
                </a>
                
                <div class="pt-4 pb-2 px-4 text-xs font-bold uppercase tracking-widest text-slate-400">Management</div>
                
                <a href="{{ route('superadmin.tenants.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition {{ request()->routeIs('superadmin.tenants.*') ? 'sidebar-item-active' : 'hover:bg-slate-50 dark:hover:bg-white/5' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                    <span class="font-semibold text-sm">Tenants / Organizers</span>
                </a>
                
                <a href="{{ route('superadmin.events.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition {{ request()->routeIs('superadmin.events.*') ? 'sidebar-item-active' : 'hover:bg-slate-50 dark:hover:bg-white/5' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                    <span class="font-semibold text-sm">All Events</span>
                </a>

                <a href="{{ route('superadmin.transactions.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition {{ request()->routeIs('superadmin.transactions.*') ? 'sidebar-item-active' : 'hover:bg-slate-50 dark:hover:bg-white/5' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                    <span class="font-semibold text-sm">Transactions</span>
                </a>
                
                <div class="pt-4 pb-2 px-4 text-xs font-bold uppercase tracking-widest text-slate-400">Settings</div>
                
                <a href="{{ route('superadmin.settings.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition {{ request()->routeIs('superadmin.settings.*') ? 'sidebar-item-active' : 'hover:bg-slate-50 dark:hover:bg-white/5' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                    <span class="font-semibold text-sm">Site Settings</span>
                </a>
            </nav>
            
            <div class="p-4 border-t border-black/5 dark:border-white/5">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-rose-50 dark:hover:bg-rose-500/10 text-rose-600 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4-4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
                        <span class="font-semibold text-sm">Logout</span>
                    </button>
                </form>
            </div>
        </aside>
        
        <!-- Main Content -->
        <main class="flex-1 flex flex-col min-w-0">
            <!-- Header -->
            <header class="h-20 bg-white dark:bg-slate-900 border-b border-black/5 dark:border-white/5 flex items-center justify-between px-8">
                <h1 class="text-xl font-bold font-outfit">{{ $title ?? 'Dashboard' }}</h1>
                <div class="flex items-center gap-4">
                    <div class="text-right hidden sm:block">
                        <div class="text-sm font-bold">{{ Auth::user()->name }}</div>
                        <div class="text-xs text-slate-400 uppercase tracking-widest">{{ Auth::user()->getRoleNames()->first() }}</div>
                    </div>
                    <div class="w-10 h-10 rounded-full bg-slate-200 dark:bg-white/5 flex items-center justify-center font-bold text-gentix-600 border border-black/5">
                        {{ substr(Auth::user()->name, 0, 1) }}
                    </div>
                </div>
            </header>
            
            <!-- Content Area -->
            <div class="p-8 flex-1 overflow-y-auto">
                {{ $slot }}
            </div>
        </main>
    </div>
</body>
</html>
