<div class="px-6 py-6 border-b border-white/5 flex items-center gap-3 bg-[#0f172a]/50 shrink-0">
    <div class="w-10 h-10 rounded-full bg-slate-800 flex items-center justify-center text-white font-bold border border-white/10 overflow-hidden shadow-inner">
        @if(Auth::user()->avatar)
            <img src="{{ asset('storage/' . Auth::user()->avatar) }}" alt="{{ Auth::user()->name }}" class="w-full h-full object-cover">
        @else
            {{ substr(Auth::user()->name, 0, 1) }}
        @endif
    </div>
    <div class="flex flex-col min-w-0">
        <span class="text-sm font-bold text-white truncate leading-tight">{{ Auth::user()->name }}</span>
        <span class="text-[10px] uppercase font-bold text-slate-500 tracking-widest mt-1">Online</span>
    </div>
</div>

<nav class="flex-1 mt-4 overflow-y-auto px-4 space-y-1 custom-scrollbar pb-6">
    @if(auth()->user()->hasRole('Superadmin'))
        <div class="px-4 py-2 text-[10px] font-black uppercase text-slate-500 tracking-[0.2em]">Dashboard</div>
        <a href="{{ route('superadmin.dashboard') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition {{ request()->routeIs('superadmin.dashboard') ? 'bg-orange-600 text-white shadow-lg' : 'hover:bg-white/5 hover:text-white' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" /></svg>
            <span class="text-sm font-bold">Dashboard</span>
        </a>
        
        <div class="pt-4 px-4 py-2 text-[10px] font-black uppercase text-slate-500 tracking-[0.2em]">Sistem</div>
        <a href="{{ route('superadmin.tenants.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition {{ request()->routeIs('superadmin.tenants.*') ? 'bg-orange-600 text-white shadow-lg' : 'hover:bg-white/5 hover:text-white' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
            <span class="text-sm font-bold">Organizers</span>
        </a>
        <a href="{{ route('superadmin.events.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition {{ request()->routeIs('superadmin.events.*') ? 'bg-orange-600 text-white shadow-lg' : 'hover:bg-white/5 hover:text-white' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
            <span class="text-sm font-bold">Global Events</span>
        </a>
        <a href="{{ route('superadmin.reports.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition {{ request()->routeIs('superadmin.reports.*') ? 'bg-orange-600 text-white shadow-lg' : 'hover:bg-white/5 hover:text-white' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-6a2 2 0 012-2h2a2 2 0 012 2v6m-6 0h6m-9 4h12a2 2 0 002-2V7a2 2 0 00-2-2h-3.5L14 3h-4L7.5 5H4a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
            <span class="text-sm font-bold">Laporan</span>
        </a>
        <a href="{{ route('superadmin.transactions.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition {{ request()->routeIs('superadmin.transactions.*') ? 'bg-orange-600 text-white shadow-lg' : 'hover:bg-white/5 hover:text-white' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.407 2.625 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.407-2.625-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            <span class="text-sm font-bold">Laporan Penjualan</span>
        </a>
        <a href="{{ route('superadmin.settings.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition {{ request()->routeIs('superadmin.settings.*') ? 'bg-orange-600 text-white shadow-lg' : 'hover:bg-white/5 hover:text-white' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
            <span class="text-sm font-bold">Pengaturan</span>
        </a>

    @elseif(auth()->user()->hasRole('Penyedia Event'))
        <div class="px-4 py-2 text-[10px] font-black uppercase text-slate-500 tracking-[0.2em]">Dashboard</div>
        <a href="{{ route('organizer.dashboard') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition {{ request()->routeIs('organizer.dashboard') ? 'bg-orange-600 text-white shadow-lg' : 'hover:bg-white/5 hover:text-white' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>
            <span class="text-sm font-bold">Dashboard</span>
        </a>

        <div class="pt-4 px-4 py-2 text-[10px] font-black uppercase text-slate-500 tracking-[0.2em]">Manajemen</div>
        <a href="{{ route('organizer.events.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition {{ request()->routeIs('organizer.events.*') ? 'bg-orange-600 text-white shadow-lg' : 'hover:bg-white/5 hover:text-white' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
            <span class="text-sm font-bold">Events</span>
        </a>
        <a href="{{ route('organizer.vouchers.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition {{ request()->routeIs('organizer.vouchers.*') ? 'bg-orange-600 text-white shadow-lg' : 'hover:bg-white/5 hover:text-white' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" /></svg>
            <span class="text-sm font-bold">Vouchers</span>
        </a>
        <a href="{{ route('organizer.reports.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition {{ request()->routeIs('organizer.reports.index') ? 'bg-orange-600 text-white shadow-lg' : 'hover:bg-white/5 hover:text-white' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-6a2 2 0 012-2h2a2 2 0 012 2v6m-6 0h6m-9 4h12a2 2 0 002-2V7a2 2 0 00-2-2h-3.5L14 3h-4L7.5 5H4a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
            <span class="text-sm font-bold">Laporan</span>
        </a>
        <a href="{{ route('organizer.reports.duplicates') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition {{ request()->routeIs('organizer.reports.duplicates') ? 'bg-orange-600 text-white shadow-lg' : 'hover:bg-white/5 hover:text-white' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
            <span class="text-sm font-bold">Check Pendaftar Ganda</span>
        </a>
        <a href="{{ route('organizer.transactions.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition {{ request()->routeIs('organizer.transactions.*') ? 'bg-orange-600 text-white shadow-lg' : 'hover:bg-white/5 hover:text-white' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.407 2.625 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.407-2.625-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            <span class="text-sm font-bold">Laporan Penjualan</span>
        </a>
        <a href="{{ route('organizer.crews.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition {{ request()->routeIs('organizer.crews.*') ? 'bg-orange-600 text-white shadow-lg' : 'hover:bg-white/5 hover:text-white' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
            <span class="text-sm font-bold">Manajemen Crew</span>
        </a>
        <a href="{{ route('organizer.settings.terms') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition {{ request()->routeIs('organizer.settings.*') ? 'bg-orange-600 text-white shadow-lg' : 'hover:bg-white/5 hover:text-white' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
            <span class="text-sm font-bold">Pengaturan Tenant</span>
        </a>
    @elseif(auth()->user()->hasRole('Petugas Loket'))
        <div class="px-4 py-2 text-[10px] font-black uppercase text-slate-500 tracking-[0.2em]">Operasional</div>
        <a href="{{ route('organizer.checkin.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition {{ request()->routeIs('organizer.checkin.*') ? 'bg-orange-600 text-white shadow-lg' : 'hover:bg-white/5 hover:text-white' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" /></svg>
            <span class="text-sm font-bold">Check-in</span>
        </a>
    @elseif(auth()->user()->hasRole('Petugas Gate'))
        <div class="px-4 py-2 text-[10px] font-black uppercase text-slate-500 tracking-[0.2em]">Gate System</div>
        <a href="{{ route('organizer.gate.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition {{ request()->routeIs('organizer.gate.*') ? 'bg-orange-600 text-white shadow-lg' : 'hover:bg-white/5 hover:text-white' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" /></svg>
            <span class="text-sm font-bold">Gate Scan</span>
        </a>
    @endif
</nav>

<div class="p-6 border-t border-white/5 bg-[#0a0f1d] shrink-0">
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="w-full flex items-center justify-center gap-3 px-4 py-3 text-sm font-black text-black bg-orange-600 hover:bg-orange-700 rounded-xl transition-all shadow-lg shadow-orange-500/20 uppercase tracking-widest">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4-4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
            <span>KELUAR</span>
        </button>
    </form>
</div>
