<x-app-layout>
    <x-slot name="title">Daftar Pengguna & Petugas</x-slot>

    <div class="space-y-6">
        <!-- Header & Stats -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl font-black text-slate-800 font-outfit tracking-tight">Daftar Pengguna & Petugas</h2>
                <p class="text-xs text-slate-500 font-medium mt-1">Kelola dan akses langsung (impersonate) akun Tenant, Petugas Gate, dan Petugas Redeem tanpa password.</p>
            </div>
        </div>

        @if(session('success'))
        <div class="bg-green-50 border border-green-200 rounded-2xl p-4 flex items-center gap-3 animate-in fade-in slide-in-from-top-2 duration-500">
            <div class="w-8 h-8 rounded-full bg-green-500 flex items-center justify-center text-white shrink-0 shadow-lg shadow-green-500/20">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
            </div>
            <div class="text-sm font-bold text-green-800">{{ session('success') }}</div>
        </div>
        @endif

        @if(session('error'))
        <div class="bg-rose-50 border border-rose-200 rounded-2xl p-4 flex items-center gap-3 animate-in fade-in slide-in-from-top-2 duration-500">
            <div class="w-8 h-8 rounded-full bg-rose-500 flex items-center justify-center text-white shrink-0 shadow-lg shadow-rose-500/20">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
            </div>
            <div class="text-sm font-bold text-rose-800">{{ session('error') }}</div>
        </div>
        @endif

        <!-- Stat Cards -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <a href="{{ route('superadmin.users.index') }}" class="p-4 rounded-2xl bg-white border {{ empty($role) ? 'border-orange-500 ring-2 ring-orange-500/10' : 'border-slate-100 hover:border-slate-200' }} shadow-sm transition-all group">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-black uppercase tracking-wider text-slate-400 group-hover:text-orange-600 transition-colors">Semua Akun</span>
                    <div class="w-8 h-8 rounded-xl bg-orange-50 text-orange-600 flex items-center justify-center font-bold text-xs">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                    </div>
                </div>
                <div class="text-2xl font-black text-slate-800 font-outfit mt-2">{{ $counts['all'] }}</div>
                <div class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-0.5">Total Pengguna</div>
            </a>

            <a href="{{ route('superadmin.users.index', ['role' => 'Penyedia Event']) }}" class="p-4 rounded-2xl bg-white border {{ $role === 'Penyedia Event' ? 'border-purple-500 ring-2 ring-purple-500/10' : 'border-slate-100 hover:border-slate-200' }} shadow-sm transition-all group">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-black uppercase tracking-wider text-slate-400 group-hover:text-purple-600 transition-colors">Akun Tenant</span>
                    <div class="w-8 h-8 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center font-bold text-xs">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                    </div>
                </div>
                <div class="text-2xl font-black text-purple-700 font-outfit mt-2">{{ $counts['tenant'] }}</div>
                <div class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-0.5">Penyedia Event</div>
            </a>

            <a href="{{ route('superadmin.users.index', ['role' => 'Petugas Gate']) }}" class="p-4 rounded-2xl bg-white border {{ $role === 'Petugas Gate' ? 'border-emerald-500 ring-2 ring-emerald-500/10' : 'border-slate-100 hover:border-slate-200' }} shadow-sm transition-all group">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-black uppercase tracking-wider text-slate-400 group-hover:text-emerald-600 transition-colors">Petugas Gate</span>
                    <div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-xs">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>
                    </div>
                </div>
                <div class="text-2xl font-black text-emerald-700 font-outfit mt-2">{{ $counts['gate'] }}</div>
                <div class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-0.5">Scanner Gate</div>
            </a>

            <a href="{{ route('superadmin.users.index', ['role' => 'Petugas Loket']) }}" class="p-4 rounded-2xl bg-white border {{ $role === 'Petugas Loket' ? 'border-blue-500 ring-2 ring-blue-500/10' : 'border-slate-100 hover:border-slate-200' }} shadow-sm transition-all group">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-black uppercase tracking-wider text-slate-400 group-hover:text-blue-600 transition-colors">Petugas Redeem</span>
                    <div class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-xs">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" /></svg>
                    </div>
                </div>
                <div class="text-2xl font-black text-blue-700 font-outfit mt-2">{{ $counts['redeem'] }}</div>
                <div class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-0.5">Petugas Loket / POS</div>
            </a>
        </div>

        <!-- Filter & Search Bar -->
        <div class="bg-white rounded-3xl p-5 shadow-sm border border-slate-100">
            <form method="GET" action="{{ route('superadmin.users.index') }}" class="flex flex-col md:flex-row gap-4 items-center justify-between">
                <div class="flex flex-1 flex-col sm:flex-row gap-3 w-full">
                    <div class="relative flex-1">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                        </div>
                        <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama, email, no. telp, atau tenant..." class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:bg-white transition-all">
                    </div>

                    <div class="w-full sm:w-48">
                        <select name="role" class="w-full py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-700 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:bg-white transition-all">
                            <option value="">Semua Peran</option>
                            <option value="Penyedia Event" {{ $role === 'Penyedia Event' ? 'selected' : '' }}>Penyedia Event (Tenant)</option>
                            <option value="Petugas Gate" {{ $role === 'Petugas Gate' ? 'selected' : '' }}>Petugas Gate</option>
                            <option value="Petugas Loket" {{ $role === 'Petugas Loket' ? 'selected' : '' }}>Petugas Loket (Redeem)</option>
                        </select>
                    </div>

                    <div class="w-full sm:w-56">
                        <select name="tenant_id" class="w-full py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-700 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:bg-white transition-all">
                            <option value="">Semua Tenant</option>
                            @foreach($tenants as $tenant)
                                <option value="{{ $tenant->id }}" {{ (string)$tenantId === (string)$tenant->id ? 'selected' : '' }}>{{ $tenant->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="flex items-center gap-2 w-full md:w-auto">
                    <button type="submit" class="w-full md:w-auto px-5 py-2.5 bg-orange-600 hover:bg-orange-700 text-white text-xs font-bold rounded-xl shadow-md shadow-orange-600/20 transition flex items-center justify-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" /></svg>
                        Filter
                    </button>
                    @if($search || $role || $tenantId)
                        <a href="{{ route('superadmin.users.index') }}" class="p-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs font-bold transition flex items-center justify-center" title="Reset Filter">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Users Table -->
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="overflow-x-auto custom-scrollbar">
                <table class="w-full text-left">
                    <thead class="bg-slate-50/75 border-b border-slate-100 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">
                        <tr>
                            <th class="px-6 py-4">Pengguna</th>
                            <th class="px-6 py-4">Peran (Role)</th>
                            <th class="px-6 py-4">Tenant / Organizer</th>
                            <th class="px-6 py-4">Status Akun</th>
                            <th class="px-6 py-4">Terdaftar</th>
                            <th class="px-6 py-4 text-right">Aksi Impersonasi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50 text-xs">
                        @forelse($users as $user)
                            @php
                                $roleName = $user->getRoleNames()->first() ?? 'Tidak ada peran';
                                
                                $badgeStyle = match($roleName) {
                                    'Penyedia Event' => 'bg-purple-50 text-purple-700 border-purple-200',
                                    'Petugas Gate' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                    'Petugas Loket' => 'bg-blue-50 text-blue-700 border-blue-200',
                                    default => 'bg-slate-50 text-slate-700 border-slate-200',
                                };

                                $roleIcon = match($roleName) {
                                    'Penyedia Event' => '<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>',
                                    'Petugas Gate' => '<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>',
                                    'Petugas Loket' => '<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" /></svg>',
                                    default => '<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>',
                                };
                            @endphp
                            <tr class="hover:bg-slate-50/75 transition-colors group">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-slate-100 to-slate-200 border border-slate-200 flex items-center justify-center font-black text-slate-700 text-sm shrink-0 shadow-inner">
                                            {{ substr($user->name, 0, 1) }}
                                        </div>
                                        <div class="min-w-0">
                                            <div class="font-extrabold text-slate-800 text-sm font-outfit truncate group-hover:text-orange-600 transition-colors">
                                                {{ $user->name }}
                                            </div>
                                            <div class="text-[11px] text-slate-400 font-medium truncate flex items-center gap-2 mt-0.5">
                                                <span>{{ $user->email }}</span>
                                                @if($user->phone)
                                                    <span class="text-slate-300">•</span>
                                                    <span>{{ $user->phone }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-[10px] font-black uppercase tracking-wider border {{ $badgeStyle }}">
                                        {!! $roleIcon !!}
                                        {{ $roleName }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    @if($user->tenant)
                                        <div class="flex items-center gap-2">
                                            <div class="w-6 h-6 rounded-lg bg-orange-50 border border-orange-100 text-orange-600 flex items-center justify-center font-bold text-[10px] shrink-0">
                                                {{ substr($user->tenant->name, 0, 1) }}
                                            </div>
                                            <div class="min-w-0">
                                                <div class="font-bold text-slate-800 truncate text-xs">{{ $user->tenant->name }}</div>
                                                <div class="text-[10px] text-slate-400">Status: <span class="capitalize font-semibold text-slate-600">{{ $user->tenant->status }}</span></div>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-slate-400 italic text-[11px]">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @if($user->is_active ?? true)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-green-100 text-green-700">
                                            <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                                            Aktif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600">
                                            <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                            Nonaktif
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-slate-500 text-[11px] font-medium">{{ $user->created_at ? $user->created_at->format('d M Y') : '-' }}</span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <form action="{{ route('impersonate.start', $user) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin login sebagai {{ addslashes($user->name) }} ({{ $roleName }})? Anda dapat kembali ke sesi Superadmin kapan saja dengan 1 klik.')">
                                        @csrf
                                        <button type="submit" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-orange-50 hover:bg-orange-600 text-orange-600 hover:text-white font-extrabold text-xs border border-orange-200 hover:border-orange-600 shadow-sm hover:shadow-md hover:shadow-orange-600/20 transition-all duration-200 group/btn">
                                            <svg class="w-4 h-4 text-orange-500 group-hover/btn:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                                            </svg>
                                            <span>Masuk Sebagai User</span>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-16 text-center">
                                    <div class="max-w-xs mx-auto text-slate-400">
                                        <svg class="w-12 h-12 mx-auto text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                                        <div class="font-black text-slate-600 text-sm font-outfit">Tidak Ada Pengguna Ditemukan</div>
                                        <p class="text-xs text-slate-400 mt-1">Coba sesuaikan kata kunci pencarian atau filter peran yang dipilih.</p>
                                        @if($search || $role || $tenantId)
                                            <a href="{{ route('superadmin.users.index') }}" class="inline-block mt-3 px-4 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-lg transition">
                                                Reset Filter
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($users->hasPages())
                <div class="px-6 py-4 bg-slate-50/50 border-t border-slate-100">
                    {{ $users->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
