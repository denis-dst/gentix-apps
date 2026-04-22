<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>{{ config('app.name', 'GenTix') }} - {{ $title ?? 'Dashboard' }}</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        :root {
            --gentix-600: #7c3aed;
        }
        .sidebar-item-active {
            background: linear-gradient(135deg, rgba(124, 58, 237, 0.1), rgba(124, 58, 237, 0.05));
            border-left: 4px solid #7c3aed;
            color: #7c3aed;
        }
        .font-outfit { font-family: 'Outfit', sans-serif; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .glass {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .dark .glass {
            background: rgba(15, 23, 42, 0.8);
            border: 1px solid rgba(255, 255, 255, 0.05);
        }
    </style>
</head>
<body class="bg-gray-100 text-gray-900 antialiased font-sans" x-data="{ sidebarOpen: true }">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <aside 
            x-show="sidebarOpen"
            class="w-64 bg-[#0f172a] text-slate-300 flex-shrink-0 flex flex-col transition-all duration-300 ease-in-out z-50">
            
            <!-- Brand Logo -->
            <div class="h-16 flex items-center px-6 bg-[#0a0f1d] border-b border-[#1e293b]">
                <a href="/" class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-purple-600 rounded-lg flex items-center justify-center shadow-lg">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                        </svg>
                    </div>
                    <span class="text-xl font-bold text-white tracking-tight">Gen<span class="text-purple-500">Tix</span> Admin</span>
                </a>
            </div>

            <!-- User Panel -->
            <div class="px-6 py-6 border-b border-[#1e293b] flex items-center gap-4 bg-[#0f172a]/50">
                <div class="w-10 h-10 rounded-full bg-[#1e293b] flex items-center justify-center text-white font-bold border border-[#334155] overflow-hidden">
                    @if(Auth::user()->avatar)
                        <img src="{{ asset('storage/' . Auth::user()->avatar) }}" alt="{{ Auth::user()->name }}" class="w-full h-full object-cover">
                    @else
                        {{ substr(Auth::user()->name, 0, 1) }}
                    @endif
                </div>
                <div class="flex flex-col">
                    <span class="text-sm font-bold text-white leading-tight">{{ Auth::user()->name }}</span>
                    <span class="text-[10px] uppercase font-bold text-slate-500 tracking-wider">Online</span>
                </div>
            </div>

            <!-- Sidebar Menu -->
            <nav class="flex-1 mt-4 overflow-y-auto px-3 space-y-1">
                <div class="px-3 py-2 text-[10px] font-bold uppercase text-slate-500 tracking-[0.2em]">General</div>
                
                @if(auth()->user()->hasRole('superadmin'))
                    <a href="{{ route('superadmin.dashboard') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition {{ request()->routeIs('superadmin.dashboard') ? 'bg-purple-600 text-white shadow-lg' : 'hover:bg-[#1e293b] hover:text-white' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>
                        <span class="text-sm font-medium">Dashboard</span>
                    </a>
                    
                    <div class="pt-4 px-3 py-2 text-[10px] font-bold uppercase text-slate-500 tracking-[0.2em]">Management</div>
                    <a href="{{ route('superadmin.tenants.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition {{ request()->routeIs('superadmin.tenants.*') ? 'bg-purple-600 text-white shadow-lg' : 'hover:bg-[#1e293b] hover:text-white' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                        <span class="text-sm font-medium">Organizers</span>
                    </a>
                    <a href="{{ route('superadmin.events.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition {{ request()->routeIs('superadmin.events.*') ? 'bg-purple-600 text-white shadow-lg' : 'hover:bg-[#1e293b] hover:text-white' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                        <span class="text-sm font-medium">Global Events</span>
                    </a>
                    <a href="{{ route('superadmin.settings.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition {{ request()->routeIs('superadmin.settings.*') ? 'bg-purple-600 text-white shadow-lg' : 'hover:bg-[#1e293b] hover:text-white' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                        <span class="text-sm font-medium">Site Settings</span>
                    </a>

                    <div class="pt-4 px-3 py-2 text-[10px] font-bold uppercase text-slate-500 tracking-[0.2em]">Trash</div>
                    <a href="{{ route('superadmin.tenants.trash') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition {{ request()->routeIs('superadmin.tenants.trash') ? 'bg-red-600 text-white shadow-lg' : 'hover:bg-[#1e293b] hover:text-white' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                        <span class="text-sm font-medium">Deleted Organizers</span>
                    </a>
                    <a href="{{ route('superadmin.events.trash') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition {{ request()->routeIs('superadmin.events.trash') ? 'bg-red-600 text-white shadow-lg' : 'hover:bg-[#1e293b] hover:text-white' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2-2v12a2 2 0 002 2z" /></svg>
                        <span class="text-sm font-medium">Deleted Events</span>
                    </a>
                @else
                    <a href="{{ route('organizer.dashboard') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition {{ request()->routeIs('organizer.dashboard') ? 'bg-purple-600 text-white shadow-lg' : 'hover:bg-[#1e293b] hover:text-white' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>
                        <span class="text-sm font-medium">Dashboard</span>
                    </a>
                @endif
            </nav>

            <!-- Logout -->
            <div class="p-4 bg-[#0a0f1d] border-t border-[#1e293b]">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 text-sm text-slate-400 hover:text-white hover:bg-[#1e293b] rounded-lg transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4-4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
                        <span class="font-bold">Sign Out</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col min-w-0 bg-slate-100 h-screen overflow-y-auto">
            <!-- Navbar -->
            <nav class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-8 flex-shrink-0 sticky top-0 z-40 shadow-sm">
                <button @click="sidebarOpen = !sidebarOpen" class="p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-lg transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /></svg>
                </button>
                <div class="relative" x-data="{ profileOpen: false }">
                    <button @click="profileOpen = !profileOpen" @click.away="profileOpen = false" class="flex items-center gap-3 hover:bg-slate-50 p-1.5 rounded-xl transition">
                        <div class="text-right hidden md:block">
                            <div class="text-sm font-bold text-slate-800 leading-tight">{{ Auth::user()->name }}</div>
                            <div class="text-[10px] text-slate-500 font-medium uppercase tracking-widest">{{ Auth::user()->getRoleNames()->first() ?? 'User' }}</div>
                        </div>
                        <div class="w-10 h-10 rounded-lg bg-indigo-100 text-indigo-600 flex items-center justify-center font-bold text-lg border border-indigo-200 shadow-sm overflow-hidden">
                            @if(Auth::user()->avatar)
                                <img src="{{ asset('storage/' . Auth::user()->avatar) }}" alt="{{ Auth::user()->name }}" class="w-full h-full object-cover">
                            @else
                                {{ substr(Auth::user()->name, 0, 1) }}
                            @endif
                        </div>
                    </button>
                    
                    <!-- Dropdown -->
                    <div x-show="profileOpen" 
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="transform opacity-0 scale-95"
                         x-transition:enter-end="transform opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="transform opacity-100 scale-100"
                         x-transition:leave-end="transform opacity-0 scale-95"
                         class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-slate-100 overflow-hidden z-50 py-1"
                         style="display: none;">
                        <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50 hover:text-indigo-600 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                            My Profile
                        </a>
                        <div class="border-t border-slate-100 my-1"></div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full flex items-center gap-2 px-4 py-2.5 text-sm font-medium text-red-600 hover:bg-red-50 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4-4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
                                Sign Out
                            </button>
                        </form>
                    </div>
                </div>
            </nav>

            <!-- Page Header -->
            <div class="bg-white border-b border-slate-200 px-8 py-6 mb-8">
                <div class="flex items-center justify-between max-w-[1600px] mx-auto">
                    <h1 class="text-3xl font-bold text-slate-900">{{ $header ?? $title ?? 'Dashboard' }}</h1>
                    <div class="text-sm text-slate-500 font-medium">
                        Admin / {{ $header ?? $title ?? 'Dashboard' }}
                    </div>
                </div>
            </div>

            <!-- Content Area -->
            <main class="px-8 flex-1 max-w-[1600px] w-full mx-auto pb-12">
                {{ $slot }}
            </main>
        </div>
    </div>
</body>
</html>
