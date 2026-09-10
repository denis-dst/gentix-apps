@if(session()->has('impersonator_id'))
    @php
        $impersonatedUser = auth()->user();
        $roleName = $impersonatedUser ? ($impersonatedUser->getRoleNames()->first() ?? 'User') : 'User';
        $tenantName = $impersonatedUser && $impersonatedUser->tenant ? $impersonatedUser->tenant->name : null;
    @endphp
    <div class="sticky top-0 z-[100] bg-gradient-to-r from-amber-600 via-orange-600 to-amber-700 text-white px-4 py-2.5 shadow-lg border-b border-white/20 transition-all duration-300">
        <div class="max-w-7xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-3 text-xs">
            <div class="flex items-center gap-2.5 min-w-0">
                <span class="flex h-2.5 w-2.5 relative shrink-0">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-yellow-300 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-yellow-200"></span>
                </span>
                <div class="font-bold flex items-center gap-1.5 flex-wrap">
                    <span class="uppercase tracking-wider px-2 py-0.5 rounded bg-black/25 text-[10px] font-black border border-white/20">
                        Mode Impersonasi
                    </span>
                    <span class="text-white/90">Anda sedang mengoperasikan akun:</span>
                    <span class="font-black text-white underline underline-offset-2">{{ $impersonatedUser->name }}</span>
                    <span class="px-1.5 py-0.5 rounded bg-white/20 text-[10px] font-semibold">
                        {{ $roleName }}
                    </span>
                    @if($tenantName)
                        <span class="text-white/80 font-normal">({{ $tenantName }})</span>
                    @endif
                </div>
            </div>
            
            <form action="{{ route('impersonate.leave') }}" method="POST" class="shrink-0 flex items-center">
                @csrf
                <button type="submit" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg bg-white text-orange-800 font-extrabold text-[11px] shadow-md hover:bg-orange-50 hover:text-orange-900 active:scale-95 transition-all">
                    <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    <span>Kembali ke Super Admin</span>
                </button>
            </form>
        </div>
    </div>
@endif
