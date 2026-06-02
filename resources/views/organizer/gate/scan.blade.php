<x-app-layout>
    <x-slot name="hideNav">{{ true }}</x-slot>
    <x-slot name="title">SCANNER - {{ $event->name }}</x-slot>

    <div class="h-full w-full flex flex-col bg-[#010409] text-white overflow-hidden font-sans" x-data="gateScanner()">
        <!-- Header Bar -->
        <header
            class="h-auto min-h-[5.5rem] pt-10 sm:pt-0 flex items-center justify-between px-4 sm:px-6 bg-[#0d1117] border-b border-white/5 shrink-0 z-50">
            <div class="flex items-center gap-3 sm:gap-4 py-4 sm:py-0">
                <a href="{{ route('organizer.gate.setup', $event) }}"
                    class="flex items-center gap-2 bg-white/5 hover:bg-white/10 px-3 py-2 rounded-xl transition text-slate-300">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    <span class="text-[10px] font-black uppercase tracking-widest">BACK</span>
                </a>
                <div class="leading-tight">
                    <h2
                        class="text-[10px] sm:text-xs font-black uppercase tracking-[0.2em] text-white/90 truncate max-w-[120px] sm:max-w-none font-outfit">
                        {{ $event->name }}</h2>
                    <p class="text-[8px] sm:text-[10px] font-bold text-orange-500 uppercase tracking-widest mt-0.5">
                        {{ session('gate_name') }}</p>
                </div>
            </div>

            <div class="flex items-center gap-2 sm:gap-4">
                <!-- Mode Switcher -->
                <div class="flex bg-black p-1 rounded-xl border border-white/5">
                    <button @click="setMode('IN')"
                        :class="mode === 'IN' ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-600/20' : 'text-slate-500 hover:text-slate-300'"
                        class="px-3 sm:px-4 py-1 sm:py-1.5 rounded-lg text-[8px] sm:text-[10px] font-black transition-all uppercase tracking-widest">IN</button>
                    <button @click="setMode('OUT')"
                        :class="mode === 'OUT' ? 'bg-orange-600 text-white shadow-lg shadow-orange-600/20' : 'text-slate-500 hover:text-slate-300'"
                        class="px-3 sm:px-4 py-1 sm:py-1.5 rounded-lg text-[8px] sm:text-[10px] font-black transition-all uppercase tracking-widest">OUT</button>
                </div>

                <!-- Input Type Switcher -->
                <div class="flex bg-black p-1 rounded-xl border border-white/5">
                    <button @click="setInputType('auto')"
                        :class="inputType === 'auto' ? 'bg-indigo-600 text-white' : 'text-slate-500'"
                        class="p-1.5 sm:p-2 rounded-lg transition-all" title="Auto Scan">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                        </svg>
                    </button>
                    <button @click="setInputType('camera')"
                        :class="inputType === 'camera' ? 'bg-indigo-600 text-white' : 'text-slate-500'"
                        class="p-1.5 sm:p-2 rounded-lg transition-all" title="Camera Scan">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </button>
                    <button @click="setInputType('manual')"
                        :class="inputType === 'manual' ? 'bg-indigo-600 text-white' : 'text-slate-500'"
                        class="p-1.5 sm:p-2 rounded-lg transition-all" title="Manual Input">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                    </button>
                </div>

                <!-- Logout Button -->
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="w-10 h-10 flex items-center justify-center bg-rose-500/10 hover:bg-rose-500/20 text-rose-500 rounded-xl transition-all border border-rose-500/20" title="Logout">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                    </button>
                </form>
            </div>
        </header>

        <!-- Content Body -->
        <main class="flex-1 flex flex-col lg:flex-row overflow-hidden relative">
            <!-- Scan Area -->
            <div class="flex-1 flex flex-col min-h-0 relative bg-black overflow-hidden">

                <!-- Main Status & Scanner Interface -->
                <div class="absolute inset-0 flex flex-col items-center justify-center transition-all duration-300">
                    
                    <!-- Background Flash -->
                    <div class="absolute inset-0 opacity-20 transition-all duration-500"
                         :class="{
                            'bg-emerald-500': status === 'success',
                            'bg-rose-500': status === 'error',
                            'bg-slate-900': status === 'idle'
                         }">
                    </div>

                    <!-- IR Hidden Input (Better compatibility for mobile) -->
                    <input type="text" x-ref="ticketInput" @keydown.enter="processScan()"
                        class="fixed opacity-0 pointer-events-none -left-[9999px]" 
                        x-model="scannedCode" autocomplete="off"
                        x-show="inputType === 'auto'">

                    <!-- Camera Container -->
                    <div x-show="inputType === 'camera'" id="reader"
                        class="absolute inset-0 w-full h-full object-cover bg-black"></div>

                    <!-- Manual Entry Card -->
                    <div x-show="inputType === 'manual'"
                        class="z-20 w-full max-w-sm p-6 animate-in zoom-in duration-300">
                        <div
                            class="bg-slate-900/90 backdrop-blur-xl border border-white/10 p-8 rounded-[2.5rem] shadow-2xl">
                            <h3
                                class="text-[10px] font-black text-center uppercase tracking-[0.4em] text-indigo-400 mb-8">
                                Manual Entry</h3>
                            <input type="text" x-model="manualCode" placeholder="KODE TIKET"
                                @keydown.enter="processManualScan()"
                                class="w-full bg-white/5 border-2 border-white/10 rounded-2xl px-6 py-5 text-2xl font-black text-center tracking-[0.2em] text-white focus:border-indigo-500 focus:ring-0 mb-6 uppercase placeholder:text-white/10">
                            <button @click="processManualScan()"
                                class="w-full py-5 bg-indigo-600 rounded-2xl font-black uppercase tracking-widest text-sm hover:bg-indigo-700 transition shadow-lg shadow-indigo-600/20 active:scale-[0.98]">Validasi
                                Tiket</button>
                        </div>
                    </div>

                    <!-- Feedback Overlays -->
                    <div class="absolute inset-0 flex items-center justify-center pointer-events-none z-30">
                        <!-- Idle Overlay (Auto Scan Mode) -->
                        <div x-show="status === 'idle' && inputType === 'auto'"
                            class="flex flex-col items-center justify-center w-full h-full">
                            <div class="relative group">
                                <div class="absolute inset-0 rounded-full bg-indigo-500/20 animate-ping duration-1000"></div>
                                <div class="absolute -inset-8 rounded-full bg-indigo-500/5 animate-pulse duration-700"></div>
                                <div class="w-56 h-56 sm:w-80 sm:h-80 border-2 border-white/10 rounded-[3rem] relative bg-white/5 backdrop-blur-sm shadow-2xl shadow-indigo-500/10">
                                    <div class="absolute top-0 left-0 w-16 h-16 border-t-4 border-l-4 border-indigo-500 rounded-tl-[2.5rem]"></div>
                                    <div class="absolute top-0 right-0 w-16 h-16 border-t-4 border-r-4 border-indigo-500 rounded-tr-[2.5rem]"></div>
                                    <div class="absolute bottom-0 left-0 w-16 h-16 border-b-4 border-l-4 border-indigo-500 rounded-bl-[2.5rem]"></div>
                                    <div class="absolute bottom-0 right-0 w-16 h-16 border-b-4 border-r-4 border-indigo-500 rounded-br-[2.5rem]"></div>
                                    <div class="absolute top-1/2 left-6 right-6 h-[2px] bg-red-500 shadow-[0_0_15px_rgba(239,68,68,0.8)] blur-[0.5px] animate-scan-line"></div>
                                    <div class="absolute inset-0 flex items-center justify-center opacity-20">
                                        <svg class="w-24 h-24 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" /></svg>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-12 text-center">
                                <p class="text-xs sm:text-sm font-black uppercase tracking-[0.6em] text-white/60 animate-pulse">Ready to Scan</p>
                                <p class="mt-2 text-[8px] font-bold text-slate-500 uppercase tracking-widest opacity-50">Connect Infrared Scanner or Type Code</p>
                            </div>
                        </div>

                        <!-- Idle Overlay (Camera Mode) -->
                        <div x-show="status === 'idle' && inputType === 'camera'"
                            class="flex flex-col items-center justify-center w-full h-full pointer-events-none">
                            <div class="w-64 h-64 sm:w-80 sm:h-80 border-2 border-indigo-500/50 rounded-[3rem] relative">
                                <div class="absolute inset-0 bg-indigo-500/10 rounded-[3rem] animate-pulse"></div>
                                <div class="absolute top-1/2 left-6 right-6 h-1 bg-red-500/50 blur-[2px] animate-scan-line"></div>
                            </div>
                            <p class="mt-10 text-[10px] font-black uppercase tracking-[0.4em] text-white/40">Camera Active</p>
                        </div>

                        <!-- Success Overlay -->
                        <div x-show="status === 'success'"
                            class="bg-[#065f46] fixed inset-0 flex flex-col items-center justify-center p-6 animate-in fade-in duration-200 z-50">
                            <div class="w-32 h-32 bg-white rounded-full flex items-center justify-center text-emerald-600 mb-8 shadow-2xl animate-bounce">
                                <svg class="w-20 h-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M5 13l4 4L19 7" /></svg>
                            </div>
                            <h3 class="text-4xl sm:text-6xl font-black text-white uppercase tracking-tight text-center mb-4 font-outfit" x-text="result.customer"></h3>
                            <div class="px-8 py-2 bg-white text-emerald-900 rounded-full font-black text-sm uppercase tracking-[0.2em]" x-text="result.category"></div>
                            <p class="mt-12 text-2xl sm:text-3xl font-black uppercase tracking-[0.5em] text-white/50">BERHASIL</p>
                        </div>

                        <!-- Error Overlay -->
                        <div x-show="status === 'error'"
                            class="bg-[#9f1239] fixed inset-0 flex flex-col items-center justify-center p-6 animate-in shake duration-300 z-50">
                            <div class="w-32 h-32 bg-white rounded-full flex items-center justify-center text-rose-600 mb-8 shadow-2xl">
                                <svg class="w-20 h-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M6 18L18 6M6 6l12 12" /></svg>
                            </div>
                            <h3 class="text-3xl sm:text-5xl font-black text-white uppercase tracking-tight text-center mb-8 font-outfit">AKSES DITOLAK</h3>
                            <div class="bg-black/40 px-8 py-6 rounded-3xl border border-white/20 text-xl sm:text-2xl font-black text-center max-w-lg leading-relaxed" x-text="errorMessage"></div>
                            <p class="mt-12 text-white/40 font-bold uppercase tracking-[0.3em] text-sm" x-text="'CODE: ' + lastScannedCode"></p>
                        </div>

                        <!-- Processing Overlay -->
                        <div x-show="status === 'processing'"
                            class="bg-black/90 fixed inset-0 flex flex-col items-center justify-center backdrop-blur-xl z-50">
                            <div class="w-20 h-20 border-4 border-white/5 border-t-indigo-500 rounded-full animate-spin mb-6"></div>
                            <p class="text-[10px] font-black uppercase tracking-[0.4em] text-indigo-400">Validating...</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Group Check-in Modal Overlay -->
            <div x-show="isGroupScan" x-cloak 
                 class="fixed inset-0 bg-black/85 backdrop-blur-md flex items-center justify-center p-4 sm:p-6 z-[60] overflow-y-auto">
                <div x-show="isGroupScan" 
                     @click.away="closeGroupModal()"
                     class="bg-[#0d1117] border border-white/10 w-full max-w-lg rounded-[2rem] shadow-2xl flex flex-col overflow-hidden my-8 transform transition-all animate-in zoom-in duration-300">
                    
                    <!-- Modal Header -->
                    <div class="px-6 py-5 bg-[#161b22] border-b border-white/5 flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-black font-outfit text-white">Daftar Anggota Transaksi</h3>
                            <p class="text-[10px] font-bold text-indigo-400 uppercase tracking-widest mt-0.5" x-text="'Invoice: ' + (groupData ? groupData.customer : '')"></p>
                        </div>
                        <button @click="closeGroupModal()" class="text-slate-400 hover:text-white p-1 hover:bg-white/5 rounded-lg transition">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <!-- Modal Body (Attendees List) -->
                    <div class="p-6 max-h-[60vh] overflow-y-auto space-y-3 custom-scrollbar">
                        <div class="flex justify-between items-center text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">
                            <span>Daftar Peserta</span>
                            <button type="button" @click="toggleSelectAllGroup()" class="text-indigo-400 hover:text-indigo-300 text-[10px]">
                                <span x-text="selectedTicketIds.length === groupData?.attendees.length ? 'Batal Pilih Semua' : 'Pilih Semua'"></span>
                            </button>
                        </div>

                        <template x-for="attendee in (groupData ? groupData.attendees : [])" :key="attendee.ticket_id">
                            <label :class="selectedTicketIds.includes(attendee.ticket_id) ? 'border-indigo-500/50 bg-indigo-500/5' : 'border-white/5 bg-[#161b22]/50 hover:bg-[#161b22]'"
                                   class="flex items-center justify-between p-4 border rounded-xl cursor-pointer transition">
                                <div class="flex items-center gap-3">
                                    <input type="checkbox" :value="attendee.ticket_id" x-model="selectedTicketIds" 
                                           class="rounded border-white/10 bg-white/5 text-indigo-600 focus:ring-0 focus:ring-offset-0 w-4 h-4">
                                    <div>
                                        <div class="text-sm font-black text-white" x-text="attendee.name"></div>
                                        <div class="flex items-center gap-1.5 mt-0.5">
                                            <span class="text-[9px] font-bold text-slate-500 uppercase tracking-wider" x-text="attendee.ticket_code"></span>
                                            <span class="text-[8px] bg-white/5 text-slate-400 px-1.5 py-0.5 rounded uppercase" x-text="attendee.gender || 'n/a'"></span>
                                        </div>
                                        <template x-if="attendee.is_checked_in">
                                            <div class="text-[10px] text-emerald-400 font-bold mt-1" x-text="'In: ' + (attendee.checked_in_at || '-') + ' oleh ' + (attendee.checked_in_by || '-')"></div>
                                        </template>
                                    </div>
                                </div>
                                <div class="flex items-center">
                                    <template x-if="attendee.is_checked_in">
                                        <span class="px-2.5 py-1 bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 text-[8px] font-black uppercase rounded-lg tracking-widest">DI DALAM</span>
                                    </template>
                                    <template x-if="!attendee.is_checked_in">
                                        <span class="px-2.5 py-1 bg-slate-500/10 text-slate-400 border border-white/5 text-[8px] font-black uppercase rounded-lg tracking-widest">DI LUAR</span>
                                    </template>
                                </div>
                            </label>
                        </template>
                    </div>

                    <!-- Modal Footer -->
                    <div class="px-6 py-4 bg-[#161b22] border-t border-white/5 flex gap-3">
                        <button @click="closeGroupModal()" 
                                class="flex-1 py-3 border border-white/10 rounded-xl text-xs font-black uppercase tracking-widest hover:bg-white/5 transition">
                            Batal
                        </button>
                        <button @click="submitBulkCheckin()" 
                                :disabled="selectedTicketIds.length === 0"
                                :class="mode === 'IN' ? 'bg-emerald-600 hover:bg-emerald-700 shadow-emerald-600/20' : 'bg-orange-600 hover:bg-orange-700 shadow-orange-600/20'"
                                class="flex-1 py-3 text-white disabled:opacity-40 disabled:pointer-events-none rounded-xl text-xs font-black uppercase tracking-widest transition shadow-lg">
                            Konfirmasi <span x-text="mode === 'IN' ? 'Check-in' : 'Check-out'"></span> (<span x-text="selectedTicketIds.length"></span>)
                        </button>
                    </div>
                </div>
            </div>

            <!-- History Sidebar -->
            <aside class="hidden lg:flex w-96 bg-[#010409] border-l border-white/5 flex-col shrink-0 h-full overflow-hidden z-40">
                <div class="h-16 px-6 bg-[#0d1117] border-b border-white/5 flex items-center justify-between shrink-0">
                    <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em]">Activity Log</h4>
                    <span class="bg-indigo-500/10 text-indigo-400 px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-wider" x-text="history.length + ' Entri'"></span>
                </div>
                <div class="flex-1 overflow-y-auto p-4 space-y-3 custom-scrollbar">
                    <template x-for="(log, index) in history" :key="log.time + index">
                        <div class="flex items-center justify-between p-4 rounded-2xl border transition-all animate-in slide-in-from-right-4 duration-300"
                             :class="log.success ? 'bg-white/5 border-white/5 hover:border-indigo-500/30' : 'bg-rose-500/5 border-rose-500/20'">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-xl flex items-center justify-center text-[10px] font-black shadow-inner"
                                     :class="log.type === 'IN' ? 'bg-emerald-500/10 text-emerald-400' : 'bg-orange-500/10 text-orange-400'">
                                    <span x-text="log.type"></span>
                                </div>
                                <div class="min-w-0">
                                    <div class="text-xs font-black text-white/90 truncate font-outfit" x-text="log.name || 'Access Denied'"></div>
                                    <div class="text-[9px] font-bold text-slate-500 uppercase tracking-widest mt-0.5 truncate" x-text="log.category || log.code"></div>
                                </div>
                            </div>
                            <div class="text-[9px] font-black text-slate-600 whitespace-nowrap bg-black/40 px-2 py-1 rounded-lg" x-text="log.time"></div>
                        </div>
                    </template>
                    <template x-if="history.length === 0">
                        <div class="flex flex-col items-center justify-center py-20 opacity-10">
                            <svg class="w-16 h-16 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            <p class="text-[10px] font-black uppercase tracking-[0.4em]">Waiting for data</p>
                        </div>
                    </template>
                </div>
            </aside>
        </main>

        <!-- Bottom Stats Bar (Global) -->
        <div class="bg-black border-t border-white/5 p-4 sm:p-6 grid grid-cols-3 gap-4 sm:gap-8 shrink-0 z-50">
            <div class="text-center group">
                <div class="text-xl sm:text-3xl font-black font-outfit text-emerald-500 leading-none mb-1 sm:mb-2" x-text="inCount">0</div>
                <div class="text-[8px] sm:text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] group-hover:text-emerald-400 transition-colors">Total In</div>
            </div>
            <div class="text-center group border-x border-white/5">
                <div class="text-xl sm:text-3xl font-black font-outfit text-orange-500 leading-none mb-1 sm:mb-2" x-text="outCount">0</div>
                <div class="text-[8px] sm:text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] group-hover:text-orange-400 transition-colors">Total Out</div>
            </div>
            <div class="text-center group">
                <div class="text-xl sm:text-3xl font-black font-outfit text-white leading-none mb-1 sm:mb-2" x-text="inCount - outCount">0</div>
                <div class="text-[8px] sm:text-[9px] font-black text-indigo-400 uppercase tracking-[0.2em] group-hover:text-indigo-300 transition-colors">Inside</div>
            </div>
        </div>
    </div>

    <!-- Audio Effects -->
    <audio id="sound-success" src="{{ asset('sounds/success.mp3') }}"></audio>
    <audio id="sound-error" src="{{ asset('sounds/invalid.mp3') }}"></audio>

    <script src="https://unpkg.com/html5-qrcode"></script>
    <script>
        function gateScanner() {
            return {
                mode: '{{ session('gate_mode', 'IN') }}',
                inputType: 'auto',
                status: 'idle',
                scannedCode: '',
                manualCode: '',
                lastScannedCode: '',
                errorMessage: '',
                result: { customer: '', category: '' },
                inCount: {{ $inCount }},
                outCount: {{ $outCount }},
                history: [],
                html5QrCode: null,

                // Group Scan State
                isGroupScan: false,
                groupData: null,
                selectedTicketIds: [],

                init() {
                    this.$nextTick(() => this.focusInput());
                    document.addEventListener('click', () => { if (this.inputType === 'auto' && !this.isGroupScan) this.focusInput(); });
                    setInterval(() => { if (this.inputType === 'auto' && this.status === 'idle' && !this.isGroupScan) this.focusInput(); }, 1000);
                },

                focusInput() { if (this.$refs.ticketInput) this.$refs.ticketInput.focus(); },

                setMode(val) { this.mode = val; this.status = 'idle'; },

                setInputType(val) {
                    if (this.inputType === 'camera') this.stopCamera();
                    this.inputType = val;
                    if (val === 'camera') {
                        setTimeout(() => this.startCamera(), 300); // Give time for x-show to render
                    }
                    if (val === 'auto') this.$nextTick(() => this.focusInput());
                },

                startCamera() {
                    if (this.html5QrCode) this.stopCamera();
                    
                    this.html5QrCode = new Html5Qrcode("reader");
                    const config = { fps: 20, qrbox: { width: 250, height: 250 } };
                    
                    this.html5QrCode.start(
                        { facingMode: "environment" }, 
                        config, 
                        (text) => { 
                            if (this.status !== 'processing' && !this.isGroupScan) {
                                this.scannedCode = text; 
                                this.processScan(); 
                            }
                        }
                    ).catch(err => {
                        console.error("Camera Error:", err);
                    });
                },

                stopCamera() { 
                    if (this.html5QrCode && this.html5QrCode.isScanning) {
                        this.html5QrCode.stop().then(() => {
                            this.html5QrCode.clear();
                        }).catch(err => console.error("Stop Error:", err));
                    }
                },

                processManualScan() { if (!this.manualCode) return; this.scannedCode = this.manualCode; this.manualCode = ''; this.processScan(); },

                processScan() {
                    if (!this.scannedCode || this.status === 'processing') return;
                    const code = this.scannedCode.trim();
                    this.scannedCode = '';
                    this.lastScannedCode = code;
                    this.status = 'processing';

                    fetch('{{ route('organizer.gate.process') }}', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: JSON.stringify({ 
                            ticket_code: code, 
                            event_id: {{ $event->id }}, 
                            mode: this.mode, 
                            gate_name: '{{ session('gate_name') }}' 
                        })
                    })
                    .then(r => r.ok ? r.json() : r.json().then(e => { throw e }))
                    .then(d => this.handleSuccess(d))
                    .catch(e => this.handleError(e.message || 'Gagal'))
                    .finally(() => { if (this.inputType === 'auto' && !this.isGroupScan) this.focusInput(); });
                },

                handleSuccess(d) {
                    if (d.is_group) {
                        this.status = 'idle';
                        this.groupData = d;
                        this.isGroupScan = true;
                        
                        // Default check only unchecked tickets when checking-in, or checked tickets when checking-out
                        this.selectedTicketIds = d.attendees
                            .filter(a => {
                                if (this.mode === 'IN') {
                                    // Highlight currently scanned ticket as selected by default
                                    return !a.is_checked_in || a.ticket_id === d.scanned_ticket_id;
                                } else {
                                    return a.is_checked_in || a.ticket_id === d.scanned_ticket_id;
                                }
                            })
                            .map(a => a.ticket_id);
                        
                        document.getElementById('sound-success').play();
                        return;
                    }

                    this.status = 'success';
                    this.result = { customer: d.customer, category: d.category };
                    this.inCount = d.in_count; this.outCount = d.out_count;
                    this.history.unshift({ success: true, type: this.mode, name: d.customer, category: d.category, time: new Date().toLocaleTimeString() });
                    document.getElementById('sound-success').play();
                    setTimeout(() => { if (this.status === 'success') this.status = 'idle'; }, 2000);
                },

                handleError(msg) {
                    this.status = 'error';
                    this.errorMessage = msg;
                    this.history.unshift({ success: false, type: this.mode, code: this.lastScannedCode, time: new Date().toLocaleTimeString() });
                    document.getElementById('sound-error').play();
                    setTimeout(() => { if (this.status === 'error') this.status = 'idle'; }, 3000);
                },

                closeGroupModal() {
                    this.isGroupScan = false;
                    this.groupData = null;
                    this.selectedTicketIds = [];
                    if (this.inputType === 'auto') this.$nextTick(() => this.focusInput());
                },

                toggleSelectAllGroup() {
                    if (!this.groupData) return;
                    if (this.selectedTicketIds.length === this.groupData.attendees.length) {
                        this.selectedTicketIds = [];
                    } else {
                        this.selectedTicketIds = this.groupData.attendees.map(a => a.ticket_id);
                    }
                },

                submitBulkCheckin() {
                    if (this.selectedTicketIds.length === 0) return;
                    this.status = 'processing';
                    
                    fetch('{{ route('organizer.gate.bulk-checkin', $event) }}', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: JSON.stringify({
                            ticket_ids: this.selectedTicketIds,
                            mode: this.mode,
                            gate_name: '{{ session('gate_name') }}'
                        })
                    })
                    .then(r => r.ok ? r.json() : r.json().then(e => { throw e }))
                    .then(d => {
                        this.inCount = d.in_count;
                        this.outCount = d.out_count;
                        
                        // Add elements to log history
                        this.selectedTicketIds.forEach(id => {
                            const att = this.groupData.attendees.find(a => a.ticket_id === id);
                            if (att) {
                                this.history.unshift({
                                    success: true,
                                    type: this.mode,
                                    name: att.name,
                                    category: att.category,
                                    time: new Date().toLocaleTimeString()
                                });
                            }
                        });

                        // Show quick temporary success state
                        this.isGroupScan = false;
                        this.groupData = null;
                        this.selectedTicketIds = [];
                        
                        this.status = 'success';
                        this.result = { customer: 'Check-in Grup', category: 'Berhasil memproses ' + this.mode };
                        setTimeout(() => { this.status = 'idle'; }, 1500);
                    })
                    .catch(e => {
                        this.isGroupScan = false;
                        this.handleError(e.message || 'Gagal memproses bulk check-in');
                    })
                    .finally(() => { if (this.inputType === 'auto') this.focusInput(); });
                }
            }
        }
    </script>

    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 3px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(255, 255, 255, 0.05); border-radius: 10px; }
        .animate-scan-line { animation: scan-line 2s ease-in-out infinite; }
        @keyframes scan-line { 0%, 100% { top: 5%; } 50% { top: 95%; } }
        .shake { animation: shake 0.5s cubic-bezier(.36, .07, .19, .97) both; }
        @keyframes shake { 10%, 90% { transform: translate3d(-1px, 0, 0); } 20%, 80% { transform: translate3d(2px, 0, 0); } 30%, 50%, 70% { transform: translate3d(-4px, 0, 0); } 40%, 60% { transform: translate3d(4px, 0, 0); } }
    </style>
</x-app-layout>