<x-app-layout>
    <x-slot name="hideNav">{{ true }}</x-slot>
    <x-slot name="title">SCANNER GATE - {{ $event->name }}</x-slot>
    
    <div class="h-screen w-full flex flex-col bg-[#0f172a] text-white overflow-hidden" x-data="gateScanner()">
        <!-- Top Status Bar (Responsive) -->
        <div class="flex items-center justify-between px-4 py-3 bg-[#1e293b] border-b border-slate-800 shrink-0">
            <div class="flex items-center gap-3">
                <a href="{{ route('organizer.gate.setup', $event) }}" class="p-2 hover:bg-slate-700 rounded-lg transition text-slate-400">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                </a>
                <div class="hidden sm:block">
                    <h2 class="text-sm font-black font-outfit uppercase tracking-wider">{{ $event->name }}</h2>
                    <p class="text-[10px] font-bold text-orange-500 uppercase tracking-[0.2em]">{{ session('gate_name') }}</p>
                </div>
            </div>

            <div class="flex items-center gap-2 sm:gap-4">
                <!-- Mode Indicator -->
                <div class="flex bg-slate-900 p-1 rounded-xl border border-slate-800">
                    <button @click="setMode('IN')" :class="mode === 'IN' ? 'bg-emerald-600 text-white shadow-lg' : 'text-slate-500 hover:text-white'" class="px-3 py-1.5 rounded-lg text-[10px] font-black uppercase tracking-widest transition-all">IN</button>
                    <button @click="setMode('OUT')" :class="mode === 'OUT' ? 'bg-orange-600 text-white shadow-lg' : 'text-slate-500 hover:text-white'" class="px-3 py-1.5 rounded-lg text-[10px] font-black uppercase tracking-widest transition-all">OUT</button>
                </div>

                <!-- Input Method Toggle -->
                <div class="flex bg-slate-900 p-1 rounded-xl border border-slate-800">
                    <button @click="inputType = 'auto'" :class="inputType === 'auto' ? 'bg-indigo-600 text-white' : 'text-slate-500'" class="p-1.5 rounded-lg transition-all" title="Auto Scan (Infra Red)">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" /></svg>
                    </button>
                    <button @click="toggleCamera()" :class="inputType === 'camera' ? 'bg-indigo-600 text-white' : 'text-slate-500'" class="p-1.5 rounded-lg transition-all" title="Camera Scan">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                    </button>
                    <button @click="inputType = 'manual'" :class="inputType === 'manual' ? 'bg-indigo-600 text-white' : 'text-slate-500'" class="p-1.5 rounded-lg transition-all" title="Manual Input">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col lg:flex-row min-h-0">
            <!-- Left: Scanning Area (Responsive Grid) -->
            <div class="flex-1 flex flex-col p-4 sm:p-6 gap-6 overflow-y-auto">
                
                <!-- Active Scanner Interface -->
                <div class="relative flex-1 min-h-[300px] rounded-[2.5rem] overflow-hidden border-4 transition-all duration-500 bg-[#1e293b] shadow-2xl"
                     :class="statusClasses">
                    
                    <div class="absolute inset-0 opacity-20" :class="bgAnimationClasses"></div>

                    <!-- IR Scanner Hidden Input -->
                    <input type="text" x-ref="ticketInput" @keydown.enter="processScan()" 
                           class="absolute opacity-0 pointer-events-none" 
                           x-model="scannedCode" autocomplete="off"
                           x-show="inputType === 'auto'">

                    <!-- Camera Viewport -->
                    <div x-show="inputType === 'camera'" id="reader" class="absolute inset-0 w-full h-full object-cover"></div>

                    <!-- Manual Input UI -->
                    <div x-show="inputType === 'manual'" class="absolute inset-0 flex flex-col items-center justify-center p-8 bg-slate-900/80 backdrop-blur-md z-10 animate-in fade-in zoom-in duration-300">
                        <div class="w-full max-w-md space-y-6">
                            <h3 class="text-xl font-black text-center uppercase tracking-widest text-indigo-400">Manual Entry</h3>
                            <div class="relative">
                                <input type="text" x-model="manualCode" placeholder="Masukkan Kode Tiket..." 
                                       @keydown.enter="processManualScan()"
                                       class="w-full px-8 py-5 bg-slate-800 border-2 border-slate-700 rounded-2xl text-2xl font-black text-center tracking-widest focus:border-indigo-500 focus:ring-0 text-white placeholder:text-slate-600">
                            </div>
                            <button @click="processManualScan()" class="w-full py-4 bg-indigo-600 rounded-2xl font-black uppercase tracking-widest hover:bg-indigo-700 transition shadow-xl shadow-indigo-500/20">Check Validitas</button>
                        </div>
                    </div>

                    <!-- Dynamic Feedback Overlay -->
                    <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none z-20">
                        
                        <!-- Idle State -->
                        <template x-if="status === 'idle'">
                            <div class="text-center animate-in fade-in duration-500">
                                <div class="w-24 h-24 bg-white/5 rounded-full flex items-center justify-center mx-auto mb-6 border-2 border-white/10">
                                    <svg class="w-12 h-12 text-white/20 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" /></svg>
                                </div>
                                <h3 class="text-2xl font-black uppercase tracking-[0.2em] text-white/40">Ready to Scan</h3>
                                <p class="text-xs font-bold text-white/20 uppercase mt-2" x-text="inputType === 'camera' ? 'Arahkan Kamera ke QR Code' : 'Gunakan Scanner Infra Red'"></p>
                            </div>
                        </template>

                        <!-- Success State -->
                        <template x-if="status === 'success'">
                            <div class="text-center animate-in zoom-in duration-300 bg-emerald-600/90 w-full h-full flex flex-col items-center justify-center p-8 backdrop-blur-sm">
                                <div class="w-32 h-32 bg-white rounded-full flex items-center justify-center mx-auto mb-8 text-emerald-600 shadow-2xl">
                                    <svg class="w-20 h-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M5 13l4 4L19 7" /></svg>
                                </div>
                                <h3 class="text-4xl sm:text-6xl font-black uppercase tracking-tight text-white mb-2" x-text="result.customer"></h3>
                                <div class="px-8 py-2 bg-white text-emerald-700 rounded-full font-black text-xl uppercase tracking-widest" x-text="result.category"></div>
                                <p class="text-emerald-200 mt-8 font-black text-3xl uppercase tracking-[0.4em]" x-text="mode === 'IN' ? 'CHECK-IN OK' : 'CHECK-OUT OK'"></p>
                            </div>
                        </template>

                        <!-- Error State -->
                        <template x-if="status === 'error'">
                            <div class="text-center animate-in shake duration-500 bg-rose-600/95 w-full h-full flex flex-col items-center justify-center p-8 backdrop-blur-md">
                                <div class="w-32 h-32 bg-white rounded-full flex items-center justify-center mx-auto mb-8 text-rose-600 shadow-2xl">
                                    <svg class="w-20 h-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M6 18L18 6M6 6l12 12" /></svg>
                                </div>
                                <h3 class="text-3xl sm:text-5xl font-black text-white uppercase tracking-tight mb-6">ACCESS DENIED</h3>
                                <div class="bg-white/10 text-white px-10 py-5 rounded-3xl font-black text-2xl border-2 border-white/20 backdrop-blur-md" x-text="errorMessage"></div>
                                <div class="mt-10 text-white/40 font-bold uppercase text-lg tracking-widest" x-text="'CODE: ' + lastScannedCode"></div>
                            </div>
                        </template>

                        <!-- Processing State -->
                        <template x-if="status === 'processing'">
                            <div class="text-center bg-slate-900/50 w-full h-full flex flex-col items-center justify-center backdrop-blur-sm">
                                <div class="w-20 h-20 border-8 border-white/10 border-t-indigo-500 rounded-full animate-spin mx-auto mb-6"></div>
                                <h3 class="text-xl font-black uppercase tracking-widest">Validating Ticket...</h3>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Responsive Stats Grid (Bottom of scanner on mobile) -->
                <div class="grid grid-cols-3 gap-4">
                    <div class="bg-[#1e293b] p-4 rounded-3xl border border-slate-800 text-center">
                        <div class="text-2xl font-black font-outfit text-emerald-500" x-text="inCount">0</div>
                        <div class="text-[9px] font-black text-slate-500 uppercase tracking-widest mt-1">Total IN</div>
                    </div>
                    <div class="bg-[#1e293b] p-4 rounded-3xl border border-slate-800 text-center">
                        <div class="text-2xl font-black font-outfit text-orange-500" x-text="outCount">0</div>
                        <div class="text-[9px] font-black text-slate-500 uppercase tracking-widest mt-1">Total OUT</div>
                    </div>
                    <div class="bg-[#1e293b] p-4 rounded-3xl border border-indigo-500/50 text-center relative overflow-hidden group">
                        <div class="absolute inset-0 bg-indigo-500/5 group-hover:bg-indigo-500/10 transition-colors"></div>
                        <div class="text-2xl font-black font-outfit text-white" x-text="inCount - outCount">0</div>
                        <div class="text-[9px] font-black text-indigo-400 uppercase tracking-widest mt-1">Inside Area</div>
                    </div>
                </div>
            </div>

            <!-- Right: Recent Activity (Hidden on small screens, shown in drawer or bottom) -->
            <div class="w-full lg:w-96 bg-[#131b2e] border-l border-slate-800 flex flex-col shrink-0">
                <div class="p-4 bg-slate-900/50 border-b border-slate-800 flex items-center justify-between">
                    <h4 class="text-xs font-black text-slate-500 uppercase tracking-widest">Recent Activity</h4>
                    <span class="px-2 py-1 bg-indigo-500/10 text-indigo-400 rounded text-[9px] font-bold uppercase" x-text="history.length + ' Logs'"></span>
                </div>
                <div class="flex-1 overflow-y-auto p-4 space-y-3 custom-scrollbar">
                    <template x-for="log in history" :key="log.time">
                        <div class="flex items-center justify-between p-4 rounded-2xl border transition-all animate-in slide-in-from-right-4"
                             :class="log.success ? 'bg-slate-900/50 border-slate-800' : 'bg-rose-500/5 border-rose-500/20'">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg flex items-center justify-center text-[10px] font-black"
                                     :class="log.type === 'IN' ? 'bg-emerald-500/20 text-emerald-400' : 'bg-orange-500/20 text-orange-400'">
                                    <span x-text="log.type"></span>
                                </div>
                                <div>
                                    <div class="text-xs font-black text-white leading-tight" x-text="log.name || 'Access Denied'"></div>
                                    <div class="text-[9px] font-bold text-slate-500 uppercase tracking-tighter" x-text="log.category || log.code"></div>
                                </div>
                            </div>
                            <div class="text-[9px] font-bold text-slate-600" x-text="log.time"></div>
                        </div>
                    </template>
                    <template x-if="history.length === 0">
                        <div class="text-center py-20 text-slate-700 italic text-xs">Belum ada aktifitas scan...</div>
                    </template>
                </div>
            </div>
        </div>
    </div>

    <!-- Audio Feedback -->
    <audio id="sound-success" src="https://assets.mixkit.co/active_storage/sfx/2568/2568-preview.mp3"></audio>
    <audio id="sound-error" src="https://assets.mixkit.co/active_storage/sfx/2571/2571-preview.mp3"></audio>

    <script src="https://unpkg.com/html5-qrcode"></script>
    <script>
        function gateScanner() {
            return {
                mode: '{{ session('gate_mode', 'IN') }}',
                inputType: 'auto', // auto, camera, manual
                status: 'idle', 
                scannedCode: '',
                manualCode: '',
                lastScannedCode: '',
                errorMessage: '',
                result: { customer: '', category: '' },
                inCount: {{ $inCount }},
                outCount: {{ $outCount }},
                history: [],
                audioCtx: null,
                html5QrCode: null,

                init() {
                    this.$nextTick(() => {
                        this.focusInput();
                    });
                    
                    document.addEventListener('click', () => {
                        if(this.inputType === 'auto') this.focusInput();
                    });

                    // Keep focusing for auto-scanner
                    setInterval(() => {
                        if(this.inputType === 'auto' && this.status === 'idle') this.focusInput();
                    }, 1000);
                },

                focusInput() {
                    if (this.$refs.ticketInput) this.$refs.ticketInput.focus();
                },

                setMode(newMode) {
                    this.mode = newMode;
                    this.status = 'idle';
                },

                toggleCamera() {
                    if (this.inputType === 'camera') {
                        this.stopCamera();
                        this.inputType = 'auto';
                    } else {
                        this.inputType = 'camera';
                        this.startCamera();
                    }
                },

                startCamera() {
                    this.$nextTick(() => {
                        this.html5QrCode = new Html5Qrcode("reader");
                        const config = { fps: 10, qrbox: { width: 250, height: 250 } };
                        
                        this.html5QrCode.start(
                            { facingMode: "environment" }, 
                            config,
                            (decodedText) => {
                                this.scannedCode = decodedText;
                                this.processScan();
                            }
                        ).catch(err => {
                            console.error("Camera Error:", err);
                            this.inputType = 'manual';
                        });
                    });
                },

                stopCamera() {
                    if (this.html5QrCode) {
                        this.html5QrCode.stop().catch(err => console.error(err));
                    }
                },

                processManualScan() {
                    if (!this.manualCode) return;
                    this.scannedCode = this.manualCode;
                    this.manualCode = '';
                    this.processScan();
                },

                processScan() {
                    if (!this.scannedCode || this.status === 'processing') return;

                    const code = this.scannedCode.trim();
                    this.scannedCode = '';
                    this.lastScannedCode = code;
                    this.status = 'processing';

                    fetch('{{ route('organizer.gate.process') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            ticket_code: code,
                            event_id: {{ $event->id }},
                            mode: this.mode,
                            gate_name: '{{ session('gate_name') }}'
                        })
                    })
                    .then(response => {
                        if (!response.ok) return response.json().then(err => { throw err });
                        return response.json();
                    })
                    .then(data => {
                        this.handleSuccess(data);
                    })
                    .catch(err => {
                        this.handleError(err.message || 'Gagal Memproses Tiket');
                    })
                    .finally(() => {
                        if(this.inputType === 'auto') this.focusInput();
                    });
                },

                handleSuccess(data) {
                    this.status = 'success';
                    this.result = { customer: data.customer, category: data.category };
                    this.inCount = data.in_count;
                    this.outCount = data.out_count;
                    
                    this.history.unshift({
                        success: true,
                        type: this.mode,
                        name: data.customer,
                        category: data.category,
                        time: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', second: '2-digit' })
                    });
                    if (this.history.length > 20) this.history.pop();

                    document.getElementById('sound-success').play();

                    setTimeout(() => {
                        if (this.status === 'success') this.status = 'idle';
                    }, 2500);
                },

                handleError(message) {
                    this.status = 'error';
                    this.errorMessage = message;
                    
                    this.history.unshift({
                        success: false,
                        type: this.mode,
                        name: 'Akses Ditolak',
                        code: this.lastScannedCode,
                        time: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', second: '2-digit' })
                    });

                    document.getElementById('sound-error').play();

                    setTimeout(() => {
                        if (this.status === 'error') this.status = 'idle';
                    }, 4000);
                },

                get statusClasses() {
                    return {
                        'border-slate-800': this.status === 'idle' || this.status === 'processing',
                        'border-emerald-500 ring-8 ring-emerald-500/20': this.status === 'success',
                        'border-rose-500 ring-8 ring-rose-500/20': this.status === 'error',
                    };
                },

                get bgAnimationClasses() {
                    return {
                        'bg-slate-500': this.status === 'idle' || this.status === 'processing',
                        'bg-emerald-500 animate-pulse': this.status === 'success',
                        'bg-rose-500 animate-ping': this.status === 'error',
                    };
                }
            }
        }
    </script>

    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #0f172a; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #1e293b; border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #334155; }
        
        .shake { animation: shake 0.5s cubic-bezier(.36,.07,.19,.97) both; }
        @keyframes shake {
            10%, 90% { transform: translate3d(-1px, 0, 0); }
            20%, 80% { transform: translate3d(2px, 0, 0); }
            30%, 50%, 70% { transform: translate3d(-4px, 0, 0); }
            40%, 60% { transform: translate3d(4px, 0, 0); }
        }
    </style>
</x-app-layout>
