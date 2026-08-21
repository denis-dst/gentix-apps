<x-app-layout>
    <x-slot name="title">Ranger Bhayangkara FC 2026/2027</x-slot>

    <!-- Top Action & Notification Banner -->
    <div class="mb-8">
        @if(session('success'))
            <div class="mb-6 flex items-center gap-3 px-4 py-3.5 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl text-xs font-bold shadow-sm">
                <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 flex items-center gap-3 px-4 py-3.5 bg-rose-50 border border-rose-200 text-rose-800 rounded-2xl text-xs font-bold shadow-sm">
                <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <!-- Main Banner -->
        <div class="bg-white rounded-3xl p-6 sm:p-8 border-2 border-slate-200 shadow-sm relative overflow-hidden flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <div class="flex items-center gap-2 mb-2">
                    <span class="px-3 py-1 bg-orange-100 text-orange-950 border border-orange-200 text-[10px] font-black uppercase tracking-widest rounded-full">Official Ranger System</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black font-outfit text-slate-950 tracking-tight">
                    Ranger Bhayangkara FC <span class="text-orange-600">2026/2027</span>
                </h1>
                <p class="text-xs sm:text-sm text-slate-700 font-semibold mt-1 max-w-2xl leading-relaxed">
                    Kelola data pendaftar ranger, atur status <strong>⭐ SPV</strong> & <strong>🔴 Offday</strong>, konfigurasi quota kebutuhan per gate, dan jalankan fitur otomasi <strong>Generate Crew</strong>.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-3 shrink-0">
                <a href="{{ route('ranger.register') }}" target="_blank" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-900 text-xs font-black rounded-xl transition flex items-center gap-2 border border-slate-300">
                    <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    <span>Buka Form Public</span>
                </a>

                <a href="{{ route('superadmin.rangers.export') }}" class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-black uppercase tracking-wider rounded-xl transition shadow-md shadow-emerald-600/20 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    <span>Export CSV</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Stats Summary Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4 mb-8">
        <!-- Total Registered -->
        <div class="bg-white p-5 rounded-2xl border-2 border-slate-200 shadow-sm">
            <p class="text-[10px] font-black uppercase tracking-widest text-slate-700 mb-1">Total Pendaftar</p>
            <h4 class="text-2xl font-black text-slate-950 font-outfit">{{ number_format($stats['total']) }}</h4>
            <span class="text-[10px] font-bold text-slate-500">Orang</span>
        </div>

        <!-- SPV (Supervisor) -->
        <div class="bg-white p-5 rounded-2xl border-2 border-amber-300 shadow-sm bg-gradient-to-br from-amber-50/50 to-white">
            <p class="text-[10px] font-black uppercase tracking-widest text-amber-900 mb-1">⭐ Supervisor (SPV)</p>
            <h4 class="text-2xl font-black text-amber-700 font-outfit">{{ number_format($stats['spv']) }}</h4>
            <span class="text-[10px] font-black text-amber-800">Tidak di-plot gate</span>
        </div>

        <!-- Offday -->
        <div class="bg-white p-5 rounded-2xl border-2 border-rose-200 shadow-sm bg-gradient-to-br from-rose-50/50 to-white">
            <p class="text-[10px] font-black uppercase tracking-widest text-rose-900 mb-1">🔴 Offday / Libur</p>
            <h4 class="text-2xl font-black text-rose-700 font-outfit">{{ number_format($stats['offday']) }}</h4>
            <span class="text-[10px] font-black text-rose-800">Tidak di-plot gate</span>
        </div>

        <!-- Male Ready -->
        <div class="bg-white p-5 rounded-2xl border-2 border-slate-200 shadow-sm">
            <p class="text-[10px] font-black uppercase tracking-widest text-slate-700 mb-1">Laki-laki (Male)</p>
            <h4 class="text-2xl font-black text-blue-700 font-outfit">{{ number_format($stats['male']) }}</h4>
            <span class="text-[10px] font-bold text-slate-500">Pendaftar</span>
        </div>

        <!-- Female Ready -->
        <div class="bg-white p-5 rounded-2xl border-2 border-slate-200 shadow-sm">
            <p class="text-[10px] font-black uppercase tracking-widest text-slate-700 mb-1">Perempuan (Female)</p>
            <h4 class="text-2xl font-black text-pink-700 font-outfit">{{ number_format($stats['female']) }}</h4>
            <span class="text-[10px] font-bold text-slate-500">Pendaftar</span>
        </div>

        <!-- Assigned -->
        <div class="bg-white p-5 rounded-2xl border-2 border-slate-200 shadow-sm">
            <p class="text-[10px] font-black uppercase tracking-widest text-slate-700 mb-1">Sudah Di-plot</p>
            <h4 class="text-2xl font-black text-emerald-700 font-outfit">{{ number_format($stats['assigned']) }}</h4>
            <span class="text-[10px] font-black text-emerald-800">Crew Pos Gate</span>
        </div>
    </div>

    <!-- Configuration & Crew Generation Section -->
    <div class="bg-white rounded-3xl border-2 border-slate-200 shadow-sm p-6 sm:p-8 mb-8" x-data="{ showQuotaForm: true }">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-slate-200">
            <div>
                <h3 class="text-lg font-black text-slate-950 font-outfit flex items-center gap-2">
                    <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <span>Konfigurasi Quota & Fitur Generate Crew</span>
                </h3>
                <p class="text-xs text-slate-700 font-semibold mt-1">
                    Atur alokasi kebutuhan ranger Laki-Laki & Perempuan per gate, lalu tekan <strong>Generate Crew</strong>.
                    <span class="text-orange-700 block mt-0.5 font-bold">ℹ️ Catatan: Ranger berstatus <strong>⭐ SPV</strong> atau <strong>🔴 Offday</strong> otomatis dikecualikan dari plotting gate.</span>
                </p>
            </div>

            <!-- Generate Crew Action Buttons -->
            <div class="flex items-center gap-3 shrink-0">
                <form action="{{ route('superadmin.rangers.generate') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menjalankan Generate Crew? Seluruh ranger aktif (non-SPV dan non-Offday) akan di-plot secara acak sesuai kuota.');">
                    @csrf
                    <button type="submit" class="px-5 py-3 bg-orange-600 hover:bg-orange-700 text-white font-black text-xs uppercase tracking-widest rounded-xl transition shadow-md shadow-orange-600/25 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        <span>Generate Crew</span>
                    </button>
                </form>

                <form action="{{ route('superadmin.rangers.reset') }}" method="POST" onsubmit="return confirm('Reset seluruh plotting gate? Semua ranger akan berstatus Belum Di-plot.');">
                    @csrf
                    <button type="submit" class="px-4 py-3 bg-slate-100 hover:bg-rose-50 hover:text-rose-700 text-slate-800 font-black text-xs rounded-xl transition flex items-center gap-1.5 border border-slate-300">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        <span>Reset Plotting</span>
                    </button>
                </form>
            </div>
        </div>

        <!-- Quota Form Grid -->
        <form action="{{ route('superadmin.rangers.update-quotas') }}" method="POST" class="mt-6">
            @csrf
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-3.5">
                @foreach($quotas as $q)
                    <div class="bg-slate-50 p-3.5 rounded-2xl border-2 border-slate-200">
                        <input type="hidden" name="quotas[{{ $loop->index }}][id]" value="{{ $q->id }}">
                        
                        <div class="flex items-center justify-between mb-2">
                            <span class="font-black text-slate-950 text-xs truncate">{{ $q->gate_name }}</span>
                            <span class="text-[10px] font-black px-2 py-0.5 bg-slate-200 text-slate-800 rounded-md">
                                Total: {{ $q->male_quota + $q->female_quota }}
                            </span>
                        </div>

                        <div class="grid grid-cols-2 gap-2">
                            <!-- Male Quota -->
                            <div>
                                <label class="block text-[9px] font-black uppercase text-blue-900 mb-0.5">Laki-laki</label>
                                <input type="number" 
                                       name="quotas[{{ $loop->index }}][male_quota]" 
                                       value="{{ $q->male_quota }}" 
                                       min="0" 
                                       class="w-full text-xs font-black text-center py-1.5 px-1 bg-white border-2 border-blue-200 rounded-lg text-blue-950 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            <!-- Female Quota -->
                            <div>
                                <label class="block text-[9px] font-black uppercase text-pink-900 mb-0.5">Perempuan</label>
                                <input type="number" 
                                       name="quotas[{{ $loop->index }}][female_quota]" 
                                       value="{{ $q->female_quota }}" 
                                       min="0" 
                                       class="w-full text-xs font-black text-center py-1.5 px-1 bg-white border-2 border-pink-200 rounded-lg text-pink-950 focus:ring-2 focus:ring-pink-500 focus:border-pink-500">
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-4 flex justify-end">
                <button type="submit" class="px-6 py-2.5 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-black uppercase tracking-wider transition shadow-sm">
                    Simpan Perubahan Quota
                </button>
            </div>
        </form>
    </div>

    <!-- Registered Rangers List Card -->
    <div class="bg-white rounded-3xl border-2 border-slate-200 shadow-sm overflow-hidden">
        <!-- Header & Filters -->
        <div class="p-6 border-b border-slate-200 flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            <div>
                <h3 class="text-xl font-black text-slate-950 font-outfit leading-none">Daftar Ranger & Crew</h3>
                <p class="text-xs text-slate-700 font-bold mt-1">Seluruh ranger yang terdaftar beserta pengaturan peran SPV, status Offday, dan posisi gate.</p>
            </div>

            <!-- Search & Filters Form -->
            <form action="{{ route('superadmin.rangers.index') }}" method="GET" class="flex flex-wrap items-center gap-2.5">
                <!-- Search Input -->
                <div class="relative min-w-[200px]">
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}" 
                           placeholder="Cari nama, WA, bank..." 
                           class="w-full pl-9 pr-4 py-2 bg-slate-50 border-2 border-slate-300 rounded-xl text-xs font-bold text-slate-950 focus:ring-2 focus:ring-orange-500 focus:bg-white focus:outline-none placeholder:text-slate-500">
                    <svg class="w-4 h-4 text-slate-500 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>

                <!-- Role / Status Filter -->
                <select name="role_filter" onchange="this.form.submit()" class="px-3 py-2 bg-slate-50 border-2 border-slate-300 rounded-xl text-xs font-black text-slate-900 focus:ring-2 focus:ring-orange-500">
                    <option value="">Semua Status / Role</option>
                    <option value="spv" {{ request('role_filter') === 'spv' ? 'selected' : '' }}>⭐ Supervisor (SPV)</option>
                    <option value="offday" {{ request('role_filter') === 'offday' ? 'selected' : '' }}>🔴 Offday / Libur</option>
                    <option value="active" {{ request('role_filter') === 'active' ? 'selected' : '' }}>🟢 Siap Plotting (Reguler)</option>
                </select>

                <!-- Gender Filter -->
                <select name="gender" onchange="this.form.submit()" class="px-3 py-2 bg-slate-50 border-2 border-slate-300 rounded-xl text-xs font-black text-slate-900 focus:ring-2 focus:ring-orange-500">
                    <option value="">Semua Gender</option>
                    <option value="male" {{ request('gender') === 'male' ? 'selected' : '' }}>Laki-laki (Male)</option>
                    <option value="female" {{ request('gender') === 'female' ? 'selected' : '' }}>Perempuan (Female)</option>
                </select>

                <!-- Gate Filter -->
                <select name="gate" onchange="this.form.submit()" class="px-3 py-2 bg-slate-50 border-2 border-slate-300 rounded-xl text-xs font-black text-slate-900 focus:ring-2 focus:ring-orange-500">
                    <option value="">Semua Pos / Gate</option>
                    <option value="unassigned" {{ request('gate') === 'unassigned' ? 'selected' : '' }}>Belum Di-plot</option>
                    @foreach($quotas as $q)
                        <option value="{{ $q->gate_name }}" {{ request('gate') === $q->gate_name ? 'selected' : '' }}>{{ $q->gate_name }}</option>
                    @endforeach
                </select>

                @if(request()->anyFilled(['search', 'role_filter', 'gender', 'gate']))
                    <a href="{{ route('superadmin.rangers.index') }}" class="px-3 py-2 bg-slate-200 hover:bg-slate-300 text-slate-900 text-xs font-black rounded-xl transition">
                        Reset Filter
                    </a>
                @endif
            </form>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto custom-scrollbar">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-100 border-b border-slate-200 text-xs font-black uppercase text-slate-900 tracking-wider">
                        <th class="py-3.5 px-4">No</th>
                        <th class="py-3.5 px-4">Nama Ranger</th>
                        <th class="py-3.5 px-4 text-center">Status / Role</th>
                        <th class="py-3.5 px-4">WhatsApp</th>
                        <th class="py-3.5 px-4">Gender</th>
                        <th class="py-3.5 px-4">Bank / E-Wallet</th>
                        <th class="py-3.5 px-4">Posisi Gate</th>
                        <th class="py-3.5 px-4">Tanggal Daftar</th>
                        <th class="py-3.5 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs font-medium text-slate-800">
                    @forelse($rangers as $index => $ranger)
                        <tr class="hover:bg-slate-50 transition border-b border-slate-100 {{ $ranger->is_spv ? 'bg-amber-50/30' : ($ranger->is_offday ? 'bg-rose-50/20' : '') }}">
                            <td class="py-4 px-4 font-black text-slate-600">
                                {{ $rangers->firstItem() + $index }}
                            </td>

                            <!-- Nama Ranger with Star Badge for SPV -->
                            <td class="py-4 px-4">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="font-black text-slate-950 font-outfit text-sm">{{ $ranger->name }}</span>
                                    
                                    @if($ranger->is_spv)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-black bg-amber-200 text-amber-950 border border-amber-400 shadow-sm" title="Supervisor">
                                            ⭐ SPV
                                        </span>
                                    @endif

                                    @if($ranger->is_offday)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-black bg-rose-100 text-rose-900 border border-rose-300" title="Offday / Libur">
                                            🔴 Offday
                                        </span>
                                    @endif
                                </div>
                                <span class="text-[10px] text-slate-500 font-mono font-bold">ID: #{{ $ranger->id }}</span>
                            </td>

                            <!-- Toggle SPV & Offday Buttons -->
                            <td class="py-4 px-4 text-center whitespace-nowrap">
                                <div class="inline-flex items-center gap-1.5">
                                    <!-- SPV Toggle Button -->
                                    <form action="{{ route('superadmin.rangers.toggle-spv', $ranger->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" 
                                                class="px-2.5 py-1.5 rounded-xl text-[10px] font-black uppercase tracking-wider transition shadow-sm flex items-center gap-1
                                                {{ $ranger->is_spv ? 'bg-amber-400 hover:bg-amber-500 text-slate-950 border border-amber-500' : 'bg-slate-100 hover:bg-amber-100 hover:text-amber-900 text-slate-700 border border-slate-300' }}"
                                                title="{{ $ranger->is_spv ? 'Klik untuk membatalkan status SPV' : 'Klik untuk menjadikan SPV' }}">
                                            <span>⭐</span>
                                            <span>{{ $ranger->is_spv ? 'SPV Aktif' : 'Jadikan SPV' }}</span>
                                        </button>
                                    </form>

                                    <!-- Offday Toggle Button -->
                                    <form action="{{ route('superadmin.rangers.toggle-offday', $ranger->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" 
                                                class="px-2.5 py-1.5 rounded-xl text-[10px] font-black uppercase tracking-wider transition shadow-sm flex items-center gap-1
                                                {{ $ranger->is_offday ? 'bg-rose-600 hover:bg-rose-700 text-white border border-rose-700' : 'bg-slate-100 hover:bg-rose-100 hover:text-rose-900 text-slate-700 border border-slate-300' }}"
                                                title="{{ $ranger->is_offday ? 'Klik untuk mengaktifkan kembali ranger' : 'Klik untuk menandai Offday (Libur)' }}">
                                            <span>🔴</span>
                                            <span>{{ $ranger->is_offday ? 'Offday' : 'Set Offday' }}</span>
                                        </button>
                                    </form>
                                </div>
                            </td>

                            <!-- WhatsApp -->
                            <td class="py-4 px-4 whitespace-nowrap">
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $ranger->whatsapp) }}" target="_blank" class="inline-flex items-center gap-1.5 text-emerald-700 hover:underline font-bold font-mono">
                                    <svg class="w-4 h-4 fill-current text-emerald-600" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-1.149 4.204 4.301-1.127z"/></svg>
                                    <span>{{ $ranger->whatsapp }}</span>
                                </a>
                            </td>

                            <!-- Gender -->
                            <td class="py-4 px-4 whitespace-nowrap">
                                @if($ranger->gender === 'male')
                                    <span class="px-2.5 py-1 rounded-full bg-blue-100 text-blue-900 text-[10px] font-black uppercase tracking-wider inline-flex items-center gap-1 border border-blue-300">
                                        <svg class="w-3 h-3 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                        Laki-laki
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full bg-pink-100 text-pink-900 text-[10px] font-black uppercase tracking-wider inline-flex items-center gap-1 border border-pink-300">
                                        <svg class="w-3 h-3 text-pink-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                        Perempuan
                                    </span>
                                @endif
                            </td>

                            <!-- Bank Account -->
                            <td class="py-4 px-4">
                                <span class="font-black text-slate-900 block">{{ $ranger->bank_name }}</span>
                                <span class="text-xs font-mono font-bold text-slate-700">{{ $ranger->account_number }}</span>
                            </td>

                            <!-- Posisi Gate -->
                            <td class="py-4 px-4 whitespace-nowrap">
                                @if($ranger->is_spv)
                                    <span class="px-2.5 py-1 rounded-xl text-xs font-black uppercase tracking-wider inline-flex items-center gap-1 bg-amber-100 text-amber-950 border border-amber-300">
                                        ⭐ SPV (Khusus)
                                    </span>
                                @elseif($ranger->is_offday)
                                    <span class="px-2.5 py-1 rounded-xl text-xs font-black uppercase tracking-wider inline-flex items-center gap-1 bg-rose-100 text-rose-950 border border-rose-300">
                                        🔴 Offday
                                    </span>
                                @elseif($ranger->assigned_gate)
                                    <span class="px-3 py-1 rounded-xl text-xs font-black uppercase tracking-wider inline-flex items-center gap-1.5
                                        {{ $ranger->assigned_gate === 'VIP' ? 'bg-amber-100 text-amber-900 border border-amber-300' : '' }}
                                        {{ $ranger->assigned_gate === 'Redemption' ? 'bg-purple-100 text-purple-900 border border-purple-300' : '' }}
                                        {{ !in_array($ranger->assigned_gate, ['VIP', 'Redemption']) ? 'bg-orange-100 text-orange-950 border border-orange-300' : '' }}">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                                        {{ $ranger->assigned_gate }}
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-lg bg-slate-100 text-slate-600 text-[10px] font-black uppercase tracking-wider border border-slate-200">
                                        Belum Di-plot
                                    </span>
                                @endif
                            </td>

                            <!-- Tanggal Daftar -->
                            <td class="py-4 px-4 text-slate-800 text-xs font-mono font-bold whitespace-nowrap">
                                {{ $ranger->created_at ? $ranger->created_at->format('d M Y, H:i') : '-' }}
                            </td>

                            <!-- Action -->
                            <td class="py-4 px-4 text-center whitespace-nowrap">
                                <div class="flex items-center justify-center gap-2" x-data="{ openAssign: false }">
                                    <!-- Manual Gate Assignment Trigger -->
                                    @if(!$ranger->is_spv && !$ranger->is_offday)
                                        <button @click="openAssign = !openAssign" type="button" title="Ubah Posisi Gate Manual" class="p-2 text-slate-600 hover:text-orange-600 hover:bg-orange-50 rounded-lg transition border border-slate-200">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </button>
                                    @endif

                                    <!-- Delete Button -->
                                    <form action="{{ route('superadmin.rangers.destroy', $ranger->id) }}" method="POST" onsubmit="return confirm('Hapus pendaftar {{ $ranger->name }}?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" title="Hapus Data" class="p-2 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition border border-slate-200">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>

                                    <!-- Inline Assignment Popover / Form -->
                                    <div x-show="openAssign" x-cloak @click.away="openAssign = false" class="absolute right-12 z-30 bg-white p-4 rounded-2xl shadow-xl border-2 border-slate-200 text-left min-w-[200px]">
                                        <form action="{{ route('superadmin.rangers.update-assignment', $ranger->id) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <label class="block text-[10px] font-black uppercase text-slate-800 mb-2">Pilih Pos Gate Manual</label>
                                            <select name="assigned_gate" class="w-full px-3 py-2 text-xs border-2 border-slate-300 rounded-xl mb-3 focus:ring-2 focus:ring-orange-500 font-bold text-slate-900">
                                                <option value="">-- Belum Di-plot --</option>
                                                @foreach($quotas as $q)
                                                    <option value="{{ $q->gate_name }}" {{ $ranger->assigned_gate === $q->gate_name ? 'selected' : '' }}>{{ $q->gate_name }}</option>
                                                @endforeach
                                            </select>
                                            <button type="submit" class="w-full py-2 bg-orange-600 hover:bg-orange-700 text-white font-black text-xs uppercase tracking-wider rounded-lg transition shadow-sm">
                                                Simpan
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="py-12 text-center text-slate-600 text-xs font-bold uppercase tracking-wider">
                                Belum ada pendaftar Ranger Bhayangkara FC 2026/2027 yang sesuai dengan filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($rangers->hasPages())
            <div class="p-6 border-t border-slate-200">
                {{ $rangers->links() }}
            </div>
        @endif
    </div>

</x-app-layout>
