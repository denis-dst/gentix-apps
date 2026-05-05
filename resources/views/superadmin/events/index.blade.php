<x-app-layout>
    <x-slot name="title">Manajemen Event</x-slot>

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
                <h3 class="text-lg font-black text-slate-800 font-outfit leading-none">Daftar Semua Event</h3>
                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-1">Pantau semua event dari seluruh organizer</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('superadmin.events.trash') }}" class="p-2.5 bg-slate-50 text-slate-500 hover:text-slate-700 hover:bg-slate-100 rounded-xl transition shadow-sm group" title="Sampah">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                </a>
                <a href="{{ route('superadmin.events.create') }}" class="px-5 py-2.5 bg-orange-600 hover:bg-orange-700 text-white text-xs font-black uppercase tracking-wider rounded-xl shadow-lg shadow-orange-600/20 transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" /></svg>
                    Tambah Event
                </a>
            </div>
        </div>
        
        <div class="overflow-x-auto custom-scrollbar">
            <table class="w-full text-left">
                <thead class="bg-slate-50/50 border-b border-slate-50 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">
                    <tr>
                        <th class="px-6 py-4">Detail Event</th>
                        <th class="px-6 py-4">Organizer</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Waktu</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($events as $event)
                    <tr class="hover:bg-slate-50/50 transition-colors group">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-xl bg-slate-50 overflow-hidden border border-slate-100 shrink-0">
                                    @if($event->banner_image)
                                        <img src="{{ asset('storage/' . $event->banner_image) }}" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-slate-200">
                                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V5h14v14zm-5.04-6.71l-2.75 3.54-1.96-2.36L6.5 17h11l-3.54-4.71z"/></svg>
                                        </div>
                                    @endif
                                </div>
                                <div class="min-w-0">
                                    <div class="font-black text-slate-800 text-sm font-outfit truncate group-hover:text-orange-600 transition-colors">{{ $event->name }}</div>
                                    <div class="text-[10px] text-slate-400 font-bold uppercase tracking-wider truncate mt-0.5">{{ $event->city }}, {{ $event->venue }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-xs font-bold text-slate-700 leading-none">{{ $event->tenant->name }}</span>
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $statusColors = [
                                    'draft' => 'bg-slate-100 text-slate-600',
                                    'published' => 'bg-green-100 text-green-700',
                                    'finished' => 'bg-blue-100 text-blue-700',
                                    'cancelled' => 'bg-rose-100 text-rose-700',
                                ];
                            @endphp
                            <span class="px-2.5 py-1 rounded-lg text-[9px] font-black uppercase tracking-wider {{ $statusColors[$event->status] ?? 'bg-slate-100 text-slate-600' }}">
                                {{ $event->status }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-[11px] font-black text-slate-700 mb-0.5">{{ $event->event_start_date->format('d M Y') }}</div>
                            <div class="text-[9px] text-slate-400 font-bold uppercase tracking-widest">{{ $event->event_start_date->format('H:i') }} WIB</div>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('superadmin.events.edit', $event) }}" class="p-2 bg-slate-100 text-slate-500 hover:text-orange-600 hover:bg-orange-100 rounded-lg transition shadow-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                </a>
                                <form action="{{ route('superadmin.events.destroy', $event) }}" method="POST" onsubmit="return confirm('Pindahkan event ini ke sampah?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 bg-slate-100 text-slate-500 hover:text-rose-600 hover:bg-rose-100 rounded-lg transition shadow-sm">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-16 text-center text-slate-400 text-xs font-bold italic uppercase tracking-widest">Belum ada event ditemukan</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($events->hasPages())
        <div class="px-6 py-4 bg-slate-50/50 border-t border-slate-50">
            {{ $events->links() }}
        </div>
        @endif
    </div>
</x-app-layout>
