<x-app-layout>
    <x-slot name="hideNav">{{ true }}</x-slot>
    <x-slot name="title">REDEEM - {{ $event->name }}</x-slot>

    <div class="h-full w-full flex flex-col bg-[#010409] text-white overflow-hidden font-sans" x-data="redeemScanner()">
        <!-- Header Bar -->
        <header class="h-auto min-h-[5.5rem] pt-10 sm:pt-0 flex items-center justify-between px-4 sm:px-6 bg-[#0d1117] border-b border-white/5 shrink-0 z-50">
            <div class="flex items-center gap-3 sm:gap-4 py-4 sm:py-0">
                <a href="{{ route('organizer.redeem.index') }}" class="flex items-center gap-2 bg-white/5 hover:bg-white/10 px-3 py-2 rounded-xl transition text-slate-300">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                    <span class="text-[10px] font-black uppercase tracking-widest">BACK</span>
                </a>
                <div class="leading-tight">
                    <h2 class="text-[10px] sm:text-xs font-black uppercase tracking-[0.2em] text-white/90 truncate max-w-[120px] sm:max-w-none font-outfit">{{ $event->name }}</h2>
                    <p class="text-[8px] sm:text-[10px] font-bold text-emerald-500 uppercase tracking-widest mt-0.5">LOKET REDEEM</p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <!-- Sync Badge (Offline Only) -->
                <div x-show="mode === 'offline' && pendingSync.length > 0" class="flex items-center gap-2 bg-amber-500/10 border border-amber-500/20 px-3 py-1.5 rounded-xl animate-pulse">
                    <div class="w-1.5 h-1.5 bg-amber-500 rounded-full"></div>
                    <span class="text-[9px] font-black text-amber-500 uppercase tracking-wider" x-text="pendingSync.length + ' Pending'"></span>
                </div>

                <!-- Mode Switcher -->
                <div class="flex bg-black p-1 rounded-xl border border-white/5">
                    <button @click="setMode('online')" :class="mode === 'online' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/20' : 'text-slate-500 hover:text-slate-300'" class="px-3 sm:px-4 py-1 sm:py-1.5 rounded-lg text-[8px] sm:text-[10px] font-black transition-all uppercase tracking-widest">ONLINE</button>
                    <button @click="setMode('offline')" :class="mode === 'offline' ? 'bg-amber-600 text-white shadow-lg shadow-amber-600/20' : 'text-slate-500 hover:text-slate-300'" class="px-3 sm:px-4 py-1 sm:py-1.5 rounded-lg text-[8px] sm:text-[10px] font-black transition-all uppercase tracking-widest">OFFLINE</button>
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
                            'bg-emerald-500': result && result.success,
                            'bg-rose-500': result && !result.success,
                            'bg-slate-900': !result
                         }">
                    </div>

                    <!-- Camera Container -->
                    <div id="reader" class="absolute inset-0 w-full h-full object-cover bg-black"></div>

                    <!-- Feedback Overlays -->
                    <div class="absolute inset-0 flex items-center justify-center pointer-events-none z-30">
                        <!-- Idle Overlay -->
                        <div x-show="!processing && !result && !cameraError" class="flex flex-col items-center justify-center w-full h-full">
                            <div class="relative group">
                                <div class="absolute inset-0 rounded-full bg-indigo-500/20 animate-ping duration-1000"></div>
                                <div class="absolute -inset-8 rounded-full bg-indigo-500/5 animate-pulse duration-700"></div>
                                <div class="w-64 h-64 sm:w-80 sm:h-80 border-2 border-white/10 rounded-[3rem] relative bg-white/5 backdrop-blur-sm shadow-2xl shadow-indigo-500/10">
                                    <div class="absolute top-0 left-0 w-16 h-16 border-t-4 border-l-4 border-indigo-500 rounded-tl-[2.5rem]"></div>
                                    <div class="absolute top-0 right-0 w-16 h-16 border-t-4 border-r-4 border-indigo-500 rounded-tr-[2.5rem]"></div>
                                    <div class="absolute bottom-0 left-0 w-16 h-16 border-b-4 border-l-4 border-indigo-500 rounded-bl-[2.5rem]"></div>
                                    <div class="absolute bottom-0 right-0 w-16 h-16 border-b-4 border-r-4 border-indigo-500 rounded-br-[2.5rem]"></div>
                                    <div class="absolute top-1/2 left-6 right-6 h-[2px] bg-indigo-500 shadow-[0_0_15px_rgba(79,70,229,0.8)] blur-[0.5px] animate-scan-line"></div>
                                </div>
                            </div>
                            <div class="mt-12 text-center">
                                <p class="text-xs sm:text-sm font-black uppercase tracking-[0.6em] text-white/60 animate-pulse">Ready to Redeem</p>
                                <p class="mt-2 text-[8px] font-bold text-slate-500 uppercase tracking-widest opacity-50">Focus Camera on Ticket QR Code</p>
                            </div>
                        </div>

                        <!-- Success Overlay -->
                        <template x-if="result && result.success">
                            <div class="bg-[#065f46] fixed inset-0 flex flex-col items-center justify-center p-6 animate-in fade-in duration-200 z-50">
                                <div class="w-32 h-32 bg-white rounded-full flex items-center justify-center text-emerald-600 mb-8 shadow-2xl animate-bounce">
                                    <svg class="w-20 h-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M5 13l4 4L19 7" /></svg>
                                </div>
                                <h3 class="text-4xl sm:text-6xl font-black text-white uppercase tracking-tight text-center mb-2 font-outfit" x-text="result.customer_name"></h3>
                                <div class="px-8 py-2 bg-white text-emerald-900 rounded-full font-black text-sm uppercase tracking-[0.2em] mb-8" x-text="result.category_name"></div>
                                
                                <div class="w-full max-w-xs aspect-video bg-black/20 rounded-3xl border border-white/20 overflow-hidden shadow-2xl">
                                    <img :src="result.photo_url" class="w-full h-full object-cover">
                                </div>
                                
                                <p class="mt-12 text-2xl sm:text-3xl font-black uppercase tracking-[0.5em] text-white/50">BERHASIL</p>
                            </div>
                        </template>

                        <!-- Error Overlay -->
                        <template x-if="result && !result.success">
                            <div class="bg-[#9f1239] fixed inset-0 flex flex-col items-center justify-center p-6 animate-in shake duration-300 z-50">
                                <div class="w-32 h-32 bg-white rounded-full flex items-center justify-center text-rose-600 mb-8 shadow-2xl">
                                    <svg class="w-20 h-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M6 18L18 6M6 6l12 12" /></svg>
                                </div>
                                <h3 class="text-3xl sm:text-5xl font-black text-white uppercase tracking-tight text-center mb-8 font-outfit">REDEEM GAGAL</h3>
                                <div class="bg-black/40 px-8 py-6 rounded-3xl border border-white/20 text-xl sm:text-2xl font-black text-center max-w-lg leading-relaxed" x-text="result.message"></div>
                                
                                <template x-if="result.redeem_photo">
                                    <div class="mt-8 w-full max-w-xs aspect-video bg-black/40 rounded-3xl border border-white/10 overflow-hidden grayscale opacity-50">
                                        <img :src="result.redeem_photo" class="w-full h-full object-cover">
                                    </div>
                                </template>

                                <p class="mt-12 text-white/40 font-bold uppercase tracking-[0.3em] text-sm" x-text="result.redeemed_at || 'CHECK DATA TIKET'"></p>
                            </div>
                        </template>

                        <!-- Processing Overlay -->
                        <div x-show="processing" class="bg-black/90 fixed inset-0 flex flex-col items-center justify-center backdrop-blur-xl z-50">
                            <div class="w-20 h-20 border-4 border-white/5 border-t-indigo-500 rounded-full animate-spin mb-6"></div>
                            <p class="text-[10px] font-black uppercase tracking-[0.4em] text-indigo-400">Verifying Ticket...</p>
                        </div>
                    </div>
                </div>

                <!-- Camera for Photo Capture (Hidden) -->
                <video id="photo-camera" class="fixed opacity-0 pointer-events-none -left-[9999px]"></video>
                <canvas id="photo-canvas" class="fixed opacity-0 pointer-events-none -left-[9999px]"></canvas>
            </div>

            <!-- Side Controls (Offline Management) -->
            <aside x-show="mode === 'offline'" class="w-full lg:w-96 bg-[#010409] border-l border-white/5 flex flex-col shrink-0 h-full overflow-hidden z-40">
                <div class="h-16 px-6 bg-[#0d1117] border-b border-white/5 flex items-center justify-between shrink-0">
                    <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em]">Offline Data</h4>
                    <span class="bg-indigo-500/10 text-indigo-400 px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-wider" x-text="offlineTickets.length + ' Tiket Ready'"></span>
                </div>
                <div class="flex-1 p-6 space-y-4">
                    <button @click="downloadData" :disabled="downloading" class="w-full py-5 bg-emerald-600 rounded-2xl font-black uppercase tracking-widest text-xs hover:bg-emerald-700 transition shadow-lg shadow-emerald-600/20 disabled:opacity-50 flex items-center justify-center gap-3">
                        <template x-if="!downloading">
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                Update Database Lokal
                            </div>
                        </template>
                        <template x-if="downloading">
                            <div class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></div>
                        </template>
                    </button>

                    <button @click="syncData" x-show="pendingSync.length > 0" :disabled="syncing" class="w-full py-5 bg-indigo-600 rounded-2xl font-black uppercase tracking-widest text-xs hover:bg-indigo-700 transition shadow-lg shadow-indigo-600/20 flex items-center justify-center gap-3">
                        <template x-if="!syncing">
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                Sinkronkan <span x-text="pendingSync.length"></span> Data
                            </div>
                        </template>
                        <template x-if="syncing">
                            <div class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></div>
                        </template>
                    </button>

                    <div class="mt-8 p-4 bg-white/5 rounded-2xl border border-white/5">
                        <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2">Penting:</p>
                        <p class="text-[9px] leading-relaxed text-slate-400">Mode offline menyimpan data redeem di perangkat ini. Pastikan Anda melakukan sinkronisasi saat koneksi internet sudah stabil.</p>
                    </div>
                </div>
            </aside>
        </main>
    </div>

    <!-- Audio Effects -->
    <audio id="sound-success" src="https://assets.mixkit.co/active_storage/sfx/2013/2013-preview.mp3"></audio>
    <audio id="sound-fail" src="https://assets.mixkit.co/active_storage/sfx/2019/2019-preview.mp3"></audio>

    <script src="https://unpkg.com/html5-qrcode"></script>
    <script>
        function redeemScanner() {
            return {
                html5QrCode: null,
                processing: false,
                result: null,
                downloading: false,
                syncing: false,
                cameraError: false,
                autoResetTimer: 0,
                autoResetInterval: null,
                mode: localStorage.getItem('redeem_mode_{{ $event->id }}') || 'online',
                offlineTickets: JSON.parse(localStorage.getItem('offline_tickets_{{ $event->id }}')) || [],
                pendingSync: JSON.parse(localStorage.getItem('pending_sync_{{ $event->id }}')) || [],
                videoStream: null,

                init() {
                    setTimeout(() => {
                        this.startScanner();
                        this.initCamera();
                    }, 500);
                },

                setMode(newMode) {
                    this.mode = newMode;
                    localStorage.setItem('redeem_mode_{{ $event->id }}', newMode);
                },

                async initCamera() {
                    try {
                        this.videoStream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: "user" } });
                        const video = document.getElementById('photo-camera');
                        video.srcObject = this.videoStream;
                        video.play();
                    } catch (err) {
                        console.error("Selfie Camera error:", err);
                    }
                },

                startScanner() {
                    this.cameraError = false;
                    this.html5QrCode = new Html5Qrcode("reader");
                    const config = { fps: 15, qrbox: { width: 250, height: 250 } };
                    this.html5QrCode.start(
                        { facingMode: "environment" }, 
                        config, 
                        (text) => this.onScanSuccess(text)
                    ).catch(err => {
                        console.error("Scanner error:", err);
                        this.cameraError = true;
                    });
                },

                async downloadData() {
                    this.downloading = true;
                    try {
                        const response = await fetch("{{ route('organizer.redeem.download', $event) }}");
                        const data = await response.json();
                        this.offlineTickets = data.tickets;
                        localStorage.setItem('offline_tickets_{{ $event->id }}', JSON.stringify(data.tickets));
                        document.getElementById('sound-success').play();
                    } catch (err) {
                        alert("Gagal mengunduh data!");
                    } finally {
                        this.downloading = false;
                    }
                },

                async onScanSuccess(decodedText) {
                    if (this.processing || this.result) return;
                    this.processing = true;
                    
                    const photo = this.takePhoto();

                    if (this.mode === 'online') {
                        await this.processOnline(decodedText, photo);
                    } else {
                        await this.processOffline(decodedText, photo);
                    }

                    this.processing = false;
                    
                    // Auto reset after 4 seconds
                    this.autoResetTimer = 4;
                    this.autoResetInterval = setInterval(() => {
                        this.autoResetTimer--;
                        if (this.autoResetTimer <= 0) {
                            this.resetScanner();
                        }
                    }, 1000);
                },

                async processOnline(code, photo) {
                    try {
                        const response = await fetch("{{ route('organizer.redeem.process') }}", {
                            method: "POST",
                            headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": "{{ csrf_token() }}" },
                            body: JSON.stringify({ ticket_code: code, photo: photo, event_id: "{{ $event->id }}" })
                        });
                        this.result = await response.json();
                        this.playResultSound();
                    } catch (err) {
                        alert("Terjadi kesalahan sistem!");
                    }
                },

                async processOffline(code, photo) {
                    const ticketIndex = this.offlineTickets.findIndex(t => t.code === code);
                    
                    if (ticketIndex === -1) {
                        this.result = { success: false, message: 'Tiket tidak terdaftar di database offline!' };
                    } else {
                        const ticket = this.offlineTickets[ticketIndex];
                        if (ticket.status === 'redeemed') {
                            this.result = { 
                                success: false, 
                                message: 'Sudah pernah di-redeem (Offline)!',
                                redeemed_at: ticket.redeemed_at,
                                redeem_photo: ticket.redeem_photo
                            };
                        } else {
                            ticket.status = 'redeemed';
                            ticket.redeemed_at = new Date().toLocaleString();
                            ticket.redeem_photo = photo;
                            
                            this.pendingSync.push({ ticket_code: code, photo: photo, event_id: "{{ $event->id }}" });
                            
                            localStorage.setItem('offline_tickets_{{ $event->id }}', JSON.stringify(this.offlineTickets));
                            localStorage.setItem('pending_sync_{{ $event->id }}', JSON.stringify(this.pendingSync));

                            this.result = { 
                                success: true, 
                                customer_name: ticket.customer, 
                                category_name: ticket.category, 
                                photo_url: photo 
                            };
                        }
                    }
                    this.playResultSound();
                },

                async syncData() {
                    if (this.pendingSync.length === 0) return;
                    this.syncing = true;
                    const items = [...this.pendingSync];

                    for (const item of items) {
                        try {
                            const response = await fetch("{{ route('organizer.redeem.process') }}", {
                                method: "POST",
                                headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": "{{ csrf_token() }}" },
                                body: JSON.stringify(item)
                            });
                            const data = await response.json();
                            if (data.success || data.reason === 'already_redeemed') {
                                this.pendingSync = this.pendingSync.filter(i => i.ticket_code !== item.ticket_code);
                                localStorage.setItem('pending_sync_{{ $event->id }}', JSON.stringify(this.pendingSync));
                            }
                        } catch (err) {
                            console.error("Sync failed", err);
                        }
                    }
                    this.syncing = false;
                },

                playResultSound() {
                    const sound = document.getElementById(this.result.success ? 'sound-success' : 'sound-fail');
                    sound.currentTime = 0;
                    sound.play();
                },

                takePhoto() {
                    try {
                        const video = document.getElementById('photo-camera');
                        const canvas = document.getElementById('photo-canvas');
                        canvas.width = video.videoWidth || 640;
                        canvas.height = video.videoHeight || 480;
                        canvas.getContext('2d').drawImage(video, 0, 0);
                        return canvas.toDataURL('image/jpeg');
                    } catch (e) {
                        return null;
                    }
                },

                resetScanner() {
                    clearInterval(this.autoResetInterval);
                    this.result = null;
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
        #reader video { object-fit: cover !important; }
        .shake { animation: shake 0.5s cubic-bezier(.36, .07, .19, .97) both; }
        @keyframes shake { 10%, 90% { transform: translate3d(-1px, 0, 0); } 20%, 80% { transform: translate3d(2px, 0, 0); } 30%, 50%, 70% { transform: translate3d(-4px, 0, 0); } 40%, 60% { transform: translate3d(4px, 0, 0); } }
    </style>
</x-app-layout>
