@php
    $isSuperadminReport = ($scope ?? '') === 'superadmin';
    $reportRoute = $isSuperadminReport ? route('superadmin.reports.index') : route('organizer.reports.index');
@endphp

<div class="space-y-6 animate-in fade-in slide-in-from-bottom-4 duration-700">
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <p class="text-[10px] font-black text-orange-500 uppercase tracking-[0.25em] mb-2">Monitoring Operasional</p>
            <h2 class="text-3xl font-black text-slate-800 font-outfit">Laporan Event & Tiket</h2>
            <p class="text-sm text-slate-500 font-medium mt-1">Pantau penjualan, redeem, check-in, dan checkout per event serta kategori tiket.</p>
        </div>

        <form action="{{ $reportRoute }}" method="GET" class="flex flex-col sm:flex-row gap-3 bg-white p-3 rounded-2xl border border-slate-100 shadow-sm">
            @if($isSuperadminReport)
                <select name="tenant_id" class="rounded-xl border-slate-200 text-sm font-bold text-slate-600 focus:border-orange-500 focus:ring-orange-500">
                    <option value="">Semua Organizer</option>
                    @foreach($tenantOptions as $tenant)
                        <option value="{{ $tenant->id }}" {{ request('tenant_id') == $tenant->id ? 'selected' : '' }}>{{ $tenant->name }}</option>
                    @endforeach
                </select>
            @endif
            <select name="event_id" class="rounded-xl border-slate-200 text-sm font-bold text-slate-600 focus:border-orange-500 focus:ring-orange-500">
                <option value="">Semua Event</option>
                @foreach($eventOptions as $eventOption)
                    <option value="{{ $eventOption->id }}" {{ request('event_id') == $eventOption->id ? 'selected' : '' }}>{{ $eventOption->name }}</option>
                @endforeach
            </select>
            <button type="submit" class="px-5 py-2.5 bg-orange-600 text-white rounded-xl text-xs font-black uppercase tracking-wider hover:bg-orange-700 transition">Filter</button>
        </form>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-6 gap-4">
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Tiket Terjual</p>
            <div class="mt-2 text-2xl font-black text-slate-800 font-outfit">{{ number_format($totals['sold']) }}</div>
        </div>
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Sudah Redeem</p>
            <div class="mt-2 text-2xl font-black text-emerald-600 font-outfit">{{ number_format($totals['redeemed']) }}</div>
        </div>
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Check-in</p>
            <div class="mt-2 text-2xl font-black text-blue-600 font-outfit">{{ number_format($totals['checkin']) }}</div>
        </div>
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Checkout</p>
            <div class="mt-2 text-2xl font-black text-amber-600 font-outfit">{{ number_format($totals['checkout']) }}</div>
        </div>
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Di Dalam Area</p>
            <div class="mt-2 text-2xl font-black text-purple-600 font-outfit">{{ number_format($totals['inside']) }}</div>
        </div>
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Pendapatan Paid</p>
            <div class="mt-2 text-xl font-black text-green-600 font-outfit">Rp {{ number_format($totals['revenue'], 0, ',', '.') }}</div>
        </div>
    </div>

    <div class="space-y-5">
        @forelse($reportRows as $row)
            @php($event = $row['event'])
            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
                <div class="px-6 py-5 border-b border-slate-100 flex flex-col xl:flex-row xl:items-center justify-between gap-4">
                    <div class="min-w-0">
                        <div class="flex flex-wrap items-center gap-2 mb-1">
                            @if($isSuperadminReport)
                                <span class="px-2.5 py-1 bg-slate-100 text-slate-500 rounded-lg text-[9px] font-black uppercase tracking-wider">{{ $event->tenant->name ?? '-' }}</span>
                            @endif
                            <span class="px-2.5 py-1 bg-orange-50 text-orange-600 rounded-lg text-[9px] font-black uppercase tracking-wider">{{ $event->status }}</span>
                        </div>
                        <h3 class="text-lg font-black text-slate-800 font-outfit truncate">{{ $event->name }}</h3>
                        <p class="text-xs text-slate-400 font-bold uppercase tracking-wider mt-1">{{ $event->event_start_date?->format('d M Y H:i') }} | {{ $event->venue }}</p>
                    </div>
                    <div class="grid grid-cols-3 md:grid-cols-6 gap-3 text-center">
                        <div><div class="text-lg font-black text-slate-800">{{ number_format($row['sold_count']) }}</div><div class="text-[9px] font-black text-slate-400 uppercase">Terjual</div></div>
                        <div><div class="text-lg font-black text-emerald-600">{{ number_format($row['redeemed_count']) }}</div><div class="text-[9px] font-black text-slate-400 uppercase">Redeem</div></div>
                        <div><div class="text-lg font-black text-blue-600">{{ number_format($row['checkin_count']) }}</div><div class="text-[9px] font-black text-slate-400 uppercase">Check-in</div></div>
                        <div><div class="text-lg font-black text-amber-600">{{ number_format($row['checkout_count']) }}</div><div class="text-[9px] font-black text-slate-400 uppercase">Checkout</div></div>
                        <div><div class="text-lg font-black text-purple-600">{{ number_format($row['inside_count']) }}</div><div class="text-[9px] font-black text-slate-400 uppercase">Inside</div></div>
                        <div><div class="text-sm font-black text-green-600">Rp {{ number_format($row['revenue'], 0, ',', '.') }}</div><div class="text-[9px] font-black text-slate-400 uppercase">Paid</div></div>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50/70">
                                <th class="px-6 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest">Kategori</th>
                                <th class="px-6 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Terjual</th>
                                <th class="px-6 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Redeem</th>
                                <th class="px-6 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Check-in</th>
                                <th class="px-6 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Checkout</th>
                                <th class="px-6 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Di Dalam</th>
                                <th class="px-6 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Transaksi Paid</th>
                                <th class="px-6 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Pendapatan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @forelse($row['categories'] as $category)
                                <tr class="hover:bg-slate-50/40 transition">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <span class="w-2.5 h-8 rounded-full" style="background: {{ $category['hex_color'] }}"></span>
                                            <span class="text-sm font-black text-slate-800">{{ $category['name'] }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-right text-sm font-black text-slate-700">{{ number_format($category['sold_count']) }}</td>
                                    <td class="px-6 py-4 text-right text-sm font-black text-emerald-600">{{ number_format($category['redeemed_count']) }}</td>
                                    <td class="px-6 py-4 text-right text-sm font-black text-blue-600">{{ number_format($category['checkin_count']) }}</td>
                                    <td class="px-6 py-4 text-right text-sm font-black text-amber-600">{{ number_format($category['checkout_count']) }}</td>
                                    <td class="px-6 py-4 text-right text-sm font-black text-purple-600">{{ number_format($category['inside_count']) }}</td>
                                    <td class="px-6 py-4 text-right text-sm font-black text-slate-700">{{ number_format($category['paid_transactions_count']) }}</td>
                                    <td class="px-6 py-4 text-right text-sm font-black text-green-600">Rp {{ number_format($category['revenue'], 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-10 text-center text-sm font-bold text-slate-400">Belum ada kategori tiket.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-3xl border border-slate-100 p-12 text-center">
                <div class="text-sm font-bold text-slate-400">Belum ada data event untuk filter ini.</div>
            </div>
        @endforelse
    </div>

    <div>
        {{ $events->links() }}
    </div>

    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-100">
            <h3 class="text-lg font-black text-slate-800 font-outfit">Transaksi Terbaru</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/70">
                        <th class="px-6 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest">Invoice</th>
                        <th class="px-6 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest">Customer</th>
                        @if($isSuperadminReport)
                            <th class="px-6 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest">Organizer</th>
                        @endif
                        <th class="px-6 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest">Event</th>
                        <th class="px-6 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Tiket</th>
                        <th class="px-6 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Total</th>
                        <th class="px-6 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($transactions as $tx)
                        <tr class="hover:bg-slate-50/40 transition">
                            <td class="px-6 py-4 text-xs font-black text-slate-600 font-mono">{{ $tx->reference_no }}</td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-black text-slate-800">{{ $tx->customer_name }}</div>
                                <div class="text-[10px] font-bold text-slate-400 uppercase">{{ $tx->customer_email }}</div>
                            </td>
                            @if($isSuperadminReport)
                                <td class="px-6 py-4 text-sm font-bold text-slate-600">{{ $tx->tenant->name ?? '-' }}</td>
                            @endif
                            <td class="px-6 py-4 text-sm font-bold text-slate-600">{{ $tx->event->name ?? '-' }}</td>
                            <td class="px-6 py-4 text-right text-sm font-black text-slate-700">{{ $tx->tickets->count() }}</td>
                            <td class="px-6 py-4 text-right text-sm font-black text-green-600">Rp {{ number_format($tx->total_amount, 0, ',', '.') }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 rounded-lg text-[9px] font-black uppercase tracking-wider {{ $tx->payment_status === 'paid' ? 'bg-emerald-50 text-emerald-600' : 'bg-amber-50 text-amber-600' }}">{{ $tx->payment_status }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $isSuperadminReport ? 7 : 6 }}" class="px-6 py-10 text-center text-sm font-bold text-slate-400">Belum ada transaksi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
