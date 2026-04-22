<x-app-layout>
    <x-slot name="title">Dashboard</x-slot>

    <!-- AdminLTE Small Boxes -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Revenue -->
        <div class="relative bg-cyan-500 text-white p-6 rounded-lg shadow-lg overflow-hidden group">
            <div class="relative z-10">
                <div class="text-3xl font-black mb-1">${{ number_format($stats['total_revenue'], 2) }}</div>
                <div class="text-xs font-bold opacity-80 uppercase tracking-widest">Total Revenue</div>
            </div>
            <div class="absolute right-2 top-2 text-white/20 transition-transform duration-300 group-hover:scale-110">
                <svg class="w-16 h-16" fill="currentColor" viewBox="0 0 24 24"><path d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            </div>
            <a href="#" class="absolute bottom-0 left-0 right-0 bg-black/10 py-1.5 text-center text-[10px] font-bold uppercase hover:bg-black/20 transition">More info</a>
        </div>

        <!-- Tenants -->
        <div class="relative bg-green-500 text-white p-6 rounded-lg shadow-lg overflow-hidden group">
            <div class="relative z-10">
                <div class="text-3xl font-black mb-1">{{ $stats['total_tenants'] }}</div>
                <div class="text-xs font-bold opacity-80 uppercase tracking-widest">Organizers</div>
            </div>
            <div class="absolute right-2 top-2 text-white/20 transition-transform duration-300 group-hover:scale-110">
                <svg class="w-16 h-16" fill="currentColor" viewBox="0 0 24 24"><path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
            </div>
            <a href="{{ route('superadmin.tenants.index') }}" class="absolute bottom-0 left-0 right-0 bg-black/10 py-1.5 text-center text-[10px] font-bold uppercase hover:bg-black/20 transition">More info</a>
        </div>

        <!-- Events -->
        <div class="relative bg-amber-500 text-white p-6 rounded-lg shadow-lg overflow-hidden group">
            <div class="relative z-10">
                <div class="text-3xl font-black mb-1">{{ $stats['total_events'] }}</div>
                <div class="text-xs font-bold opacity-80 uppercase tracking-widest">Active Events</div>
            </div>
            <div class="absolute right-2 top-2 text-white/20 transition-transform duration-300 group-hover:scale-110">
                <svg class="w-16 h-16" fill="currentColor" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
            </div>
            <a href="{{ route('superadmin.events.index') }}" class="absolute bottom-0 left-0 right-0 bg-black/10 py-1.5 text-center text-[10px] font-bold uppercase hover:bg-black/20 transition">More info</a>
        </div>

        <!-- Tickets -->
        <div class="relative bg-rose-500 text-white p-6 rounded-lg shadow-lg overflow-hidden group">
            <div class="relative z-10">
                <div class="text-3xl font-black mb-1">{{ $stats['total_tickets'] }}</div>
                <div class="text-xs font-bold opacity-80 uppercase tracking-widest">Tickets Sold</div>
            </div>
            <div class="absolute right-2 top-2 text-white/20 transition-transform duration-300 group-hover:scale-110">
                <svg class="w-16 h-16" fill="currentColor" viewBox="0 0 24 24"><path d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" /></svg>
            </div>
            <a href="#" class="absolute bottom-0 left-0 right-0 bg-black/10 py-1.5 text-center text-[10px] font-bold uppercase hover:bg-black/20 transition">More info</a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Transactions -->
        <div class="bg-white rounded-sm border-t-4 border-[#17a2b8] shadow-md overflow-hidden">
            <div class="px-4 py-3 border-b border-[#dee2e6] flex justify-between items-center bg-white">
                <h3 class="text-lg font-medium">Recent Transactions</h3>
                <span class="bg-[#17a2b8] text-white px-2 py-0.5 rounded-md text-xs font-bold">{{ count($recent_transactions ?? []) }} New</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-[#f8f9fa] border-b border-[#dee2e6]">
                        <tr>
                            <th class="px-4 py-2 font-bold">Trx ID</th>
                            <th class="px-4 py-2 font-bold">Event</th>
                            <th class="px-4 py-2 font-bold">Amount</th>
                            <th class="px-4 py-2 font-bold">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#dee2e6]">
                        @forelse($recent_transactions ?? [] as $transaction)
                        <tr>
                            <td class="px-4 py-3">{{ $transaction->transaction_number }}</td>
                            <td class="px-4 py-3 font-medium">{{ $transaction->event->name ?? 'N/A' }}</td>
                            <td class="px-4 py-3">${{ number_format($transaction->total_amount, 2) }}</td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $transaction->status === 'paid' ? 'bg-[#28a745] text-white' : 'bg-[#ffc107] text-[#343a40]' }}">
                                    {{ $transaction->status }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-gray-500 italic">No recent transactions.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-4 py-3 border-t border-[#dee2e6] text-center">
                <a href="{{ route('superadmin.transactions.index') }}" class="text-gentix-600 hover:underline text-xs font-bold uppercase">View All Transactions</a>
            </div>
        </div>

        <!-- Active Events -->
        <div class="bg-white rounded-sm border-t-4 border-[#28a745] shadow-md overflow-hidden">
            <div class="px-4 py-3 border-b border-[#dee2e6] flex justify-between items-center bg-white">
                <h3 class="text-lg font-medium">Top Performing Events</h3>
                <span class="bg-[#28a745] text-white px-2 py-0.5 rounded-md text-xs font-bold">Live</span>
            </div>
            <div class="p-4 space-y-4">
                @forelse($active_events ?? [] as $event)
                <div class="flex items-center justify-between group">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded bg-[#f8f9fa] border border-[#dee2e6] flex items-center justify-center text-gentix-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                        </div>
                        <div>
                            <div class="font-bold text-[#343a40]">{{ $event->name }}</div>
                            <div class="text-xs text-[#6c757d]">{{ $event->tenant->name ?? 'General' }}</div>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="text-sm font-bold text-[#28a745]">{{ $event->tickets_count }} Sold</div>
                        <div class="text-[10px] text-[#6c757d] uppercase font-bold tracking-tighter">
                            {{ optional($event->event_start_date)->format('M d, Y') ?? 'N/A' }}
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center py-8 text-gray-500 italic">No active events found.</div>
                @endforelse
            </div>
            <div class="px-4 py-3 border-t border-[#dee2e6] text-center">
                <a href="{{ route('superadmin.events.index') }}" class="text-gentix-600 hover:underline text-xs font-bold uppercase">Manage All Events</a>
            </div>
        </div>
    </div>
</x-app-layout>
