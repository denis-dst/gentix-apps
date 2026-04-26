<x-app-layout>
    <x-slot name="title">Check-in Pelanggan</x-slot>
    <x-slot name="header">Monitoring Check-in</x-slot>

    <div class="space-y-6">
        <!-- Search Bar -->
        <div class="bg-white p-4 rounded-2xl shadow-sm border border-slate-100">
            <form action="{{ route('organizer.checkin.index') }}" method="GET" class="flex gap-4">
                <div class="flex-1 relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Nama atau Kode Tiket (GTX-...)" class="w-full pl-12 pr-4 py-3 rounded-xl border-slate-200 focus:border-purple-500 focus:ring-purple-500 transition">
                    <svg class="w-6 h-6 text-slate-400 absolute left-4 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                </div>
                <button type="submit" class="px-8 py-3 bg-slate-900 text-white rounded-xl font-bold hover:bg-slate-800 transition">
                    Cari
                </button>
            </form>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/50">
                            <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider">Tiket</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider">Pelanggan</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider">Event</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($tickets as $ticket)
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="px-6 py-4">
                                    <div class="font-mono font-black text-slate-900">{{ $ticket->ticket_code }}</div>
                                    <div class="text-[10px] text-purple-600 font-bold uppercase tracking-widest">{{ $ticket->category->name }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-bold text-slate-900 text-sm">{{ $ticket->transaction->customer_name }}</div>
                                    <div class="text-[10px] text-slate-400 font-bold">{{ $ticket->transaction->customer_nik }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-slate-600">{{ $ticket->event->name }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $ticketStatusClasses = [
                                            'sold' => 'bg-emerald-50 text-emerald-600 border-emerald-100',
                                            'redeemed' => 'bg-indigo-50 text-indigo-600 border-indigo-100',
                                            'void' => 'bg-rose-50 text-rose-600 border-rose-100',
                                            'reserved' => 'bg-amber-50 text-amber-600 border-amber-100',
                                        ];
                                    @endphp
                                    <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase border {{ $ticketStatusClasses[$ticket->status] ?? 'bg-slate-50 text-slate-500 border-slate-100' }}">
                                        @if($ticket->status === 'sold')
                                            Siap Digunakan
                                        @elseif($ticket->status === 'redeemed')
                                            Sudah Ditukar
                                        @else
                                            {{ $ticket->status }}
                                        @endif
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    @if($ticket->status === 'sold')
                                        <form action="{{ route('organizer.checkin.redeem', $ticket->id) }}" method="POST" onsubmit="return confirm('Konfirmasi penukaran tiket ini?')">
                                            @csrf
                                            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white text-xs font-bold rounded-lg hover:bg-indigo-700 transition shadow-lg shadow-indigo-100 flex items-center gap-2">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" /></svg>
                                                Redeem
                                            </button>
                                        </form>
                                    @else
                                        <div class="text-[10px] text-slate-300 font-bold uppercase tracking-widest">No Action</div>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-slate-400">
                                    Data tidak ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-6 bg-slate-50/30 border-t border-slate-50">
                {{ $tickets->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
