<x-app-layout>
    <x-slot name="title">Ringkasan Dashboard</x-slot>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 mb-8">
        <!-- Revenue -->
        <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100 group hover:shadow-md transition-all duration-300 flex items-center justify-between">
            <div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-1">Total Pendapatan</p>
                <h3 class="text-2xl font-black text-slate-800 font-outfit leading-tight">${{ number_format($stats['total_revenue'], 2) }}</h3>
                <div class="mt-2 flex items-center gap-2">
                    <span class="px-2 py-0.5 bg-cyan-50 text-cyan-600 text-[9px] font-black uppercase rounded-md tracking-wider">Live Update</span>
                </div>
            </div>
            <div class="w-14 h-14 bg-cyan-50 rounded-2xl flex items-center justify-center text-cyan-500 group-hover:bg-cyan-500 group-hover:text-white transition-all duration-300 shadow-sm border border-cyan-100/50">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            </div>
        </div>

        <!-- Tenants -->
        <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100 group hover:shadow-md transition-all duration-300 flex items-center justify-between">
            <div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-1">Penyedia Event</p>
                <h3 class="text-2xl font-black text-slate-800 font-outfit leading-tight">{{ $stats['total_tenants'] }}</h3>
                <div class="mt-2">
                    <a href="{{ route('superadmin.tenants.index') }}" class="text-[9px] font-black text-green-600 uppercase tracking-wider hover:underline">Kelola &rarr;</a>
                </div>
            </div>
            <div class="w-14 h-14 bg-green-50 rounded-2xl flex items-center justify-center text-green-500 group-hover:bg-green-500 group-hover:text-white transition-all duration-300 shadow-sm border border-green-100/50">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
            </div>
        </div>

        <!-- Events -->
        <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100 group hover:shadow-md transition-all duration-300 flex items-center justify-between">
            <div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-1">Event Aktif</p>
                <h3 class="text-2xl font-black text-slate-800 font-outfit leading-tight">{{ $stats['total_events'] }}</h3>
                <div class="mt-2">
                    <a href="{{ route('superadmin.events.index') }}" class="text-[9px] font-black text-amber-600 uppercase tracking-wider hover:underline">Monitor &rarr;</a>
                </div>
            </div>
            <div class="w-14 h-14 bg-amber-50 rounded-2xl flex items-center justify-center text-amber-500 group-hover:bg-amber-500 group-hover:text-white transition-all duration-300 shadow-sm border border-amber-100/50">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
            </div>
        </div>

        <!-- Tickets -->
        <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100 group hover:shadow-md transition-all duration-300 flex items-center justify-between">
            <div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-1">Tiket Terjual</p>
                <h3 class="text-2xl font-black text-slate-800 font-outfit leading-tight">{{ $stats['total_tickets'] }}</h3>
                <div class="mt-2">
                    <span class="px-2 py-0.5 bg-rose-50 text-rose-600 text-[9px] font-black uppercase rounded-md tracking-wider">Total Sales</span>
                </div>
            </div>
            <div class="w-14 h-14 bg-rose-50 rounded-2xl flex items-center justify-center text-rose-500 group-hover:bg-rose-500 group-hover:text-white transition-all duration-300 shadow-sm border border-rose-100/50">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" /></svg>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 md:gap-8">
        <!-- Recent Transactions -->
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-50 flex justify-between items-center bg-white">
                <div>
                    <h3 class="text-lg font-black text-slate-800 font-outfit leading-none">Transaksi Terakhir</h3>
                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-1">Aktivitas penjualan terbaru</p>
                </div>
                <span class="bg-orange-50 text-orange-600 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider">{{ count($recent_transactions ?? []) }} Baru</span>
            </div>
            <div class="overflow-x-auto custom-scrollbar">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-slate-50/50 border-b border-slate-50">
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">ID Transaksi</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Event</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Jumlah</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse($recent_transactions ?? [] as $transaction)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-6 py-4 text-xs font-bold text-slate-700 font-mono">{{ $transaction->reference_no }}</td>
                            <td class="px-6 py-4">
                                <div class="text-xs font-bold text-slate-800 truncate max-w-[150px]">{{ $transaction->event->name ?? 'N/A' }}</div>
                            </td>
                            <td class="px-6 py-4 text-xs font-black text-slate-900 font-outfit">${{ number_format($transaction->total_amount, 2) }}</td>
                            <td class="px-6 py-4">
                                @php
                                    $statusClasses = [
                                        'paid' => 'bg-green-100 text-green-700',
                                        'pending' => 'bg-amber-100 text-amber-700',
                                        'failed' => 'bg-rose-100 text-rose-700',
                                    ];
                                    $class = $statusClasses[$transaction->payment_status] ?? 'bg-slate-100 text-slate-600';
                                @endphp
                                <span class="px-2.5 py-1 rounded-lg text-[9px] font-black uppercase tracking-wider {{ $class }}">
                                    {{ $transaction->payment_status }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-slate-400 text-xs font-bold italic uppercase tracking-widest">Belum ada transaksi</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 bg-slate-50/50 text-center border-t border-slate-50">
                <a href="{{ route('superadmin.transactions.index') }}" class="text-orange-600 hover:text-orange-700 text-[10px] font-black uppercase tracking-[0.2em]">Lihat Semua Transaksi &rarr;</a>
            </div>
        </div>

        <!-- Top Events -->
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-50 flex justify-between items-center bg-white">
                <div>
                    <h3 class="text-lg font-black text-slate-800 font-outfit leading-none">Event Terpopuler</h3>
                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-1">Berdasarkan jumlah tiket terjual</p>
                </div>
                <span class="bg-green-50 text-green-600 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider">Live</span>
            </div>
            <div class="p-6 space-y-4">
                @forelse($active_events ?? [] as $event)
                <div class="flex items-center justify-between p-4 bg-slate-50/50 rounded-2xl hover:bg-slate-50 transition-colors group border border-transparent hover:border-slate-100">
                    <div class="flex items-center gap-4 min-w-0">
                        <div class="w-12 h-12 rounded-xl bg-white border border-slate-100 flex items-center justify-center text-orange-500 shadow-sm shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                        </div>
                        <div class="min-w-0">
                            <div class="font-black text-slate-800 text-sm truncate font-outfit leading-tight mb-1 group-hover:text-orange-600 transition-colors">{{ $event->name }}</div>
                            <div class="text-[10px] text-slate-400 font-bold uppercase tracking-widest truncate">{{ $event->tenant->name ?? 'General' }}</div>
                        </div>
                    </div>
                    <div class="text-right shrink-0 ml-4">
                        <div class="text-sm font-black text-green-600 font-outfit leading-none mb-1">{{ $event->tickets_count }} Laku</div>
                        <div class="text-[9px] text-slate-400 uppercase font-black tracking-widest">
                            {{ optional($event->event_start_date)->format('d M Y') ?? 'N/A' }}
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center py-12 text-slate-400 text-xs font-bold italic uppercase tracking-widest">Belum ada event aktif</div>
                @endforelse
            </div>
            <div class="px-6 py-4 bg-slate-50/50 text-center border-t border-slate-50">
                <a href="{{ route('superadmin.events.index') }}" class="text-orange-600 hover:text-orange-700 text-[10px] font-black uppercase tracking-[0.2em]">Kelola Semua Event &rarr;</a>
            </div>
        </div>
    </div>
</x-app-layout>
