<x-app-layout>
    <x-slot name="hideNav">{{ true }}</x-slot>
    <x-slot name="title">ACCESS CONTROL - {{ $event->name }}</x-slot>

    <div class="min-h-screen w-full flex flex-col bg-[#0B0F19] text-white overflow-y-auto font-sans"
         x-data="{
             selectedGate: '{{ $currentGateId }}',
             mode: '{{ $currentMode }}',
             autoTimer: {{ $currentAutoTimer ? 'true' : 'false' }}
         }">
        
        <!-- Header Bar (matching mobile appBar) -->
        <header class="h-auto min-h-[5rem] pt-8 sm:pt-0 flex items-center justify-between px-4 sm:px-8 bg-[#0F172A] border-b border-white/10 shrink-0 sticky top-0 z-50">
            <div class="flex items-center gap-3">
                <a href="{{ route('organizer.gate.verify', $event) }}" 
                   class="flex items-center gap-2 bg-white/5 hover:bg-white/10 px-3.5 py-2 rounded-xl transition text-slate-300 border border-white/10 active:scale-95">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                    </svg>
                    <span class="text-[10px] font-black uppercase tracking-widest">KEMBALI</span>
                </a>
            </div>

            <div class="text-center">
                <h1 class="text-xs sm:text-sm font-black uppercase tracking-[0.25em] text-white font-outfit">ACCESS CONTROL</h1>
                <p class="text-[9px] font-bold text-indigo-400 tracking-widest uppercase mt-0.5">Konfigurasi Gate System</p>
            </div>

            <div class="w-16 flex justify-end">
                <div class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-ping"></div>
            </div>
        </header>

        <!-- Main Content (matching mobile GateControlScreen layout) -->
        <main class="flex-1 p-4 sm:p-8 flex flex-col items-center justify-center">
            <div class="w-full max-w-lg space-y-6">

                <!-- ── 1. Event Card (matching _buildEventCard from mobile) ── -->
                <div class="w-full p-6 sm:p-7 rounded-3xl bg-gradient-to-br from-indigo-600 via-indigo-700 to-purple-800 border border-white/15 shadow-2xl shadow-indigo-600/30 relative overflow-hidden">
                    <div class="absolute -right-8 -top-8 w-32 h-32 bg-white/10 rounded-full blur-xl pointer-events-none"></div>
                    
                    <div class="flex items-center gap-2 text-white/80 text-xs font-semibold tracking-wider uppercase mb-2">
                        <svg class="w-4 h-4 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Event Terpilih</span>
                    </div>

                    <h2 class="text-xl sm:text-2xl font-black text-white font-outfit leading-tight drop-shadow-sm truncate">
                        {{ $event->name }}
                    </h2>

                    <div class="mt-4 pt-3 border-t border-white/15 space-y-2 text-xs text-white/90">
                        <div class="flex items-center gap-2.5">
                            <svg class="w-4 h-4 text-indigo-200 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span class="truncate">{{ $event->venue }}</span>
                        </div>

                        <div class="flex items-center gap-2.5">
                            <svg class="w-4 h-4 text-indigo-200 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span>{{ \Carbon\Carbon::parse($event->event_start_date)->translatedFormat('d F Y, H:i') }} WIB</span>
                        </div>
                    </div>
                </div>

                <!-- Form Configuration -->
                <form action="{{ route('organizer.gate.setup.post', $event) }}" method="POST" class="space-y-6">
                    @csrf
                    <input type="hidden" name="gate_id" :value="selectedGate">
                    <input type="hidden" name="gate_mode" :value="mode">
                    <input type="hidden" name="gate_auto_timer" :value="autoTimer ? '1' : '0'">

                    <!-- ── 2. Pilih Gate Dropdown (matching _buildGateDropdown from mobile) ── -->
                    <div class="space-y-2.5">
                        <div class="flex items-center gap-2">
                            <div class="w-1 h-4 bg-indigo-500 rounded-full"></div>
                            <label class="text-xs sm:text-sm font-bold text-white tracking-wide">Pilih Gate</label>
                        </div>

                        <div class="relative bg-[#161B26] border-2 border-indigo-500/35 rounded-2xl p-1 shadow-lg transition hover:border-indigo-500/60">
                            <select x-model="selectedGate"
                                    class="w-full bg-transparent text-white font-bold text-sm px-4 py-3.5 outline-none focus:ring-0 border-0 cursor-pointer">
                                <option value="all" class="bg-[#1E293B] text-indigo-400 font-black py-2">
                                    ⚡ All Gate (Semua Kategori Tiket)
                                </option>
                                
                                @if($gates->isNotEmpty())
                                    <optgroup label="── Gate Terdaftar ──" class="bg-[#1E293B] text-slate-400 font-semibold">
                                        @foreach($gates as $gate)
                                            <option value="{{ $gate->id }}" class="bg-[#1E293B] text-white font-bold py-2">
                                                🚪 {{ $gate->name }} ({{ $gate->ticketCategories->count() }} Kategori Diizinkan)
                                            </option>
                                        @endforeach
                                    </optgroup>
                                @endif

                                @if($categories->isNotEmpty())
                                    <optgroup label="── Kategori Spesifik (Manual) ──" class="bg-[#1E293B] text-slate-400 font-semibold">
                                        @foreach($categories as $category)
                                            <option value="cat_{{ $category->id }}" class="bg-[#1E293B] text-white font-medium py-2">
                                                🎟️ Tiket {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </optgroup>
                                @endif
                            </select>
                        </div>

                        <!-- Helper text for active gate -->
                        <div class="px-1 text-[11px] text-slate-400 flex items-center gap-1.5">
                            <template x-if="selectedGate === 'all'">
                                <span class="text-indigo-400 flex items-center gap-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-indigo-400"></span>
                                    Mode All Gate: Memproses semua kategori tiket & wristband tanpa batasan.
                                </span>
                            </template>
                            <template x-if="selectedGate !== 'all'">
                                <span class="text-slate-300 flex items-center gap-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                                    Hanya tiket yang diizinkan untuk gate ini yang dapat masuk.
                                </span>
                            </template>
                        </div>
                    </div>

                    <!-- ── 3. Check In / Check Out Buttons (matching _buildToggleButton from mobile) ── -->
                    <div class="space-y-2.5">
                        <div class="flex items-center gap-2">
                            <div class="w-1 h-4 bg-indigo-500 rounded-full"></div>
                            <label class="text-xs sm:text-sm font-bold text-white tracking-wide">Mode Akses Gate</label>
                        </div>

                        <div class="grid grid-cols-2 gap-3.5">
                            <!-- Check In Button -->
                            <button type="button" 
                                    @click="mode = 'IN'"
                                    :class="mode === 'IN' 
                                        ? 'border-2 border-emerald-500 bg-emerald-500/15 text-white shadow-xl shadow-emerald-500/15' 
                                        : 'border border-white/10 bg-[#161B26] text-slate-400 hover:border-white/20 hover:text-white'"
                                    class="p-4 sm:p-5 rounded-2xl flex items-center justify-center gap-3 transition-all duration-200 active:scale-95 group">
                                <div :class="mode === 'IN' ? 'bg-emerald-500 text-black' : 'bg-white/5 text-slate-400 group-hover:text-white'"
                                     class="w-10 h-10 rounded-xl flex items-center justify-center transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                                    </svg>
                                </div>
                                <div class="text-left">
                                    <div class="text-sm sm:text-base font-black uppercase tracking-wider" :class="mode === 'IN' ? 'text-emerald-400' : 'text-slate-300'">Check In</div>
                                    <div class="text-[10px] text-slate-400 font-medium">Pintu Masuk</div>
                                </div>
                            </button>

                            <!-- Check Out Button -->
                            <button type="button" 
                                    @click="mode = 'OUT'"
                                    :class="mode === 'OUT' 
                                        ? 'border-2 border-orange-500 bg-orange-500/15 text-white shadow-xl shadow-orange-500/15' 
                                        : 'border border-white/10 bg-[#161B26] text-slate-400 hover:border-white/20 hover:text-white'"
                                    class="p-4 sm:p-5 rounded-2xl flex items-center justify-center gap-3 transition-all duration-200 active:scale-95 group">
                                <div :class="mode === 'OUT' ? 'bg-orange-500 text-black' : 'bg-white/5 text-slate-400 group-hover:text-white'"
                                     class="w-10 h-10 rounded-xl flex items-center justify-center transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                    </svg>
                                </div>
                                <div class="text-left">
                                    <div class="text-sm sm:text-base font-black uppercase tracking-wider" :class="mode === 'OUT' ? 'text-orange-400' : 'text-slate-300'">Check Out</div>
                                    <div class="text-[10px] text-slate-400 font-medium">Pintu Keluar</div>
                                </div>
                            </button>
                        </div>
                    </div>

                    <!-- ── 4. Pengaturan Gate (matching mobile SettingsScreen gateAutoTimer) ── -->
                    <div class="space-y-2.5 pt-1">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <div class="w-1 h-4 bg-indigo-500 rounded-full"></div>
                                <label class="text-xs sm:text-sm font-bold text-white tracking-wide">Pengaturan Gate</label>
                            </div>
                        </div>
                        <p class="text-[11px] text-slate-400 px-1">Cara menutup hasil scan sebelum kembali ke mode siap scan.</p>

                        <div class="grid grid-cols-2 gap-3.5">
                            <!-- Tombol OK (Manual Confirmation) -->
                            <button type="button"
                                    @click="autoTimer = false"
                                    :class="!autoTimer 
                                        ? 'border-2 border-indigo-500 bg-indigo-500/15 text-white shadow-xl shadow-indigo-500/15' 
                                        : 'border border-white/10 bg-[#161B26] text-slate-400 hover:border-white/20 hover:text-white'"
                                    class="p-4 rounded-2xl flex items-center gap-3 transition-all duration-200 active:scale-95 group text-left">
                                <div :class="!autoTimer ? 'bg-indigo-600 text-white' : 'bg-white/5 text-slate-400 group-hover:text-white'"
                                     class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122" />
                                    </svg>
                                </div>
                                <div class="min-w-0">
                                    <div class="text-xs sm:text-sm font-bold truncate" :class="!autoTimer ? 'text-indigo-400' : 'text-slate-200'">Tombol OK</div>
                                    <div class="text-[10px] text-slate-400 truncate">Konfirmasi manual</div>
                                </div>
                            </button>

                            <!-- Timer Otomatis (Auto Reset 3s) -->
                            <button type="button"
                                    @click="autoTimer = true"
                                    :class="autoTimer 
                                        ? 'border-2 border-indigo-500 bg-indigo-500/15 text-white shadow-xl shadow-indigo-500/15' 
                                        : 'border border-white/10 bg-[#161B26] text-slate-400 hover:border-white/20 hover:text-white'"
                                    class="p-4 rounded-2xl flex items-center gap-3 transition-all duration-200 active:scale-95 group text-left">
                                <div :class="autoTimer ? 'bg-indigo-600 text-white' : 'bg-white/5 text-slate-400 group-hover:text-white'"
                                     class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div class="min-w-0">
                                    <div class="text-xs sm:text-sm font-bold truncate" :class="autoTimer ? 'text-indigo-400' : 'text-slate-200'">Timer Otomatis</div>
                                    <div class="text-[10px] text-slate-400 truncate">Kembali 3 detik</div>
                                </div>
                            </button>
                        </div>
                    </div>

                    <!-- ── 5. Action Button (matching mobile Scan Wristband button) ── -->
                    <div class="pt-4 pb-8">
                        <button type="submit" 
                                class="w-full h-14 sm:h-16 bg-gradient-to-r from-indigo-600 via-indigo-500 to-purple-600 hover:from-indigo-500 hover:to-purple-500 text-white rounded-2xl font-black shadow-2xl shadow-indigo-600/35 transition-all transform active:scale-[0.98] flex items-center justify-center gap-3 uppercase tracking-widest text-xs sm:text-sm">
                            <svg class="w-5 h-5 sm:w-6 sm:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                            </svg>
                            <span>Scan Wristband / QR</span>
                        </button>
                    </div>
                </form>

            </div>
        </main>
    </div>
</x-app-layout>
