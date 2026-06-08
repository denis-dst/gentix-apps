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

    <!-- Detailed Reports Section with Tabs, Search, and Export -->
    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden" x-data="{ activeReportTab: 'transactions', reportSearch: '' }">
        <div class="px-6 py-5 border-b border-slate-100 flex flex-col lg:flex-row lg:items-center justify-between gap-4 bg-slate-50/50">
            <div>
                <h3 class="text-lg font-black text-slate-800 font-outfit">Laporan Detail Pendaftaran</h3>
                <p class="text-xs text-slate-500 font-medium mt-0.5">Daftar lengkap pendaftar dan detail tiket peserta yang terverifikasi.</p>
            </div>
            
            <div class="flex flex-wrap items-center gap-3">
                <!-- Search Input -->
                <div class="relative w-full sm:w-64">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                    </span>
                    <input type="text" id="report-search-input" onkeyup="filterReportTables()" placeholder="Cari data pendaftar..." 
                           class="w-full pl-9 pr-4 py-2 rounded-xl border-slate-200 text-xs font-bold text-slate-600 focus:border-orange-500 focus:ring-orange-500 bg-white">
                </div>

                <!-- Export CSV Buttons -->
                <button type="button" onclick="exportActiveTableToCSV()" class="w-full sm:w-auto px-4 py-2 bg-emerald-600 text-white rounded-xl text-xs font-black uppercase tracking-wider hover:bg-emerald-700 transition flex items-center justify-center gap-2 shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                    Export ke CSV
                </button>
            </div>
        </div>

        <!-- Tab Buttons (Glassmorphic design) -->
        <div class="px-6 py-3 border-b border-slate-100 flex items-center gap-2 bg-white">
            <button type="button" id="tab-btn-tx" onclick="switchReportTab('transactions')" class="px-4 py-2 rounded-xl text-xs font-black uppercase tracking-wider transition duration-200 bg-orange-600 text-black">
                📂 Per Transaksi (Satuan)
            </button>
            <button type="button" id="tab-btn-tickets" onclick="switchReportTab('tickets')" class="px-4 py-2 rounded-xl text-xs font-black uppercase tracking-wider transition duration-200 bg-slate-100 text-slate-500 hover:bg-slate-200">
                🎫 Per Tiket (Detail Peserta)
            </button>
        </div>

        <!-- 1. Laporan Per Transaksi (Satuan) -->
        <div id="report-tab-transactions" class="block">
            <div class="overflow-x-auto">
                <table id="table-report-tx" class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/70 border-b border-slate-100">
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Waktu & Invoice</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Nama Pemesan</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">NIK</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Email</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">WhatsApp</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Gender</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Jawaban Umroh</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Event & Kategori</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Qty</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Total Bayar</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Status</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Metode</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50 font-medium">
                        @forelse($transactions as $tx)
                            <tr class="tx-row hover:bg-slate-50/40 transition">
                                <td class="px-6 py-4 text-xs font-black text-slate-600 font-mono">{{ $tx->reference_no }}<br><span class="text-[9px] font-bold text-slate-400 font-sans block mt-0.5">{{ $tx->created_at->format('d M Y H:i') }}</span></td>
                                <td class="px-6 py-4 text-sm font-black text-slate-800 uppercase">{{ $tx->customer_name }}</td>
                                <td class="px-6 py-4 text-xs text-slate-600">{{ $tx->customer_nik ?? '-' }}</td>
                                <td class="px-6 py-4 text-xs text-slate-600">{{ $tx->customer_email }}</td>
                                <td class="px-6 py-4 text-xs text-slate-600">{{ $tx->customer_phone }}</td>
                                <td class="px-6 py-4 text-xs">
                                    @if($tx->customer_gender)
                                        <span class="font-black uppercase {{ $tx->customer_gender === 'ikhwan' ? 'text-blue-600' : 'text-pink-600' }}">
                                            {{ $tx->customer_gender === 'ikhwan' ? '🧔 Ikhwan' : '🧕 Akhwat' }}
                                        </span>
                                    @else
                                        <span class="text-slate-300">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-xs text-slate-600 max-w-[200px] truncate" title="{{ $tx->customer_umroh_answer }}">{{ $tx->customer_umroh_answer ?? '-' }}</td>
                                <td class="px-6 py-4 text-xs">
                                    <span class="font-bold text-slate-700 block">{{ $tx->event->name ?? '-' }}</span>
                                    <span class="text-[10px] text-orange-500 font-black uppercase tracking-wider block mt-0.5">{{ $tx->category->name ?? 'Mixed' }}</span>
                                </td>
                                <td class="px-6 py-4 text-right text-sm font-black text-slate-700">{{ $tx->quantity }}</td>
                                <td class="px-6 py-4 text-right text-sm font-black text-green-600">Rp {{ number_format($tx->total_amount, 0, ',', '.') }}</td>
                                <td class="px-6 py-4">
                                    @if($tx->payment_status === 'paid')
                                        <span class="px-2 py-1 rounded bg-emerald-50 text-emerald-600 text-[9px] font-black uppercase tracking-wider">PAID</span>
                                    @elseif($tx->payment_status === 'refunded')
                                        <span class="px-2 py-1 rounded bg-rose-50 text-rose-600 text-[9px] font-black uppercase tracking-wider">REFUNDED</span>
                                    @else
                                        <span class="px-2 py-1 rounded bg-amber-50 text-amber-600 text-[9px] font-black uppercase tracking-wider">{{ strtoupper($tx->payment_status) }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-xs text-slate-600 uppercase">{{ $tx->payment_method ?? 'Unknown' }}</td>
                            </tr>
                        @empty
                            <tr class="no-data-tx">
                                <td colspan="12" class="px-6 py-10 text-center text-sm font-bold text-slate-400">Belum ada transaksi pendaftaran.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <!-- Pagination Controls for Transactions -->
            <div id="tx-pagination" class="px-6 py-4 border-t border-slate-100 flex items-center justify-between bg-white text-xs font-bold text-slate-500">
                <div id="tx-pagination-info">Menampilkan 1-10 dari 10 data</div>
                <div class="flex items-center gap-2">
                    <button type="button" onclick="prevReportPage('transactions')" class="px-3 py-1.5 rounded-lg border border-slate-200 hover:bg-slate-50 transition">Prev</button>
                    <button type="button" onclick="nextReportPage('transactions')" class="px-3 py-1.5 rounded-lg border border-slate-200 hover:bg-slate-50 transition">Next</button>
                </div>
            </div>
        </div>

        <!-- 2. Laporan Per Tiket (Detail Keseluruhan) -->
        <div id="report-tab-tickets" class="hidden">
            <div class="overflow-x-auto">
                <table id="table-report-tickets" class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/70 border-b border-slate-100">
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Kode Tiket</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Invoice</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Nama Peserta</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">NIK</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Email</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">WhatsApp</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Gender</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Jawaban Umroh</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Event</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Kategori</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Status</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Scan Checkin</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50 font-medium">
                        @php($hasTickets = false)
                        @foreach($transactions as $tx)
                            @foreach($tx->tickets as $ticket)
                                @php($hasTickets = true)
                                <tr class="ticket-row hover:bg-slate-50/40 transition">
                                    <td class="px-6 py-4 text-xs font-black text-slate-700 font-mono">{{ $ticket->ticket_code }}</td>
                                    <td class="px-6 py-4 text-xs font-bold text-slate-500 font-mono">{{ $tx->reference_no }}</td>
                                    <td class="px-6 py-4 text-sm font-black text-slate-800 uppercase">{{ $ticket->visitor_data['name'] ?? $tx->customer_name }}</td>
                                    <td class="px-6 py-4 text-xs text-slate-600">{{ $ticket->visitor_data['nik'] ?? $tx->customer_nik ?? '-' }}</td>
                                    <td class="px-6 py-4 text-xs text-slate-600">{{ $ticket->visitor_data['email'] ?? $tx->customer_email }}</td>
                                    <td class="px-6 py-4 text-xs text-slate-600">{{ $ticket->visitor_data['phone'] ?? $tx->customer_phone }}</td>
                                    <td class="px-6 py-4 text-xs">
                                        @php($tGender = $ticket->visitor_data['gender'] ?? $tx->customer_gender)
                                        @if($tGender)
                                            <span class="font-black uppercase {{ $tGender === 'ikhwan' ? 'text-blue-600' : 'text-pink-600' }}">
                                                {{ $tGender === 'ikhwan' ? '🧔 Ikhwan' : '🧕 Akhwat' }}
                                            </span>
                                        @else
                                            <span class="text-slate-300">-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-xs text-slate-600 max-w-[200px] truncate" title="{{ $ticket->visitor_data['umroh_answer'] ?? $tx->customer_umroh_answer }}">{{ $ticket->visitor_data['umroh_answer'] ?? $tx->customer_umroh_answer ?? '-' }}</td>
                                    <td class="px-6 py-4 text-xs text-slate-700 font-bold">{{ $tx->event->name ?? '-' }}</td>
                                    <td class="px-6 py-4 text-xs text-orange-500 font-black uppercase tracking-wider">{{ $ticket->category->name ?? '-' }}</td>
                                    <td class="px-6 py-4">
                                        @if($ticket->status === 'sold')
                                            <span class="px-2 py-0.5 rounded bg-blue-50 text-blue-600 font-bold text-[9px] uppercase tracking-wide">SOLD</span>
                                        @elseif($ticket->status === 'redeemed')
                                            <span class="px-2 py-0.5 rounded bg-emerald-50 text-emerald-600 font-bold text-[9px] uppercase tracking-wide">REDEEMED</span>
                                        @elseif($ticket->status === 'void')
                                            <span class="px-2 py-0.5 rounded bg-rose-50 text-rose-600 font-bold text-[9px] uppercase tracking-wide">VOID</span>
                                        @else
                                            <span class="px-2 py-0.5 rounded bg-slate-50 text-slate-500 font-bold text-[9px] uppercase tracking-wide">{{ strtoupper($ticket->status) }}</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-xs text-slate-500">
                                        {{ $ticket->redeemed_at ? $ticket->redeemed_at->format('d M Y H:i') . ' WIB' : 'Belum Scan' }}
                                    </td>
                                </tr>
                            @endforeach
                        @endforeach
                        @if(!$hasTickets)
                            <tr class="no-data-tickets">
                                <td colspan="12" class="px-6 py-10 text-center text-sm font-bold text-slate-400">Belum ada detail tiket peserta.</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
            <!-- Pagination Controls for Tickets -->
            <div id="tickets-pagination" class="px-6 py-4 border-t border-slate-100 flex items-center justify-between bg-white text-xs font-bold text-slate-500">
                <div id="tickets-pagination-info">Menampilkan 1-10 dari 10 data</div>
                <div class="flex items-center gap-2">
                    <button type="button" onclick="prevReportPage('tickets')" class="px-3 py-1.5 rounded-lg border border-slate-200 hover:bg-slate-50 transition">Prev</button>
                    <button type="button" onclick="nextReportPage('tickets')" class="px-3 py-1.5 rounded-lg border border-slate-200 hover:bg-slate-50 transition">Next</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Tab switching state
    let activeTab = 'transactions';
    let txCurrentPage = 1;
    let ticketsCurrentPage = 1;
    const itemsPerPage = 15;

    function switchReportTab(tab) {
        activeTab = tab;
        const btnTx = document.getElementById('tab-btn-tx');
        const btnTickets = document.getElementById('tab-btn-tickets');
        const paneTx = document.getElementById('report-tab-transactions');
        const paneTickets = document.getElementById('report-tab-tickets');

        if (tab === 'transactions') {
            btnTx.className = "px-4 py-2 rounded-xl text-xs font-black uppercase tracking-wider transition duration-200 bg-orange-600 text-black";
            btnTickets.className = "px-4 py-2 rounded-xl text-xs font-black uppercase tracking-wider transition duration-200 bg-slate-100 text-slate-500 hover:bg-slate-200";
            paneTx.className = "block animate-in fade-in duration-200";
            paneTickets.className = "hidden";
        } else {
            btnTx.className = "px-4 py-2 rounded-xl text-xs font-black uppercase tracking-wider transition duration-200 bg-slate-100 text-slate-500 hover:bg-slate-200";
            btnTickets.className = "px-4 py-2 rounded-xl text-xs font-black uppercase tracking-wider transition duration-200 bg-orange-600 text-black";
            paneTx.className = "hidden";
            paneTickets.className = "block animate-in fade-in duration-200";
        }
        
        filterReportTables(); // Recalculate pagination
    }

    function filterReportTables() {
        const query = document.getElementById('report-search-input').value.toLowerCase().trim();
        
        // 1. Process Transactions
        const txRows = document.querySelectorAll('.tx-row');
        let visibleTx = [];
        txRows.forEach(row => {
            const text = row.textContent.toLowerCase();
            if (query === '' || text.includes(query)) {
                row.setAttribute('data-matched', 'true');
                visibleTx.push(row);
            } else {
                row.setAttribute('data-matched', 'false');
                row.style.display = 'none';
            }
        });

        // 2. Process Tickets
        const ticketRows = document.querySelectorAll('.ticket-row');
        let visibleTickets = [];
        ticketRows.forEach(row => {
            const text = row.textContent.toLowerCase();
            if (query === '' || text.includes(query)) {
                row.setAttribute('data-matched', 'true');
                visibleTickets.push(row);
            } else {
                row.setAttribute('data-matched', 'false');
                row.style.display = 'none';
            }
        });

        // Handle transaction pagination
        const totalTx = visibleTx.length;
        const totalTxPages = Math.ceil(totalTx / itemsPerPage) || 1;
        if (txCurrentPage > totalTxPages) txCurrentPage = totalTxPages;
        
        const txStartIdx = (txCurrentPage - 1) * itemsPerPage;
        const txEndIdx = Math.min(txStartIdx + itemsPerPage, totalTx);

        visibleTx.forEach((row, idx) => {
            if (idx >= txStartIdx && idx < txEndIdx) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });

        const txInfo = document.getElementById('tx-pagination-info');
        if (txInfo) {
            txInfo.textContent = `Menampilkan ${totalTx === 0 ? 0 : txStartIdx + 1}-${txEndIdx} dari ${totalTx} data`;
        }

        // Handle ticket pagination
        const totalTickets = visibleTickets.length;
        const totalTicketPages = Math.ceil(totalTickets / itemsPerPage) || 1;
        if (ticketsCurrentPage > totalTicketPages) ticketsCurrentPage = totalTicketPages;

        const ticketStartIdx = (ticketsCurrentPage - 1) * itemsPerPage;
        const ticketEndIdx = Math.min(ticketStartIdx + itemsPerPage, totalTickets);

        visibleTickets.forEach((row, idx) => {
            if (idx >= ticketStartIdx && idx < ticketEndIdx) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });

        const ticketInfo = document.getElementById('tickets-pagination-info');
        if (ticketInfo) {
            ticketInfo.textContent = `Menampilkan ${totalTickets === 0 ? 0 : ticketStartIdx + 1}-${ticketEndIdx} dari ${totalTickets} data`;
        }
    }

    function prevReportPage(tab) {
        if (tab === 'transactions') {
            if (txCurrentPage > 1) {
                txCurrentPage--;
                filterReportTables();
            }
        } else {
            if (ticketsCurrentPage > 1) {
                ticketsCurrentPage--;
                filterReportTables();
            }
        }
    }

    function nextReportPage(tab) {
        if (tab === 'transactions') {
            const matchedRows = document.querySelectorAll('.tx-row[data-matched="true"]').length;
            const totalPages = Math.ceil(matchedRows / itemsPerPage);
            if (txCurrentPage < totalPages) {
                txCurrentPage++;
                filterReportTables();
            }
        } else {
            const matchedRows = document.querySelectorAll('.ticket-row[data-matched="true"]').length;
            const totalPages = Math.ceil(matchedRows / itemsPerPage);
            if (ticketsCurrentPage < totalPages) {
                ticketsCurrentPage++;
                filterReportTables();
            }
        }
    }

    function exportActiveTableToCSV() {
        let csv = [];
        let filename = '';
        let headers = [];
        let rows = [];

        if (activeTab === 'transactions') {
            filename = 'Laporan_Pendaftar_Per_Transaksi.csv';
            headers = ['Invoice No', 'Tanggal', 'Nama Pemesan', 'NIK', 'Email', 'WhatsApp', 'Gender', 'Jawaban Umroh', 'Event Name', 'Category Name', 'Quantity', 'Total Amount', 'Status', 'Payment Method'];
            
            // Extract matching rows
            const txRows = document.querySelectorAll('.tx-row[data-matched="true"]');
            txRows.forEach(row => {
                const cols = row.querySelectorAll('td');
                let rowData = [
                    cols[0].firstChild.textContent.trim(), // Invoice No
                    cols[0].querySelector('span').textContent.trim(), // Tanggal
                    cols[1].textContent.trim(), // Nama Pemesan
                    cols[2].textContent.trim(), // NIK
                    cols[3].textContent.trim(), // Email
                    cols[4].textContent.trim(), // Phone
                    cols[5].textContent.trim().replace(/[🧔🧕]/g, '').trim(), // Gender
                    cols[6].textContent.trim(), // Umroh
                    cols[7].querySelector('span:nth-child(1)').textContent.trim(), // Event
                    cols[7].querySelector('span:nth-child(2)').textContent.trim(), // Category
                    cols[8].textContent.trim(), // Qty
                    cols[9].textContent.trim().replace(/[Rp\s\.]/g, ''), // Total
                    cols[10].querySelector('span').textContent.trim(), // Status
                    cols[11].textContent.trim() // Method
                ];
                rows.push(rowData);
            });
        } else {
            filename = 'Laporan_Pendaftar_Per_Tiket.csv';
            headers = ['Kode Tiket', 'Invoice No', 'Nama Peserta', 'NIK', 'Email', 'WhatsApp', 'Gender', 'Jawaban Umroh', 'Event Name', 'Category Name', 'Status Tiket', 'Scan Checkin'];
            
            const ticketRows = document.querySelectorAll('.ticket-row[data-matched="true"]');
            ticketRows.forEach(row => {
                const cols = row.querySelectorAll('td');
                let rowData = [
                    cols[0].textContent.trim(), // Kode Tiket
                    cols[1].textContent.trim(), // Invoice No
                    cols[2].textContent.trim(), // Nama Peserta
                    cols[3].textContent.trim(), // NIK
                    cols[4].textContent.trim(), // Email
                    cols[5].textContent.trim(), // Phone
                    cols[6].textContent.trim().replace(/[🧔🧕]/g, '').trim(), // Gender
                    cols[7].textContent.trim(), // Umroh
                    cols[8].textContent.trim(), // Event Name
                    cols[9].textContent.trim(), // Category Name
                    cols[10].querySelector('span').textContent.trim(), // Status Tiket
                    cols[11].textContent.trim() // Scan Checkin
                ];
                rows.push(rowData);
            });
        }

        // Build CSV string
        csv.push(headers.map(h => `"${h.replace(/"/g, '""')}"`).join(','));
        rows.forEach(r => {
            csv.push(r.map(val => `"${val.replace(/"/g, '""')}"`).join(','));
        });

        // Download trigger
        const csvFile = new Blob([new Uint8Array([0xEF, 0xBB, 0xBF]), csv.join('\n')], { type: 'text/csv;charset=utf-8;' });
        const downloadLink = document.createElement('a');
        downloadLink.download = filename;
        downloadLink.href = window.URL.createObjectURL(csvFile);
        downloadLink.style.display = 'none';
        document.body.appendChild(downloadLink);
        downloadLink.click();
        document.body.removeChild(downloadLink);
    }

    // Initialize report tables on page load
    document.addEventListener('DOMContentLoaded', () => {
        filterReportTables();
    });
</script>

