<x-app-layout>
    <x-slot name="title">Ranger Bhayangkara FC 2026/2027</x-slot>

    <!-- Session Alerts -->
    @if(session('success'))
        <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 rounded-2xl flex items-center gap-3 text-emerald-800 text-sm font-medium shadow-sm">
            <div class="w-8 h-8 rounded-xl bg-emerald-500 text-white flex items-center justify-center font-bold shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
            </div>
            <div class="flex-1">{{ session('success') }}</div>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 p-4 bg-rose-50 border border-rose-200 rounded-2xl flex items-center gap-3 text-rose-800 text-sm font-medium shadow-sm">
            <div class="w-8 h-8 rounded-xl bg-rose-500 text-white flex items-center justify-center font-bold shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
            </div>
            <div class="flex-1">{{ session('error') }}</div>
        </div>
    @endif

    <!-- Top Action & Stat Banner -->
    <div class="mb-8 bg-gradient-to-br from-slate-900 via-slate-800 to-slate-950 p-6 sm:p-8 rounded-3xl text-white shadow-xl relative overflow-hidden">
        <div class="absolute -right-10 -bottom-10 w-64 h-64 bg-orange-500/10 rounded-full blur-3xl pointer-events-none"></div>
        
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <span class="px-3 py-1 bg-orange-500/20 text-orange-400 border border-orange-500/30 text-[10px] font-black uppercase tracking-widest rounded-full">Official Ranger System</span>
                <h2 class="text-2xl sm:text-3xl font-black font-outfit uppercase tracking-tight mt-2 text-white">
                    Ranger Bhayangkara FC <span class="text-orange-400">2026/2027</span>
                </h2>
                <p class="text-slate-400 text-xs sm:text-sm mt-1 max-w-xl">
                    Kelola data pendaftar ranger, konfigurasi quota kebutuhan per gate, dan jalankan fitur otomasi <strong>Generate Crew</strong>.
                </p>
            </div>
            
            <div class="flex flex-wrap items-center gap-3 shrink-0">
                <a href="{{ route('ranger.register') }}" target="_blank" class="px-4 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-bold rounded-xl transition flex items-center gap-2 border border-slate-700">
                    <svg class="w-4 h-4 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    <span>Buka Form Public</span>
                </a>

                <a href="{{ route('superadmin.rangers.export') }}" class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-black uppercase tracking-wider rounded-xl transition shadow-lg shadow-emerald-600/20 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    <span>Export CSV</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Stats Summary Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4 mb-8">
        <!-- Total Registered -->
        <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm">
            <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-1">Total Pendaftar</p>
            <h4 class="text-2xl font-black text-slate-900 font-outfit">{{ number_format($stats['total']) }}</h4>
            <span class="text-[10px] font-bold text-slate-400">Orang</span>
        </div>

        <!-- Male -->
        <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm">
            <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-1">Laki-laki (Male)</p>
            <h4 class="text-2xl font-black text-blue-600 font-outfit">{{ number_format($stats['male']) }}</h4>
            <span class="text-[10px] font-bold text-slate-400">Pendaftar</span>
        </div>

        <!-- Female -->
        <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm">
            <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-1">Perempuan (Female)</p>
            <h4 class="text-2xl font-black text-pink-600 font-outfit">{{ number_format($stats['female']) }}</h4>
            <span class="text-[10px] font-bold text-slate-400">Pendaftar</span>
        </div>

        <!-- Assigned -->
        <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm">
            <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-1">Sudah Di-plot</p>
            <h4 class="text-2xl font-black text-emerald-600 font-outfit">{{ number_format($stats['assigned']) }}</h4>
            <span class="text-[10px] font-bold text-emerald-600">Crew Terdaftar</span>
        </div>

        <!-- Unassigned -->
        <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm">
            <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-1">Belum Di-plot</p>
            <h4 class="text-2xl font-black text-amber-600 font-outfit">{{ number_format($stats['unassigned']) }}</h4>
            <span class="text-[10px] font-bold text-amber-600">Standby / Cadangan</span>
        </div>
    </div>

    <!-- Configuration & Crew Generation Section -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6 sm:p-8 mb-8" x-data="{ showQuotaForm: true }">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-slate-100">
            <div>
                <h3 class="text-lg font-black text-slate-900 font-outfit flex items-center gap-2">
                    <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <span>Konfigurasi Quota & Fitur Generate Crew</span>
                </h3>
                <p class="text-xs text-slate-500 mt-1">
                    Atur alokasi kebutuhan ranger Laki-Laki & Perempuan per gate (Gate 1 - 8, VIP, Redemption), lalu tekan <strong>Generate Crew</strong>.
                </p>
            </div>

            <!-- Generate Crew Action Buttons -->
            <div class="flex items-center gap-3">
                <form action="{{ route('superadmin.rangers.generate') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menjalankan Generate Crew? Seluruh plotting ranger akan disesuaikan secara otomatis berdasarkan quota gate.');">
                    @csrf
                    <button type="submit" class="px-5 py-3 bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 text-slate-950 font-black text-xs uppercase tracking-widest rounded-xl transition shadow-lg shadow-orange-500/25 flex items-center gap-2">
                        <svg class="w-4 h-4 text-slate-950" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        <span>Generate Crew</span>
                    </button>
                </form>

                <form action="{{ route('superadmin.rangers.reset') }}" method="POST" onsubmit="return confirm('Reset seluruh plotting gate? Semua ranger akan berstatus Belum Di-plot.');">
                    @csrf
                    <button type="submit" class="px-4 py-3 bg-slate-100 hover:bg-rose-50 hover:text-rose-600 text-slate-600 font-bold text-xs rounded-xl transition flex items-center gap-1.5 border border-slate-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        <span>Reset Plotting</span>
                    </button>
                </form>
            </div>
        </div>

        <!-- Quota Settings Form -->
        <form action="{{ route('superadmin.rangers.update-quotas') }}" method="POST" class="mt-6">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                @foreach($quotas as $index => $quota)
                    <div class="p-4 bg-slate-50/80 rounded-2xl border border-slate-200/60 relative hover:border-orange-300 transition">
                        <input type="hidden" name="quotas[{{ $index }}][id]" value="{{ $quota->id }}">
                        
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-xs font-black font-outfit uppercase tracking-wide text-slate-800 flex items-center gap-1.5">
                                @if($quota->gate_name === 'Redemption')
                                    <span class="w-2 h-2 rounded-full bg-purple-500"></span>
                                @elseif($quota->gate_name === 'VIP')
                                    <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                                @else
                                    <span class="w-2 h-2 rounded-full bg-orange-500"></span>
                                @endif
                                {{ $quota->gate_name }}
                            </span>
                            <span class="text-[10px] font-extrabold px-2 py-0.5 rounded-full bg-slate-200 text-slate-700">
                                Total: {{ $quota->male_quota + $quota->female_quota }}
                            </span>
                        </div>

                        <div class="space-y-2.5 text-xs">
                            <div>
                                <label class="block text-[10px] font-bold uppercase text-slate-500 mb-1">Quota Laki-laki</label>
                                <input type="number" 
                                       name="quotas[{{ $index }}][male_quota]" 
                                       value="{{ $quota->male_quota }}" 
                                       min="0" 
                                       required 
                                       class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-xs font-black text-blue-600 focus:ring-2 focus:ring-orange-500 focus:outline-none">
                            </div>

                            <div>
                                <label class="block text-[10px] font-bold uppercase text-slate-500 mb-1">Quota Perempuan</label>
                                <input type="number" 
                                       name="quotas[{{ $index }}][female_quota]" 
                                       value="{{ $quota->female_quota }}" 
                                       min="0" 
                                       required 
                                       class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-xs font-black text-pink-600 focus:ring-2 focus:ring-orange-500 focus:outline-none">
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-6 flex justify-end">
                <button type="submit" class="px-6 py-2.5 bg-slate-900 hover:bg-slate-800 text-white font-black text-xs uppercase tracking-wider rounded-xl transition shadow-md flex items-center gap-2">
                    <svg class="w-4 h-4 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    <span>Simpan Pengaturan Quota</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Registered Rangers List Card -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
        <!-- Header & Filters -->
        <div class="p-6 border-b border-slate-100 flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            <div>
                <h3 class="text-lg font-black text-slate-900 font-outfit leading-none">Daftar Ranger & Crew</h3>
                <p class="text-xs text-slate-500 mt-1">Seluruh ranger yang terdaftar beserta status plotting pos/gate.</p>
            </div>

            <!-- Search & Filters Form -->
            <form action="{{ route('superadmin.rangers.index') }}" method="GET" class="flex flex-wrap items-center gap-3">
                <!-- Search Input -->
                <div class="relative min-w-[220px]">
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}" 
                           placeholder="Cari nama, WA, bank..." 
                           class="w-full pl-9 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium focus:ring-2 focus:ring-orange-500 focus:bg-white focus:outline-none">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>

                <!-- Gender Filter -->
                <select name="gender" onchange="this.form.submit()" class="px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-700 focus:ring-2 focus:ring-orange-500">
                    <option value="">Semua Gender</option>
                    <option value="male" {{ request('gender') === 'male' ? 'selected' : '' }}>Laki-laki (Male)</option>
                    <option value="female" {{ request('gender') === 'female' ? 'selected' : '' }}>Perempuan (Female)</option>
                </select>

                <!-- Gate Filter -->
                <select name="gate" onchange="this.form.submit()" class="px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-700 focus:ring-2 focus:ring-orange-500">
                    <option value="">Semua Pos / Gate</option>
                    <option value="unassigned" {{ request('gate') === 'unassigned' ? 'selected' : '' }}>Belum Di-plot</option>
                    @foreach($quotas as $q)
                        <option value="{{ $q->gate_name }}" {{ request('gate') === $q->gate_name ? 'selected' : '' }}>{{ $q->gate_name }}</option>
                    @endforeach
                </select>

                @if(request()->anyFilled(['search', 'gender', 'gate']))
                    <a href="{{ route('superadmin.rangers.index') }}" class="px-3 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 text-xs font-bold rounded-xl transition">
                        Reset Filter
                    </a>
                @endif
            </form>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto custom-scrollbar">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-[10px] font-black uppercase text-slate-400 tracking-wider">
                        <th class="py-4 px-6">No</th>
                        <th class="py-4 px-6">Nama Ranger</th>
                        <th class="py-4 px-6">WhatsApp</th>
                        <th class="py-4 px-6">Gender</th>
                        <th class="py-4 px-6">Bank / E-Wallet</th>
                        <th class="py-4 px-6">Posisi Gate</th>
                        <th class="py-4 px-6">Tanggal Daftar</th>
                        <th class="py-4 px-6 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs font-medium text-slate-700">
                    @forelse($rangers as $index => $ranger)
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="py-4 px-6 font-bold text-slate-400">
                                {{ $rangers->firstItem() + $index }}
                            </td>

                            <td class="py-4 px-6">
                                <span class="font-black text-slate-900 block font-outfit text-sm">{{ $ranger->name }}</span>
                                <span class="text-[10px] text-slate-400">ID: #{{ $ranger->id }}</span>
                            </td>

                            <td class="py-4 px-6">
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $ranger->whatsapp) }}" target="_blank" class="inline-flex items-center gap-1.5 text-emerald-600 hover:underline font-bold">
                                    <svg class="w-4 h-4 fill-current text-emerald-500" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-1.149 4.204 4.301-1.127z"/></svg>
                                    <span>{{ $ranger->whatsapp }}</span>
                                </a>
                            </td>

                            <td class="py-4 px-6">
                                @if($ranger->gender === 'male')
                                    <span class="px-2.5 py-1 rounded-full bg-blue-50 text-blue-700 text-[10px] font-black uppercase tracking-wider inline-flex items-center gap-1 border border-blue-200/60">
                                        <svg class="w-3 h-3 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                        Laki-laki
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full bg-pink-50 text-pink-700 text-[10px] font-black uppercase tracking-wider inline-flex items-center gap-1 border border-pink-200/60">
                                        <svg class="w-3 h-3 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                        Perempuan
                                    </span>
                                @endif
                            </td>

                            <td class="py-4 px-6">
                                <span class="font-bold text-slate-800 block">{{ $ranger->bank_name }}</span>
                                <span class="text-xs font-mono text-slate-500">{{ $ranger->account_number }}</span>
                            </td>

                            <td class="py-4 px-6">
                                @if($ranger->assigned_gate)
                                    <span class="px-3 py-1 rounded-xl text-xs font-black uppercase tracking-wider inline-flex items-center gap-1.5
                                        {{ $ranger->assigned_gate === 'VIP' ? 'bg-amber-100 text-amber-900 border border-amber-300' : '' }}
                                        {{ $ranger->assigned_gate === 'Redemption' ? 'bg-purple-100 text-purple-900 border border-purple-300' : '' }}
                                        {{ !in_array($ranger->assigned_gate, ['VIP', 'Redemption']) ? 'bg-orange-100 text-orange-900 border border-orange-300' : '' }}">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                                        {{ $ranger->assigned_gate }}
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-lg bg-slate-100 text-slate-500 text-[10px] font-bold uppercase tracking-wider">
                                        Belum Di-plot
                                    </span>
                                @endif
                            </td>

                            <td class="py-4 px-6 text-slate-500 text-xs">
                                {{ $ranger->created_at ? $ranger->created_at->format('d M Y, H:i') : '-' }}
                            </td>

                            <td class="py-4 px-6 text-center">
                                <div class="flex items-center justify-center gap-2" x-data="{ openAssign: false }">
                                    <!-- Manual Gate Assignment Trigger -->
                                    <button @click="openAssign = !openAssign" type="button" title="Ubah Posisi Gate" class="p-2 text-slate-500 hover:text-orange-600 hover:bg-orange-50 rounded-lg transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>

                                    <!-- Delete Button -->
                                    <form action="{{ route('superadmin.rangers.destroy', $ranger->id) }}" method="POST" onsubmit="return confirm('Hapus pendaftar {{ $ranger->name }}?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" title="Hapus Data" class="p-2 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>

                                    <!-- Inline Assignment Popover / Form -->
                                    <div x-show="openAssign" x-cloak @click.away="openAssign = false" class="absolute right-12 z-30 bg-white p-4 rounded-2xl shadow-xl border border-slate-200 text-left min-w-[200px]">
                                        <form action="{{ route('superadmin.rangers.update-assignment', $ranger->id) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <label class="block text-[10px] font-black uppercase text-slate-400 mb-2">Pilih Pos Gate Manual</label>
                                            <select name="assigned_gate" class="w-full px-3 py-2 text-xs border border-slate-200 rounded-xl mb-3 focus:ring-2 focus:ring-orange-500">
                                                <option value="">-- Belum Di-plot --</option>
                                                @foreach($quotas as $q)
                                                    <option value="{{ $q->gate_name }}" {{ $ranger->assigned_gate === $q->gate_name ? 'selected' : '' }}>{{ $q->gate_name }}</option>
                                                @endforeach
                                            </select>
                                            <button type="submit" class="w-full py-2 bg-orange-500 hover:bg-orange-600 text-slate-950 font-extrabold text-xs uppercase tracking-wider rounded-lg transition">
                                                Simpan
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-slate-400 text-xs font-bold uppercase tracking-wider">
                                Belum ada pendaftar Ranger Bhayangkara FC 2026/2027.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($rangers->hasPages())
            <div class="p-6 border-t border-slate-100">
                {{ $rangers->links() }}
            </div>
        @endif
    </div>

</x-app-layout>
