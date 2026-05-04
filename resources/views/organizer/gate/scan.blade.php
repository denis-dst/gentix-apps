<x-app-layout>
    <x-slot name="title">Scanner Gate - {{ $event->name }}</x-slot>
    
    <div class="h-[calc(100vh-14rem)] flex flex-col gap-6" x-data="gateScanner()">
        <!-- Header Info -->
        <div class="flex items-center justify-between bg-white p-6 rounded-3xl shadow-sm border border-slate-100">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-orange-600 text-white rounded-xl flex items-center justify-center shadow-lg shadow-orange-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
                </div>
                <div>
                    <h2 class="text-xl font-black text-slate-900 font-outfit uppercase tracking-tight">{{ $event->name }}</h2>
                    <div class="flex items-center gap-2 text-xs font-bold text-slate-400 uppercase tracking-widest">
                        <span class="text-indigo-600">{{ session('gate_name') }}</span>
                        <span>•</span>
                        <span :class="mode === 'IN' ? 'text-emerald-600' : 'text-orange-600'" x-text="mode === 'IN' ? 'MODE: CHECK-IN' : 'MODE: CHECK-OUT'"></span>
                    </div>
                </div>
            </div>

            <div class="flex gap-4">
                <button @click="toggleMode()" class="px-6 py-3 rounded-2xl font-black text-sm transition-all flex items-center gap-2"
                        :class="mode === 'IN' ? 'bg-orange-100 text-orange-600 hover:bg-orange-200' : 'bg-emerald-100 text-emerald-600 hover:bg-emerald-200'">
                    <i class="fas fa-exchange-alt"></i>
                    <span x-text="mode === 'IN' ? 'Switch to Check-Out' : 'Switch to Check-In'"></span>
                </button>
                <a href="{{ route('organizer.gate.setup', $event) }}" class="p-3 bg-slate-100 text-slate-500 rounded-2xl hover:bg-slate-200 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                </a>
            </div>
        </div>

        <div class="flex-1 grid grid-cols-1 lg:grid-cols-12 gap-6 min-h-0">
            <!-- Left Side: Scanner Interaction -->
            <div class="lg:col-span-8 flex flex-col gap-6 min-h-0">
                <!-- Status Display -->
                <div class="flex-1 rounded-[3rem] border-4 flex flex-col items-center justify-center transition-all duration-300 relative overflow-hidden bg-white shadow-2xl"
                     :class="statusClasses">
                    
                    <div class="absolute inset-0 opacity-10" :class="bgAnimationClasses"></div>

                    <!-- Input for IR Scanner (Always focused) -->
                    <input type="text" x-ref="ticketInput" @keydown.enter="processScan()" 
                           class="absolute opacity-0 pointer-events-none" 
                           x-model="scannedCode" autocomplete="off">

                    <!-- Visual Feedback -->
                    <template x-if="status === 'idle'">
                        <div class="text-center animate-in fade-in zoom-in duration-500">
                            <div class="w-32 h-32 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-8 text-slate-300 border-4 border-slate-100">
                                <svg class="w-16 h-16 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" /></svg>
                            </div>
                            <h3 class="text-3xl font-black text-slate-800 font-outfit uppercase tracking-widest">Ready to Scan</h3>
                            <p class="text-slate-400 mt-2 font-bold uppercase tracking-widest text-sm">Sorotkan QR Tiket ke Scanner</p>
                        </div>
                    </template>

                    <template x-if="status === 'success'">
                        <div class="text-center animate-in zoom-in duration-300">
                            <div class="w-40 h-40 bg-emerald-500 rounded-full flex items-center justify-center mx-auto mb-8 text-white shadow-2xl shadow-emerald-200">
                                <svg class="w-24 h-24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" /></svg>
                            </div>
                            <h3 class="text-5xl font-black text-emerald-600 font-outfit uppercase tracking-tight mb-2" x-text="result.customer"></h3>
                            <div class="inline-block px-6 py-2 bg-emerald-600 text-white rounded-full font-black text-lg uppercase tracking-widest" x-text="result.category"></div>
                            <p class="text-emerald-500 mt-6 font-black text-2xl uppercase tracking-[0.3em]" x-text="mode === 'IN' ? 'Check-in Success' : 'Check-out Success'"></p>
                        </div>
                    </template>

                    <template x-if="status === 'error'">
                        <div class="text-center animate-in shake duration-500">
                            <div class="w-40 h-40 bg-rose-500 rounded-full flex items-center justify-center mx-auto mb-8 text-white shadow-2xl shadow-rose-200">
                                <svg class="w-24 h-24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12" /></svg>
                            </div>
                            <h3 class="text-4xl font-black text-rose-600 font-outfit uppercase tracking-tight mb-4">ACCESS DENIED</h3>
                            <div class="bg-rose-100 text-rose-700 px-8 py-4 rounded-3xl font-black text-xl border-2 border-rose-200" x-text="errorMessage"></div>
                            <div class="mt-8 text-slate-400 font-bold uppercase text-sm" x-text="lastScannedCode"></div>
                        </div>
                    </template>

                    <template x-if="status === 'processing'">
                        <div class="text-center">
                            <div class="w-20 h-20 border-8 border-slate-100 border-t-orange-500 rounded-full animate-spin mx-auto mb-8"></div>
                            <h3 class="text-2xl font-black text-slate-800 font-outfit uppercase">Memproses...</h3>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Right Side: Statistics & History -->
            <div class="lg:col-span-4 flex flex-col gap-6 min-h-0">
                <!-- Stats Card -->
                <div class="bg-[#0f172a] text-white rounded-[2.5rem] p-8 shadow-2xl relative overflow-hidden">
                    <div class="absolute -right-10 -top-10 w-40 h-40 bg-orange-500/10 rounded-full blur-3xl"></div>
                    <h3 class="text-xs font-black text-slate-400 uppercase tracking-[0.3em] mb-8">Live Statistics</h3>
                    
                    <div class="grid grid-cols-2 gap-8">
                        <div class="space-y-1">
                            <div class="text-4xl font-black font-outfit" x-text="inCount">0</div>
                            <div class="text-[10px] font-black text-emerald-400 uppercase tracking-widest">Total In</div>
                        </div>
                        <div class="space-y-1">
                            <div class="text-4xl font-black font-outfit" x-text="outCount">0</div>
                            <div class="text-[10px] font-black text-orange-400 uppercase tracking-widest">Total Out</div>
                        </div>
                        <div class="col-span-2 pt-6 border-t border-slate-800">
                            <div class="flex items-end justify-between">
                                <div>
                                    <div class="text-5xl font-black font-outfit text-white" x-text="inCount - outCount">0</div>
                                    <div class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] mt-2">Inside Area</div>
                                </div>
                                <div class="w-16 h-16 bg-white/5 rounded-2xl flex items-center justify-center text-orange-500">
                                    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20"><path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z" /></svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- History -->
                <div class="flex-1 bg-white rounded-[2.5rem] border border-slate-100 shadow-xl overflow-hidden flex flex-col min-h-0">
                    <div class="p-6 border-b border-slate-50 bg-slate-50/50">
                        <h4 class="text-xs font-black text-slate-800 uppercase tracking-widest">Recent Logs</h4>
                    </div>
                    <div class="flex-1 overflow-y-auto p-4 space-y-3">
                        <template x-for="log in history" :key="log.time">
                            <div class="flex items-center justify-between p-4 rounded-2xl bg-slate-50 border border-slate-100 animate-in slide-in-from-right-4 duration-300">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg flex items-center justify-center text-[10px] font-bold"
                                         :class="log.type === 'IN' ? 'bg-emerald-100 text-emerald-600' : 'bg-orange-100 text-orange-600'">
                                        <span x-text="log.type"></span>
                                    </div>
                                    <div>
                                        <div class="text-sm font-black text-slate-800 leading-tight" x-text="log.name"></div>
                                        <div class="text-[10px] font-bold text-slate-400 uppercase tracking-tighter" x-text="log.category"></div>
                                    </div>
                                </div>
                                <div class="text-[10px] font-bold text-slate-400" x-text="log.time"></div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function gateScanner() {
            return {
                mode: '{{ session('gate_mode', 'IN') }}',
                status: 'idle', // idle, processing, success, error
                scannedCode: '',
                lastScannedCode: '',
                errorMessage: '',
                result: { customer: '', category: '' },
                inCount: {{ $inCount }},
                outCount: {{ $outCount }},
                history: [],
                audioCtx: null,

                init() {
                    this.$nextTick(() => {
                        this.focusInput();
                    });
                    
                    // Keep focus on input
                    document.addEventListener('click', () => this.focusInput());
                    
                    // Initialize Audio Context on first user interaction
                    const initAudio = () => {
                        this.audioCtx = new (window.AudioContext || window.webkitAudioContext)();
                        document.removeEventListener('click', initAudio);
                    };
                    document.addEventListener('click', initAudio);
                },

                focusInput() {
                    this.$refs.ticketInput.focus();
                },

                toggleMode() {
                    this.mode = this.mode === 'IN' ? 'OUT' : 'IN';
                    this.status = 'idle';
                    this.focusInput();
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
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            this.handleSuccess(data);
                        } else {
                            this.handleError(data.message);
                        }
                    })
                    .catch(err => {
                        this.handleError('Network error or server failed');
                    })
                    .finally(() => {
                        this.focusInput();
                    });
                },

                handleSuccess(data) {
                    this.status = 'success';
                    this.result = { customer: data.customer, category: data.category };
                    this.inCount = data.in_count;
                    this.outCount = data.out_count;
                    
                    this.history.unshift({
                        type: this.mode,
                        name: data.customer,
                        category: data.category,
                        time: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', second: '2-digit' })
                    });
                    if (this.history.length > 5) this.history.pop();

                    this.playBeep(880, 0.2); // Sharp high beep

                    setTimeout(() => {
                        if (this.status === 'success') this.status = 'idle';
                    }, 2000);
                },

                handleError(message) {
                    this.status = 'error';
                    this.errorMessage = message;
                    
                    this.playBeep(220, 0.5); // Firm low beep

                    setTimeout(() => {
                        if (this.status === 'error') this.status = 'idle';
                    }, 3000);
                },

                playBeep(frequency, duration) {
                    if (!this.audioCtx) return;
                    
                    const osc = this.audioCtx.createOscillator();
                    const gain = this.audioCtx.createGain();
                    
                    osc.type = 'square';
                    osc.frequency.setValueAtTime(frequency, this.audioCtx.currentTime);
                    
                    gain.gain.setValueAtTime(0.2, this.audioCtx.currentTime);
                    gain.gain.exponentialRampToValueAtTime(0.01, this.audioCtx.currentTime + duration);
                    
                    osc.connect(gain);
                    gain.connect(this.audioCtx.destination);
                    
                    osc.start();
                    osc.stop(this.audioCtx.currentTime + duration);
                },

                get statusClasses() {
                    return {
                        'border-slate-100': this.status === 'idle' || this.status === 'processing',
                        'border-emerald-500 shadow-emerald-100': this.status === 'success',
                        'border-rose-500 shadow-rose-100': this.status === 'error',
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
        .shake {
            animation: shake 0.5s cubic-bezier(.36,.07,.19,.97) both;
        }
        @keyframes shake {
            10%, 90% { transform: translate3d(-1px, 0, 0); }
            20%, 80% { transform: translate3d(2px, 0, 0); }
            30%, 50%, 70% { transform: translate3d(-4px, 0, 0); }
            40%, 60% { transform: translate3d(4px, 0, 0); }
        }
    </style>
</x-app-layout>
