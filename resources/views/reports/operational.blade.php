@php
    $isSuperadminReport = ($scope ?? '') === 'superadmin';
    $reportRoute = $isSuperadminReport ? route('superadmin.reports.index') : route('organizer.reports.index');
    $formatWaUrl = function ($phone) {
        $number = preg_replace('/\D+/', '', (string) $phone);

        if ($number === '') {
            return null;
        }

        if (str_starts_with($number, '0')) {
            $number = '62' . substr($number, 1);
        } elseif (!str_starts_with($number, '62')) {
            $number = '62' . $number;
        }

        return 'https://wa.me/' . $number;
    };

    $reportTransactionRows = collect($transactionReportRows ?? null);

    if ($reportTransactionRows->isEmpty() && isset($transactions)) {
        $reportTransactionRows = collect($transactions)->map(function ($transaction) {
            $proofTicket = $transaction->tickets->first(function ($ticket) {
                $visitorData = is_array($ticket->visitor_data) ? $ticket->visitor_data : [];

                return !empty($visitorData['proof_ig']) || !empty($visitorData['proof_review']);
            });

            $proofData = $proofTicket && is_array($proofTicket->visitor_data) ? $proofTicket->visitor_data : [];

            return [
                'reference_no' => $transaction->reference_no,
                'created_at' => $transaction->created_at,
                'customer_name' => $transaction->customer_name,
                'customer_nik' => $transaction->customer_nik,
                'customer_email' => $transaction->customer_email,
                'customer_phone' => $transaction->customer_phone,
                'customer_gender' => $transaction->customer_gender,
                'customer_umroh_answer' => $transaction->customer_umroh_answer,
                'event_name' => $transaction->event->name ?? '-',
                'category_name' => $transaction->category->name ?? 'Mixed',
                'quantity' => $transaction->quantity,
                'total_amount' => $transaction->total_amount,
                'payment_status' => $transaction->payment_status,
                'payment_method' => $transaction->payment_method,
                'proof_ig' => $proofData['proof_ig'] ?? null,
                'proof_review' => $proofData['proof_review'] ?? null,
            ];
        })->values();
    }
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
            @php $event = $row['event']; @endphp
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
                    Export Data
                </button>
            </div>
        </div>

        <!-- Tab Buttons (Glassmorphic design) -->
        <div class="px-6 py-3 border-b border-slate-100 flex items-center gap-2 bg-white">
            <button type="button" 
                    @click="activeReportTab = 'transactions'; window.activeTab = 'transactions'; filterReportTables()" 
                    :class="activeReportTab === 'transactions' ? 'bg-orange-600 text-black' : 'bg-slate-100 text-slate-500 hover:bg-slate-200'"
                    class="px-4 py-2 rounded-xl text-xs font-black uppercase tracking-wider transition duration-200">
                📂 Per Transaksi (Satuan)
            </button>
            <button type="button" 
                    @click="activeReportTab = 'tickets'; window.activeTab = 'tickets'; filterReportTables()" 
                    :class="activeReportTab === 'tickets' ? 'bg-orange-600 text-black' : 'bg-slate-100 text-slate-500 hover:bg-slate-200'"
                    class="px-4 py-2 rounded-xl text-xs font-black uppercase tracking-wider transition duration-200">
                🎫 Per Tiket (Detail Peserta)
            </button>
        </div>

        <!-- 1. Laporan Per Transaksi (Satuan) -->
        <div id="report-tab-transactions" x-show="activeReportTab === 'transactions'">
            <div class="overflow-x-auto">
                <table id="table-report-tx" class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/70 border-b border-slate-100">
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Waktu & Invoice</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Nama Pemesan</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">NIK</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Email</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">WhatsApp</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Upload Bukti IG</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Upload Bukti Review</th>
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
                        @php $transactionRow = []; @endphp
                        @if($reportTransactionRows->isEmpty())
                            <tr class="no-data-tx">
                                <td colspan="14" class="px-6 py-10 text-center text-sm font-bold text-slate-400">Belum ada transaksi pendaftaran.</td>
                            </tr>
                        @else
                            @foreach($reportTransactionRows as $transactionRow)
                            @php
                                $referenceNo = $transactionRow['reference_no'] ?? '-';
                                $createdAt = $transactionRow['created_at'] ?? null;
                                $createdAtText = $createdAt && method_exists($createdAt, 'format') ? $createdAt->format('d M Y H:i') : '-';
                                $customerName = $transactionRow['customer_name'] ?? '-';
                                $customerNik = $transactionRow['customer_nik'] ?? '-';
                                $customerEmail = $transactionRow['customer_email'] ?? '-';
                                $customerPhone = $transactionRow['customer_phone'] ?? null;
                                $customerGender = $transactionRow['customer_gender'] ?? null;
                                $customerUmrohAnswer = $transactionRow['customer_umroh_answer'] ?? '-';
                                $eventName = $transactionRow['event_name'] ?? '-';
                                $categoryName = $transactionRow['category_name'] ?? 'Mixed';
                                $quantity = $transactionRow['quantity'] ?? 0;
                                $totalAmount = $transactionRow['total_amount'] ?? 0;
                                $paymentStatus = $transactionRow['payment_status'] ?? 'unknown';
                                $paymentMethod = $transactionRow['payment_method'] ?? 'Unknown';
                                $proofIgUrl = !empty($transactionRow['proof_ig']) ? asset('storage/' . $transactionRow['proof_ig']) : null;
                                $proofReviewUrl = !empty($transactionRow['proof_review']) ? asset('storage/' . $transactionRow['proof_review']) : null;
                                $waUrl = $formatWaUrl($customerPhone);
                            @endphp
                            <tr class="tx-row hover:bg-slate-50/40 transition">
                                <td class="px-6 py-4 text-xs font-black text-slate-600 font-mono">{{ $referenceNo }}<br><span class="text-[9px] font-bold text-slate-400 font-sans block mt-0.5">{{ $createdAtText }}</span></td>
                                <td class="px-6 py-4 text-sm font-black text-slate-800 uppercase">{{ $customerName }}</td>
                                <td class="px-6 py-4 text-xs text-slate-600">{{ $customerNik }}</td>
                                <td class="px-6 py-4 text-xs text-slate-600">{{ $customerEmail }}</td>
                                <td class="px-6 py-4 text-xs text-slate-600">
                                    @if($waUrl)
                                        <a href="{{ $waUrl }}" target="_blank" rel="noopener noreferrer" class="font-black text-emerald-600 hover:text-emerald-700 hover:underline whitespace-nowrap">
                                            {{ $customerPhone }}
                                        </a>
                                    @else
                                        <span class="text-slate-300">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center proof-status" data-export="{{ $proofIgUrl ? 'Sudah Upload' : 'Belum Upload' }}">
                                    @if($proofIgUrl)
                                        <button type="button" onclick="openProofPreview(@js($proofIgUrl), @js('Bukti Follow IG - ' . $referenceNo))" class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-emerald-50 text-emerald-600 hover:bg-emerald-600 hover:text-white transition" title="Lihat Bukti Follow IG">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" /></svg>
                                        </button>
                                    @else
                                        <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-rose-50 text-rose-500" title="Belum Upload">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12" /></svg>
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center proof-status" data-export="{{ $proofReviewUrl ? 'Sudah Upload' : 'Belum Upload' }}">
                                    @if($proofReviewUrl)
                                        <button type="button" onclick="openProofPreview(@js($proofReviewUrl), @js('Bukti Google Review - ' . $referenceNo))" class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-emerald-50 text-emerald-600 hover:bg-emerald-600 hover:text-white transition" title="Lihat Bukti Google Review">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" /></svg>
                                        </button>
                                    @else
                                        <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-rose-50 text-rose-500" title="Belum Upload">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12" /></svg>
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-xs">
                                    @if($customerGender)
                                        <span class="font-black uppercase {{ $customerGender === 'ikhwan' ? 'text-blue-600' : 'text-pink-600' }}">
                                            {{ $customerGender === 'ikhwan' ? 'Ikhwan' : 'Akhwat' }}
                                        </span>
                                    @else
                                        <span class="text-slate-300">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-xs text-slate-600 max-w-[200px] truncate" title="{{ $customerUmrohAnswer }}">{{ $customerUmrohAnswer }}</td>
                                <td class="px-6 py-4 text-xs">
                                    <span class="font-bold text-slate-700 block">{{ $eventName }}</span>
                                    <span class="text-[10px] text-orange-500 font-black uppercase tracking-wider block mt-0.5">{{ $categoryName }}</span>
                                </td>
                                <td class="px-6 py-4 text-right text-sm font-black text-slate-700">{{ $quantity }}</td>
                                <td class="px-6 py-4 text-right text-sm font-black text-green-600">Rp {{ number_format($totalAmount, 0, ',', '.') }}</td>
                                <td class="px-6 py-4">
                                    @if($paymentStatus === 'paid')
                                        <span class="px-2 py-1 rounded bg-emerald-50 text-emerald-600 text-[9px] font-black uppercase tracking-wider">PAID</span>
                                    @elseif($paymentStatus === 'refunded')
                                        <span class="px-2 py-1 rounded bg-rose-50 text-rose-600 text-[9px] font-black uppercase tracking-wider">REFUNDED</span>
                                    @else
                                        <span class="px-2 py-1 rounded bg-amber-50 text-amber-600 text-[9px] font-black uppercase tracking-wider">{{ strtoupper($paymentStatus) }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-xs text-slate-600 uppercase">{{ $paymentMethod }}</td>
                            </tr>
                            @endforeach
                        @endif
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
        <div id="report-tab-tickets" x-show="activeReportTab === 'tickets'" x-cloak>
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
                        @forelse($ticketReportRows as $ticketRow)
                            @php $ticketPhone = $ticketRow['phone']; @endphp
                            @php $ticketWaUrl = $formatWaUrl($ticketPhone); @endphp
                                <tr class="ticket-row hover:bg-slate-50/40 transition">
                                    <td class="px-6 py-4 text-xs font-black text-slate-700 font-mono">{{ $ticketRow['ticket_code'] }}</td>
                                    <td class="px-6 py-4 text-xs font-bold text-slate-500 font-mono">{{ $ticketRow['reference_no'] }}</td>
                                    <td class="px-6 py-4 text-sm font-black text-slate-800 uppercase">{{ $ticketRow['name'] }}</td>
                                    <td class="px-6 py-4 text-xs text-slate-600">{{ $ticketRow['nik'] }}</td>
                                    <td class="px-6 py-4 text-xs text-slate-600">{{ $ticketRow['email'] }}</td>
                                    <td class="px-6 py-4 text-xs text-slate-600">
                                        @if($ticketWaUrl)
                                            <a href="{{ $ticketWaUrl }}" target="_blank" rel="noopener noreferrer" class="font-black text-emerald-600 hover:text-emerald-700 hover:underline whitespace-nowrap">
                                                {{ $ticketPhone }}
                                            </a>
                                        @else
                                            <span class="text-slate-300">-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-xs">
                                        @php $tGender = $ticketRow['gender']; @endphp
                                        @if($tGender)
                                            <span class="font-black uppercase {{ $tGender === 'ikhwan' ? 'text-blue-600' : 'text-pink-600' }}">
                                                {{ $tGender === 'ikhwan' ? '🧔 Ikhwan' : '🧕 Akhwat' }}
                                            </span>
                                        @else
                                            <span class="text-slate-300">-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-xs text-slate-600 max-w-[200px] truncate" title="{{ $ticketRow['umroh_answer'] }}">{{ $ticketRow['umroh_answer'] ?? '-' }}</td>
                                    <td class="px-6 py-4 text-xs text-slate-700 font-bold">{{ $ticketRow['event_name'] }}</td>
                                    <td class="px-6 py-4 text-xs text-orange-500 font-black uppercase tracking-wider">{{ $ticketRow['category_name'] }}</td>
                                    <td class="px-6 py-4">
                                        @if($ticketRow['status'] === 'sold')
                                            <span class="px-2 py-0.5 rounded bg-blue-50 text-blue-600 font-bold text-[9px] uppercase tracking-wide">SOLD</span>
                                        @elseif($ticketRow['status'] === 'redeemed')
                                            <span class="px-2 py-0.5 rounded bg-emerald-50 text-emerald-600 font-bold text-[9px] uppercase tracking-wide">REDEEMED</span>
                                        @elseif($ticketRow['status'] === 'void')
                                            <span class="px-2 py-0.5 rounded bg-rose-50 text-rose-600 font-bold text-[9px] uppercase tracking-wide">VOID</span>
                                        @else
                                            <span class="px-2 py-0.5 rounded bg-slate-50 text-slate-500 font-bold text-[9px] uppercase tracking-wide">{{ strtoupper($ticketRow['status']) }}</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-xs text-slate-500">
                                        {{ $ticketRow['redeemed_at'] ? $ticketRow['redeemed_at']->format('d M Y H:i') . ' WIB' : 'Belum Scan' }}
                                    </td>
                                </tr>
                        @empty
                            <tr class="no-data-tickets">
                                <td colspan="12" class="px-6 py-10 text-center text-sm font-bold text-slate-400">Belum ada detail tiket peserta.</td>
                            </tr>
                        @endforelse
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

<div id="proof-preview-modal" class="fixed inset-0 z-[80] hidden items-center justify-center p-4">
    <div class="absolute inset-0 bg-slate-900/70 backdrop-blur-sm" onclick="closeProofPreview()"></div>
    <div class="relative w-full max-w-3xl overflow-hidden rounded-3xl bg-white shadow-2xl border border-slate-100">
        <div class="flex items-center justify-between gap-4 border-b border-slate-100 bg-slate-50 px-6 py-4">
            <h3 id="proof-preview-title" class="text-sm font-black text-slate-800 uppercase tracking-wider">Bukti Upload</h3>
            <button type="button" onclick="closeProofPreview()" class="rounded-full p-2 text-slate-400 hover:bg-white hover:text-slate-700 transition">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        </div>
        <div class="max-h-[75vh] overflow-auto bg-slate-100 p-4">
            <img id="proof-preview-image" src="" alt="Bukti upload" class="mx-auto max-h-[70vh] max-w-full rounded-2xl bg-white object-contain shadow-sm">
        </div>
        <div class="flex justify-end border-t border-slate-100 bg-white px-6 py-4">
            <a id="proof-preview-link" href="#" target="_blank" rel="noopener noreferrer" class="px-4 py-2 rounded-xl bg-orange-600 text-black text-xs font-black uppercase tracking-wider hover:bg-orange-700 transition">Buka File</a>
        </div>
    </div>
</div>

<script>
    // Tab switching state attached to window to ensure global availability
    window.activeTab = 'transactions';
    window.txCurrentPage = 1;
    window.ticketsCurrentPage = 1;
    window.itemsPerPage = 15;

    window.switchReportTab = function(tab) {
        window.activeTab = tab;
        window.filterReportTables(); // Recalculate pagination
    }

    window.filterReportTables = function() {
        const queryInput = document.getElementById('report-search-input');
        const query = queryInput ? queryInput.value.toLowerCase().trim() : '';
        
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
        const totalTxPages = Math.ceil(totalTx / window.itemsPerPage) || 1;
        if (window.txCurrentPage > totalTxPages) window.txCurrentPage = totalTxPages;
        
        const txStartIdx = (window.txCurrentPage - 1) * window.itemsPerPage;
        const txEndIdx = Math.min(txStartIdx + window.itemsPerPage, totalTx);

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
        const totalTicketPages = Math.ceil(totalTickets / window.itemsPerPage) || 1;
        if (window.ticketsCurrentPage > totalTicketPages) window.ticketsCurrentPage = totalTicketPages;

        const ticketStartIdx = (window.ticketsCurrentPage - 1) * window.itemsPerPage;
        const ticketEndIdx = Math.min(ticketStartIdx + window.itemsPerPage, totalTickets);

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

    window.prevReportPage = function(tab) {
        if (tab === 'transactions') {
            if (window.txCurrentPage > 1) {
                window.txCurrentPage--;
                window.filterReportTables();
            }
        } else {
            if (window.ticketsCurrentPage > 1) {
                window.ticketsCurrentPage--;
                window.filterReportTables();
            }
        }
    }

    window.nextReportPage = function(tab) {
        if (tab === 'transactions') {
            const matchedRows = document.querySelectorAll('.tx-row[data-matched="true"]').length;
            const totalPages = Math.ceil(matchedRows / window.itemsPerPage);
            if (window.txCurrentPage < totalPages) {
                window.txCurrentPage++;
                window.filterReportTables();
            }
        } else {
            const matchedRows = document.querySelectorAll('.ticket-row[data-matched="true"]').length;
            const totalPages = Math.ceil(matchedRows / window.itemsPerPage);
            if (window.ticketsCurrentPage < totalPages) {
                window.ticketsCurrentPage++;
                window.filterReportTables();
            }
        }
    }

    window.exportActiveTableToCSV = function() {
        let csv = [];
        let filename = '';
        let headers = [];
        let rows = [];

        if (window.activeTab === 'transactions') {
            filename = 'Laporan_Pendaftar_Per_Transaksi.csv';
            headers = ['Invoice No', 'Tanggal', 'Nama Pemesan', 'NIK', 'Email', 'WhatsApp', 'Upload Bukti IG', 'Upload Bukti Review', 'Gender', 'Jawaban Umroh', 'Event Name', 'Category Name', 'Quantity', 'Total Amount', 'Status', 'Payment Method'];
            
            // Extract matching rows
            const txRows = document.querySelectorAll('.tx-row[data-matched="true"]');
            txRows.forEach(row => {
                const cols = row.querySelectorAll('td');
                let rowData;
                try {
                    rowData = [
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
                } catch (error) {
                    rowData = [];
                }
                rowData = [
                    cols[0].firstChild.textContent.trim(),
                    cols[0].querySelector('span').textContent.trim(),
                    cols[1].textContent.trim(),
                    cols[2].textContent.trim(),
                    cols[3].textContent.trim(),
                    cols[4].textContent.trim(),
                    cols[5].getAttribute('data-export') || cols[5].textContent.trim(),
                    cols[6].getAttribute('data-export') || cols[6].textContent.trim(),
                    cols[7].textContent.trim().replace(/[ðŸ§”ðŸ§•]/g, '').trim(),
                    cols[8].textContent.trim(),
                    cols[9].querySelector('span:nth-child(1)').textContent.trim(),
                    cols[9].querySelector('span:nth-child(2)').textContent.trim(),
                    cols[10].textContent.trim(),
                    cols[11].textContent.trim().replace(/[Rp\s\.]/g, ''),
                    cols[12].querySelector('span').textContent.trim(),
                    cols[13].textContent.trim()
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

    window.openProofPreview = function(url, title) {
        const modal = document.getElementById('proof-preview-modal');
        const image = document.getElementById('proof-preview-image');
        const link = document.getElementById('proof-preview-link');
        const titleElement = document.getElementById('proof-preview-title');

        if (!modal || !image || !link || !titleElement) {
            return;
        }

        image.src = url;
        link.href = url;
        titleElement.textContent = title || 'Bukti Upload';
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    window.closeProofPreview = function() {
        const modal = document.getElementById('proof-preview-modal');
        const image = document.getElementById('proof-preview-image');

        if (!modal || !image) {
            return;
        }

        modal.classList.add('hidden');
        modal.classList.remove('flex');
        image.src = '';
    }

    // Initialize report tables on page load or on dynamic navigation load
    function initOperationalReports() {
        window.filterReportTables();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initOperationalReports);
    } else {
        initOperationalReports();
    }
</script>
