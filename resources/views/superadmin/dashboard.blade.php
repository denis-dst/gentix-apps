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

    <!-- Early Arrival Spotlight (Rekor Tercepat Masuk Global/Event) -->
    @if($earliestScan)
        <div class="mb-8 bg-gradient-to-r from-amber-50 via-orange-50 to-amber-100/70 border border-amber-200/80 rounded-3xl p-6 shadow-sm">
            <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 bg-amber-500 text-white rounded-2xl flex items-center justify-center shrink-0 shadow-md shadow-amber-500/30">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="px-2.5 py-0.5 bg-amber-500 text-white text-[10px] font-black uppercase rounded-full tracking-wider">
                                Juara 1 Tercepat Masuk
                            </span>
                            <span class="text-xs font-bold text-amber-900">
                                {{ $earliestScan->event->tenant->name ?? 'Tenant' }} &bull; {{ $earliestScan->event->name ?? 'Event' }} &bull; {{ $earliestScan->gate_name ?: 'Main Gate' }}
                            </span>
                        </div>
                        <h3 class="text-xl font-black text-slate-900 font-outfit mt-1">
                            {{ $earliestScan->ticket->visitor_data['name'] ?? $earliestScan->ticket->transaction->customer_name ?? 'Pengunjung' }}
                        </h3>
                        <p class="text-xs text-slate-600 mt-0.5 font-medium">
                            Tiket: <span class="font-bold text-slate-800">{{ $earliestScan->ticket->category->name ?? 'Reguler' }}</span> ({{ $earliestScan->ticket->ticket_code }})
                        </p>
                    </div>
                </div>
                <div class="bg-white/90 backdrop-blur-sm px-5 py-3 rounded-2xl border border-amber-200 text-right shrink-0 w-full md:w-auto">
                    <p class="text-[10px] font-black uppercase tracking-wider text-amber-700">Waktu Kedatangan Terawal</p>
                    <p class="text-base md:text-lg font-black text-slate-900 font-mono">
                        {{ $earliestScan->scanned_at->timezone('Asia/Jakarta')->format('H:i:s') }} <span class="text-xs font-sans text-slate-500">WIB</span>
                    </p>
                    <p class="text-[10px] text-slate-500 font-medium">
                        {{ $earliestScan->scanned_at->timezone('Asia/Jakarta')->format('d M Y') }}
                    </p>
                </div>
            </div>
        </div>
    @endif

    <!-- Scan History & Early Arrival Section -->
    <div id="scan-history" class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden mb-8">
        {{-- Header & Filter Toolbar --}}
        <div class="p-6 border-b border-slate-100 bg-white">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 mb-6">
                <div>
                    <div class="flex items-center gap-2">
                        <h3 class="text-xl font-black text-slate-800 font-outfit leading-none">Riwayat Scan Tiap Event & Gate</h3>
                        <span class="px-2.5 py-0.5 bg-indigo-50 text-indigo-600 text-[10px] font-black rounded-full uppercase tracking-wider">
                            Monitoring Global
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 font-medium mt-1.5">
                        Pantau urutan waktu kehadiran pengunjung terawal dan log aktivitas scanner di seluruh tenant & event.
                    </p>
                </div>
                <div class="flex items-center gap-4 text-xs font-bold text-slate-500">
                    <div>Total Log: <span class="font-black text-slate-900">{{ $totalScans }}</span></div>
                    <div class="h-3 w-px bg-slate-200"></div>
                    <div>Check-In: <span class="font-black text-emerald-600">{{ $totalCheckIn }}</span></div>
                    <div class="h-3 w-px bg-slate-200"></div>
                    <div>Check-Out: <span class="font-black text-blue-600">{{ $totalCheckOut }}</span></div>
                </div>
            </div>

            {{-- Filter Form --}}
            <form method="GET" action="{{ route('superadmin.dashboard') }}#scan-history" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3">
                {{-- Filter Tenant --}}
                <div>
                    <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-1">Penyedia Event</label>
                    <select name="tenant_id" class="w-full bg-slate-50 border border-slate-200 text-slate-800 text-xs rounded-xl px-3 py-2.5 font-bold focus:ring-2 focus:ring-indigo-500 focus:outline-none transition">
                        <option value="">Semua Penyedia</option>
                        @foreach($tenantOptions as $t)
                            <option value="{{ $t->id }}" {{ (string) $selectedTenantId === (string) $t->id ? 'selected' : '' }}>
                                {{ $t->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Filter Event --}}
                <div>
                    <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-1">Event</label>
                    <select name="event_id" class="w-full bg-slate-50 border border-slate-200 text-slate-800 text-xs rounded-xl px-3 py-2.5 font-bold focus:ring-2 focus:ring-indigo-500 focus:outline-none transition">
                        <option value="">Semua Event</option>
                        @foreach($eventOptions as $ev)
                            <option value="{{ $ev->id }}" {{ (string) $selectedEventId === (string) $ev->id ? 'selected' : '' }}>
                                {{ $ev->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Filter Gate --}}
                <div>
                    <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-1">Gate / Pintu</label>
                    <select name="gate_name" class="w-full bg-slate-50 border border-slate-200 text-slate-800 text-xs rounded-xl px-3 py-2.5 font-bold focus:ring-2 focus:ring-indigo-500 focus:outline-none transition">
                        <option value="">Semua Gate</option>
                        @foreach($gateOptions as $gate)
                            <option value="{{ $gate }}" {{ $selectedGate === $gate ? 'selected' : '' }}>
                                {{ $gate }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Filter Tipe Scan --}}
                <div>
                    <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-1">Tipe Scan</label>
                    <select name="scan_type" class="w-full bg-slate-50 border border-slate-200 text-slate-800 text-xs rounded-xl px-3 py-2.5 font-bold focus:ring-2 focus:ring-indigo-500 focus:outline-none transition">
                        <option value="">Semua (IN / OUT)</option>
                        <option value="IN" {{ $selectedType === 'IN' ? 'selected' : '' }}>Check-In (Masuk)</option>
                        <option value="OUT" {{ $selectedType === 'OUT' ? 'selected' : '' }}>Check-Out (Keluar)</option>
                    </select>
                </div>

                {{-- Filter Urutan / Sort --}}
                <div>
                    <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-1">Urutan Waktu</label>
                    <select name="sort" class="w-full bg-slate-50 border border-slate-200 text-slate-800 text-xs rounded-xl px-3 py-2.5 font-bold focus:ring-2 focus:ring-indigo-500 focus:outline-none transition">
                        <option value="earliest" {{ $sort === 'earliest' ? 'selected' : '' }}>Tercepat Datang</option>
                        <option value="latest" {{ $sort === 'latest' ? 'selected' : '' }}>Terbaru Di-Scan</option>
                    </select>
                </div>

                {{-- Search Input & Actions --}}
                <div class="flex items-end gap-2">
                    <div class="flex-1">
                        <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-1">Cari Pengunjung</label>
                        <input type="text" name="search" value="{{ $search }}" placeholder="Nama / Kode..."
                               class="w-full bg-slate-50 border border-slate-200 text-slate-800 text-xs rounded-xl px-3 py-2.5 font-medium focus:ring-2 focus:ring-indigo-500 focus:outline-none transition">
                    </div>
                    <button type="submit" class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-black text-xs uppercase tracking-wider transition shadow-sm h-[42px] flex items-center justify-center">
                        Filter
                    </button>
                    @if($selectedTenantId || $selectedEventId || $selectedGate || $selectedType || $search || $sort !== 'earliest')
                        <a href="{{ route('superadmin.dashboard') }}#scan-history" class="px-3 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl font-bold text-xs transition h-[42px] flex items-center justify-center" title="Reset filter">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- Table & List --}}
        @if($scanLogs->isEmpty())
            <div class="p-12 text-center">
                <div class="w-16 h-16 bg-slate-50 rounded-2xl flex items-center justify-center mx-auto mb-4 text-slate-300">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h4 class="text-base font-black text-slate-700 font-outfit">Belum ada data scan tiket</h4>
                <p class="text-xs text-slate-500 max-w-sm mx-auto mt-1 mb-6">
                    @if($selectedTenantId || $selectedEventId || $selectedGate || $selectedType || $search)
                        Tidak ada log scan yang cocok dengan filter yang Anda tentukan. Silakan ubah filter.
                    @else
                        Seluruh aktivitas scan tiket di gate scanner akan terekam di sini secara real-time.
                    @endif
                </p>
                @if($selectedTenantId || $selectedEventId || $selectedGate || $selectedType || $search)
                    <a href="{{ route('superadmin.dashboard') }}#scan-history" class="inline-flex items-center gap-2 px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-bold text-xs transition">
                        Reset Filter
                    </a>
                @endif
            </div>
        @else
            <div class="overflow-x-auto custom-scrollbar">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/70 border-b border-slate-100">
                            <th class="p-4 md:px-6 md:py-3.5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Urutan / Rank</th>
                            <th class="p-4 md:px-6 md:py-3.5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Pengunjung</th>
                            <th class="p-4 md:px-6 md:py-3.5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Event & Tenant</th>
                            <th class="p-4 md:px-6 md:py-3.5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Gate</th>
                            <th class="p-4 md:px-6 md:py-3.5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Tiket & Kategori</th>
                            <th class="p-4 md:px-6 md:py-3.5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Waktu Scan</th>
                            <th class="p-4 md:px-6 md:py-3.5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Status</th>
                            <th class="p-4 md:px-6 md:py-3.5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Petugas</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($scanLogs as $index => $log)
                            @php
                                $rankNumber = ($scanLogs->currentPage() - 1) * $scanLogs->perPage() + $loop->iteration;
                                $visitorName = $log->ticket->visitor_data['name'] ?? $log->ticket->transaction->customer_name ?? '-';
                                $visitorContact = $log->ticket->visitor_data['phone'] ?? $log->ticket->transaction->customer_phone ?? ($log->ticket->visitor_data['email'] ?? $log->ticket->transaction->customer_email ?? '-');
                                $categoryColor = $log->ticket->category->hex_color ?? '#6366f1';
                            @endphp
                            <tr class="hover:bg-slate-50/50 transition-colors group">
                                {{-- Rank / Urutan --}}
                                <td class="p-4 md:px-6 md:py-4">
                                    @if($sort === 'earliest')
                                        @if($rankNumber === 1)
                                            <div class="inline-flex items-center gap-1.5 px-3 py-1 bg-amber-500 text-white rounded-lg text-xs font-black shadow-sm">
                                                <span>#1</span>
                                                <span class="text-[9px] uppercase tracking-wider font-bold">Tercepat</span>
                                            </div>
                                        @elseif($rankNumber === 2)
                                            <div class="inline-flex items-center gap-1 px-2.5 py-1 bg-slate-700 text-white rounded-lg text-xs font-black">
                                                <span>#2</span>
                                                <span class="text-[9px] uppercase tracking-wider font-bold">Ke-2</span>
                                            </div>
                                        @elseif($rankNumber === 3)
                                            <div class="inline-flex items-center gap-1 px-2.5 py-1 bg-amber-700 text-white rounded-lg text-xs font-black">
                                                <span>#3</span>
                                                <span class="text-[9px] uppercase tracking-wider font-bold">Ke-3</span>
                                            </div>
                                        @else
                                            <span class="text-xs font-black text-slate-600 font-mono">
                                                #{{ $rankNumber }}
                                            </span>
                                        @endif
                                    @else
                                        <span class="text-xs font-black text-slate-500 font-mono">
                                            #{{ $rankNumber }}
                                        </span>
                                    @endif
                                </td>

                                {{-- Pengunjung --}}
                                <td class="p-4 md:px-6 md:py-4">
                                    <div class="font-black text-slate-900 text-sm font-outfit leading-tight group-hover:text-indigo-600 transition-colors">
                                        {{ $visitorName }}
                                    </div>
                                    <div class="text-[11px] text-slate-500 font-medium mt-0.5">
                                        {{ $visitorContact }}
                                    </div>
                                </td>

                                {{-- Event & Tenant --}}
                                <td class="p-4 md:px-6 md:py-4">
                                    <div class="text-xs font-bold text-slate-800 truncate max-w-[150px]">
                                        {{ $log->event->name ?? '-' }}
                                    </div>
                                    <div class="text-[10px] text-slate-500 font-medium truncate max-w-[150px]">
                                        {{ $log->event->tenant->name ?? 'Partner' }}
                                    </div>
                                </td>

                                {{-- Gate --}}
                                <td class="p-4 md:px-6 md:py-4">
                                    <span class="px-2.5 py-1 bg-slate-100 text-slate-800 rounded-lg text-[10px] font-black uppercase tracking-wider">
                                        {{ $log->gate_name ?: 'Main Gate' }}
                                    </span>
                                </td>

                                {{-- Tiket & Kategori --}}
                                <td class="p-4 md:px-6 md:py-4">
                                    <div class="flex items-center gap-2">
                                        <span class="w-2.5 h-2.5 rounded-full shrink-0" style="background-color: {{ $categoryColor }};"></span>
                                        <span class="text-xs font-bold text-slate-800">{{ $log->ticket->category->name ?? 'Reguler' }}</span>
                                    </div>
                                    <div class="text-[10px] text-slate-500 font-mono mt-0.5">
                                        {{ $log->ticket->ticket_code }}
                                        @if($log->ticket->wristband_qr)
                                            <span class="text-slate-400">&bull; {{ $log->ticket->wristband_qr }}</span>
                                        @endif
                                    </div>
                                </td>

                                {{-- Waktu Scan --}}
                                <td class="p-4 md:px-6 md:py-4">
                                    <div class="text-xs font-black text-slate-900 font-mono">
                                        {{ $log->scanned_at->timezone('Asia/Jakarta')->format('H:i:s') }} <span class="text-[10px] text-slate-500 font-sans">WIB</span>
                                    </div>
                                    <div class="text-[10px] text-slate-500 font-medium mt-0.5">
                                        {{ $log->scanned_at->timezone('Asia/Jakarta')->format('d M Y') }}
                                    </div>
                                </td>

                                {{-- Status IN/OUT --}}
                                <td class="p-4 md:px-6 md:py-4">
                                    @if($log->type === 'IN')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-lg text-[10px] font-black uppercase tracking-wider">
                                            <svg class="w-3 h-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" /></svg>
                                            Masuk (IN)
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-blue-50 text-blue-700 border border-blue-200 rounded-lg text-[10px] font-black uppercase tracking-wider">
                                            <svg class="w-3 h-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
                                            Keluar (OUT)
                                        </span>
                                    @endif
                                </td>

                                {{-- Petugas Scanner --}}
                                <td class="p-4 md:px-6 md:py-4">
                                    <span class="text-xs font-medium text-slate-700">
                                        {{ $log->scanner->name ?? 'System' }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="p-4 md:px-6 border-t border-slate-100 bg-slate-50/50">
                {{ $scanLogs->links() }}
            </div>
        @endif
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
