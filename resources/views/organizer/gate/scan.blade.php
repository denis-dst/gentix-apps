<x-app-layout>
    <x-slot name="hideNav">{{ true }}</x-slot>
    <x-slot name="title">SCANNER - {{ $event->name }}</x-slot>
    
    <div class="h-full w-full flex flex-col bg-[#0f172a] text-white overflow-hidden" x-data="gateScanner()">
        <!-- Header Bar -->
        <header class="flex items-center justify-between px-4 py-2 bg-[#1e293b] border-b border-slate-800 shrink-0 z-50">
            <div class="flex items-center gap-3">
                <a href="{{ route('organizer.gate.setup', $event) }}" class="p-2 hover:bg-slate-700 rounded-lg transition text-slate-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                </a>
                <div class="leading-tight">
                    <h2 class="text-xs font-black uppercase tracking-wider truncate max-w-[150px] sm:max-w-none">{{ $event->name }}</h2>
                    <p class="text-[10px] font-bold text-orange-500 uppercase tracking-widest">{{ session('gate_name') }}</p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <!-- Mode Switcher (Pill style) -->
                <div class="flex bg-slate-900 p-1 rounded-full border border-slate-800">
                    <button @click="setMode('IN')" :class="mode === 'IN' ? 'bg-emerald-600 text-white' : 'text-slate-500'" class="px-3 py-1 rounded-full text-[9px] font-black transition-all">IN</button>
                    <button @click="setMode('OUT')" :class="mode === 'OUT' ? 'bg-orange-600 text-white' : 'text-slate-500'" class="px-3 py-1 rounded-full text-[9px] font-black transition-all">OUT</button>
                </div>

                <!-- Input Type Switcher -->
                <div class="flex bg-slate-900 p-1 rounded-lg border border-slate-800">
                    <button @click="setInputType('auto')" :class="inputType === 'auto' ? 'bg-indigo-600 text-white' : 'text-slate-500'" class="p-1 rounded transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" /></svg>
                    </button>
                    <button @click="setInputType('camera')" :class="inputType === 'camera' ? 'bg-indigo-600 text-white' : 'text-slate-500'" class="p-1 rounded transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                    </button>
                    <button @click="setInputType('manual')" :class="inputType === 'manual' ? 'bg-indigo-600 text-white' : 'text-slate-500'" class="p-1 rounded transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                    </button>
                </div>
            </div>
        </header>

        <!-- Content Body -->
        <main class="flex-1 flex flex-col lg:flex-row overflow-hidden">
            <!-- Scan Area -->
            <div class="flex-1 flex flex-col min-h-0 relative bg-black">
                
                <!-- Main Status & Scanner Interface -->
                <div class="absolute inset-0 flex flex-col items-center justify-center transition-all duration-300"
                     :class="statusClasses">
                    
                    <!-- Background Flash -->
                    <div class="absolute inset-0 opacity-20 transition-all duration-500" :class="bgAnimationClasses"></div>

                    <!-- IR Hidden Input -->
                    <input type="text" x-ref="ticketInput" @keydown.enter="processScan()" 
                           class="absolute opacity-0 pointer-events-none" 
                           x-model="scannedCode" autocomplete="off"
                           x-show="inputType === 'auto'">

                    <!-- Camera Container -->
                    <div x-show="inputType === 'camera'" id="reader" class="absolute inset-0 w-full h-full"></div>

                    <!-- Manual Entry Card -->
                    <div x-show="inputType === 'manual'" class="z-20 w-full max-w-sm p-6 animate-in zoom-in duration-300">
                        <div class="bg-slate-900/90 backdrop-blur-xl border border-slate-700 p-8 rounded-[2rem] shadow-2xl">
                            <h3 class="text-xs font-black text-center uppercase tracking-[0.3em] text-indigo-400 mb-6">Input Manual</h3>
                            <input type="text" x-model="manualCode" placeholder="KODE TIKET" 
                                   @keydown.enter="processManualScan()"
                                   class="w-full bg-slate-800 border-2 border-slate-700 rounded-xl px-6 py-4 text-xl font-black text-center tracking-widest text-white focus:border-indigo-500 focus:ring-0 mb-4 uppercase">
                            <button @click="processManualScan()" class="w-full py-4 bg-indigo-600 rounded-xl font-black uppercase tracking-widest text-sm hover:bg-indigo-700 transition">Proses</button>
                        </div>
                    </div>

                    <!-- Feedback Overlays -->
                    <div class="absolute inset-0 flex items-center justify-center pointer-events-none z-30">
                        <!-- Idle Overlay (Scanner Target) -->
                        <div x-show="status === 'idle' && inputType !== 'manual'" class="flex flex-col items-center opacity-30">
                            <div class="w-48 h-48 border-2 border-white/20 rounded-3xl relative">
                                <div class="absolute top-0 left-0 w-8 h-8 border-t-4 border-l-4 border-white rounded-tl-xl"></div>
                                <div class="absolute top-0 right-0 w-8 h-8 border-t-4 border-r-4 border-white rounded-tr-xl"></div>
                                <div class="absolute bottom-0 left-0 w-8 h-8 border-b-4 border-l-4 border-white rounded-bl-xl"></div>
                                <div class="absolute bottom-0 right-0 w-8 h-8 border-b-4 border-r-4 border-white rounded-br-xl"></div>
                                <div class="absolute top-1/2 left-0 w-full h-0.5 bg-red-500/50 animate-scan-line"></div>
                            </div>
                            <p class="mt-8 text-xs font-black uppercase tracking-[0.4em]">Ready to Scan</p>
                        </div>

                        <!-- Success Overlay -->
                        <div x-show="status === 'success'" class="bg-emerald-600 fixed inset-0 flex flex-col items-center justify-center p-6 animate-in fade-in duration-200">
                            <div class="w-24 h-24 bg-white rounded-full flex items-center justify-center text-emerald-600 mb-6 shadow-2xl">
                                <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M5 13l4 4L19 7" /></svg>
                            </div>
                            <h3 class="text-4xl font-black text-white uppercase tracking-tight text-center mb-2" x-text="result.customer"></h3>
                            <div class="px-6 py-1.5 bg-white text-emerald-700 rounded-full font-black text-sm uppercase tracking-widest" x-text="result.category"></div>
                            <p class="mt-8 text-2xl font-black uppercase tracking-[0.4em] text-emerald-100">BERHASIL</p>
                        </div>

                        <!-- Error Overlay -->
                        <div x-show="status === 'error'" class="bg-rose-600 fixed inset-0 flex flex-col items-center justify-center p-6 animate-in shake duration-300">
                            <div class="w-24 h-24 bg-white rounded-full flex items-center justify-center text-rose-600 mb-6 shadow-2xl">
                                <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M6 18L18 6M6 6l12 12" /></svg>
                            </div>
                            <h3 class="text-3xl font-black text-white uppercase tracking-tight text-center mb-6">AKSES DITOLAK</h3>
                            <div class="bg-white/10 px-8 py-4 rounded-2xl border border-white/20 text-xl font-black text-center" x-text="errorMessage"></div>
                            <p class="mt-8 text-white/40 font-bold uppercase tracking-widest text-sm" x-text="'CODE: ' + lastScannedCode"></p>
                        </div>

                        <!-- Processing Overlay -->
                        <div x-show="status === 'processing'" class="bg-slate-900/80 fixed inset-0 flex flex-col items-center justify-center backdrop-blur-md">
                            <div class="w-16 h-16 border-4 border-white/10 border-t-indigo-500 rounded-full animate-spin mb-4"></div>
                            <p class="text-xs font-black uppercase tracking-[0.3em]">Validasi...</p>
                        </div>
                    </div>
                </div>

                <!-- Bottom Stats Bar (Mobile Friendly) -->
                <div class="bg-[#1e293b] border-t border-slate-800 p-4 grid grid-cols-3 gap-4 shrink-0">
                    <div class="text-center">
                        <div class="text-lg font-black font-outfit text-emerald-500 leading-none" x-text="inCount">0</div>
                        <div class="text-[8px] font-bold text-slate-500 uppercase tracking-widest mt-1">TOTAL IN</div>
                    </div>
                    <div class="text-center">
                        <div class="text-lg font-black font-outfit text-orange-500 leading-none" x-text="outCount">0</div>
                        <div class="text-[8px] font-bold text-slate-500 uppercase tracking-widest mt-1">TOTAL OUT</div>
                    </div>
                    <div class="text-center">
                        <div class="text-lg font-black font-outfit text-white leading-none" x-text="inCount - outCount">0</div>
                        <div class="text-[8px] font-bold text-indigo-400 uppercase tracking-widest mt-1">INSIDE</div>
                    </div>
                </div>
            </div>

            <!-- History Sidebar (Desktop: Right, Mobile: Hidden or Bottom Drawer) -->
            <aside class="w-full lg:w-80 bg-[#131b2e] border-l border-slate-800 flex flex-col shrink-0 lg:h-full overflow-hidden">
                <div class="p-3 bg-slate-900/50 border-b border-slate-800 flex items-center justify-between">
                    <h4 class="text-[9px] font-black text-slate-500 uppercase tracking-[0.2em]">Log Aktivitas</h4>
                    <span class="text-[9px] text-slate-600 font-bold" x-text="history.length + ' Entri'"></span>
                </div>
                <div class="flex-1 overflow-y-auto p-2 space-y-2 custom-scrollbar">
                    <template x-for="log in history" :key="log.time">
                        <div class="flex items-center justify-between p-3 rounded-xl border transition-all"
                             :class="log.success ? 'bg-slate-900/40 border-slate-800/50' : 'bg-rose-500/5 border-rose-500/10'">
                            <div class="flex items-center gap-3">
                                <div class="w-6 h-6 rounded flex items-center justify-center text-[8px] font-black"
                                     :class="log.type === 'IN' ? 'bg-emerald-500/20 text-emerald-400' : 'bg-orange-500/20 text-orange-400'">
                                    <span x-text="log.type"></span>
                                </div>
                                <div class="min-w-0">
                                    <div class="text-[10px] font-black text-white truncate" x-text="log.name || 'Gagal'"></div>
                                    <div class="text-[8px] font-bold text-slate-600 uppercase" x-text="log.category || log.code"></div>
                                </div>
                            </div>
                            <div class="text-[8px] font-bold text-slate-700 whitespace-nowrap" x-text="log.time"></div>
                        </div>
                    </template>
                    <template x-if="history.length === 0">
                        <div class="text-center py-10 text-[10px] text-slate-700 italic">Belum ada scan...</div>
                    </template>
                </div>
            </aside>
        </main>
    </div>

    <!-- Audio Effects -->
    <audio id="sound-success" src="https://assets.mixkit.co/active_storage/sfx/2568/2568-preview.mp3"></audio>
    <audio id="sound-error" src="https://assets.mixkit.co/active_storage/sfx/2571/2571-preview.mp3"></audio>

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

                init() {
                    this.$nextTick(() => this.focusInput());
                    document.addEventListener('click', () => { if(this.inputType === 'auto') this.focusInput(); });
                    setInterval(() => { if(this.inputType === 'auto' && this.status === 'idle') this.focusInput(); }, 1000);
                },

                focusInput() { if (this.$refs.ticketInput) this.$refs.ticketInput.focus(); },

                setMode(val) { this.mode = val; this.status = 'idle'; },

                setInputType(val) {
                    if (this.inputType === 'camera') this.stopCamera();
                    this.inputType = val;
                    if (val === 'camera') this.startCamera();
                    if (val === 'auto') this.$nextTick(() => this.focusInput());
                },

                startCamera() {
                    this.$nextTick(() => {
                        this.html5QrCode = new Html5Qrcode("reader");
                        this.html5QrCode.start({ facingMode: "environment" }, { fps: 15, qrbox: 250 }, 
                            (text) => { this.scannedCode = text; this.processScan(); }
                        ).catch(() => this.setInputType('manual'));
                    });
                },

                stopCamera() { if (this.html5QrCode) this.html5QrCode.stop().catch(() => {}); },

                processManualScan() { if(!this.manualCode) return; this.scannedCode = this.manualCode; this.manualCode = ''; this.processScan(); },

                processScan() {
                    if (!this.scannedCode || this.status === 'processing') return;
                    const code = this.scannedCode.trim();
                    this.scannedCode = '';
                    this.lastScannedCode = code;
                    this.status = 'processing';

                    fetch('{{ route('organizer.gate.process') }}', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: JSON.stringify({ ticket_code: code, event_id: {{ $event->id }}, mode: this.mode, gate_name: '{{ session('gate_name') }}' })
                    })
                    .then(r => r.ok ? r.json() : r.json().then(e => { throw e }))
                    .then(d => this.handleSuccess(d))
                    .catch(e => this.handleError(e.message || 'Gagal'))
                    .finally(() => { if(this.inputType === 'auto') this.focusInput(); });
                },

                handleSuccess(d) {
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
                }
            }
        }
    </script>

    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 3px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #1e293b; border-radius: 10px; }
        .animate-scan-line { animation: scan-line 2s ease-in-out infinite; }
        @keyframes scan-line { 0%, 100% { top: 0%; } 50% { top: 100%; } }
        .shake { animation: shake 0.5s cubic-bezier(.36,.07,.19,.97) both; }
        @keyframes shake { 10%, 90% { transform: translate3d(-1px, 0, 0); } 20%, 80% { transform: translate3d(2px, 0, 0); } 30%, 50%, 70% { transform: translate3d(-4px, 0, 0); } 40%, 60% { transform: translate3d(4px, 0, 0); } }
    </style>
</x-app-layout>
