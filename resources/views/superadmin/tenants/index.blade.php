<x-app-layout>
    <x-slot name="title">Manajemen Organizer</x-slot>

    @if(session('success'))
    <div class="mb-8 bg-green-50 border border-green-200 rounded-2xl p-4 flex items-center gap-3 animate-in fade-in slide-in-from-top-2 duration-500">
        <div class="w-8 h-8 rounded-full bg-green-500 flex items-center justify-center text-white shrink-0 shadow-lg shadow-green-500/20">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
        </div>
        <div class="text-sm font-bold text-green-800">{{ session('success') }}</div>
    </div>
    @endif

    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-50 flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white">
            <div>
                <h3 class="text-lg font-black text-slate-800 font-outfit leading-none">Daftar Organizer</h3>
                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-1">Kelola semua penyedia event di platform</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('superadmin.tenants.trash') }}" class="p-2.5 bg-slate-50 text-slate-500 hover:text-slate-700 hover:bg-slate-100 rounded-xl transition shadow-sm group" title="Sampah">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                </a>
                <a href="{{ route('superadmin.tenants.create') }}" class="px-5 py-2.5 bg-orange-600 hover:bg-orange-700 text-white text-xs font-black uppercase tracking-wider rounded-xl shadow-lg shadow-orange-600/20 transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" /></svg>
                    Tambah Organizer
                </a>
            </div>
        </div>
        
        <div class="overflow-x-auto custom-scrollbar">
            <table class="w-full text-left">
                <thead class="bg-slate-50/50 border-b border-slate-50 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">
                    <tr>
                        <th class="px-6 py-4">Organizer / Email</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Jumlah Event</th>
                        <th class="px-6 py-4">Tanggal Bergabung</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($tenants as $tenant)
                    <tr class="hover:bg-slate-50/50 transition-colors group">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-xl bg-orange-50 flex items-center justify-center font-black text-orange-600 text-sm uppercase shadow-sm border border-orange-100">
                                    {{ substr($tenant->name, 0, 1) }}
                                </div>
                                <div class="min-w-0">
                                    <div class="font-black text-slate-800 text-sm font-outfit truncate group-hover:text-orange-600 transition-colors">{{ $tenant->name }}</div>
                                    <div class="text-[10px] text-slate-400 font-bold lowercase tracking-wider truncate">{{ $tenant->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $statusClasses = [
                                    'active' => 'bg-green-100 text-green-700',
                                    'inactive' => 'bg-slate-100 text-slate-600',
                                    'suspended' => 'bg-rose-100 text-rose-700',
                                ];
                                $class = $statusClasses[$tenant->status] ?? 'bg-slate-100 text-slate-600';
                            @endphp
                            <span class="px-2.5 py-1 rounded-lg text-[9px] font-black uppercase tracking-wider {{ $class }}">
                                {{ $tenant->status }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-xs font-black text-slate-700 font-outfit">{{ $tenant->events_count }} Event</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">{{ $tenant->created_at->format('d M Y') }}</span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex justify-end gap-2">
                                <div class="flex items-center gap-1 bg-slate-50 p-1 rounded-xl border border-slate-200">
                                    {{-- Impersonate Tenant --}}
                                    <form action="{{ route('impersonate.tenant.role', [$tenant, 'tenant']) }}" method="POST" class="inline" onsubmit="return confirm('Masuk sebagai Tenant {{ addslashes($tenant->name) }}?')">
                                        @csrf
                                        <button type="submit" class="p-1.5 bg-purple-50 text-purple-600 hover:text-white hover:bg-purple-600 rounded-lg transition shadow-xs border border-purple-100 flex items-center gap-1 text-[10px] font-bold" title="Impersonate Tenant (Organizer)">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                                            <span class="hidden xl:inline">Tenant</span>
                                        </button>
                                    </form>

                                    {{-- Impersonate Gate --}}
                                    <form action="{{ route('impersonate.tenant.role', [$tenant, 'gate']) }}" method="POST" class="inline" onsubmit="return confirm('Masuk sebagai Petugas Gate untuk {{ addslashes($tenant->name) }}?')">
                                        @csrf
                                        <button type="submit" class="p-1.5 bg-emerald-50 text-emerald-600 hover:text-white hover:bg-emerald-600 rounded-lg transition shadow-xs border border-emerald-100 flex items-center gap-1 text-[10px] font-bold" title="Impersonate Petugas Gate">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>
                                            <span class="hidden xl:inline">Gate</span>
                                        </button>
                                    </form>

                                    {{-- Impersonate Check-in --}}
                                    <form action="{{ route('impersonate.tenant.role', [$tenant, 'checkin']) }}" method="POST" class="inline" onsubmit="return confirm('Masuk sebagai Petugas Check-in untuk {{ addslashes($tenant->name) }}?')">
                                        @csrf
                                        <button type="submit" class="p-1.5 bg-blue-50 text-blue-600 hover:text-white hover:bg-blue-600 rounded-lg transition shadow-xs border border-blue-100 flex items-center gap-1 text-[10px] font-bold" title="Impersonate Petugas Check-in">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" /></svg>
                                            <span class="hidden xl:inline">Check-in</span>
                                        </button>
                                    </form>
                                </div>
                                <a href="{{ route('superadmin.tenants.edit', $tenant) }}" class="p-2 bg-orange-50 text-orange-500 hover:text-orange-700 hover:bg-orange-100 rounded-lg transition shadow-sm border border-orange-100" title="Edit Tenant">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                </a>
                                <form action="{{ route('superadmin.tenants.destroy', $tenant) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this tenant?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 bg-orange-50 text-orange-500 hover:text-rose-600 hover:bg-rose-100 rounded-lg transition shadow-sm border border-orange-100" title="Hapus Tenant">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-16 text-center text-slate-400 text-xs font-bold italic uppercase tracking-widest">Belum ada organizer</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($tenants->hasPages())
        <div class="px-6 py-4 bg-slate-50/50 border-t border-slate-50">
            {{ $tenants->links() }}
        </div>
        @endif
    </div>
</x-app-layout>
