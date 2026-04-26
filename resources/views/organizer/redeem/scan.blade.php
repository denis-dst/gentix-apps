<x-app-layout>
    <x-slot name="title">Scanning - {{ $event->name }}</x-slot>

    <div class="max-w-xl mx-auto space-y-6 animate-in fade-in duration-700 h-full pb-20" x-data="redeemScanner()">
        <!-- Mode Switcher & Sync Info -->
        <div style="background-color: white; border: 1px solid #e2e8f0; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);" class="rounded-[2rem] p-4 space-y-4">
            <div class="flex items-center justify-between px-2">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center text-white transition-colors duration-500"
                         :class="mode === 'online' ? 'bg-indigo-600' : 'bg-amber-500'">
                        <svg x-show="mode === 'online'" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071a9.05 9.05 0 0112.728 0M6.343 6.343a12.607 12.607 0 0117.678 0"/></svg>
                        <svg x-show="mode === 'offline'" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636a9 9 0 010 12.728m0 0l-2.829-2.829m2.829 2.829L21 21M15.536 8.464a5 5 0 010 7.072m0 0l-2.829-2.829m-4.243 2.829a4.978 4.978 0 01-1.414-3.536m0 0l2.829-2.829m-2.829 2.829L3 21M9.172 9.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <h2 class="text-sm font-black text-slate-900 uppercase tracking-tight" x-text="mode === 'online' ? 'Online Mode' : 'Offline Mode'"></h2>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest" x-text="mode === 'online' ? 'Langsung sinkron ke server' : 'Data disimpan di perangkat'"></p>
                    </div>
                </div>
                <div class="flex items-center bg-slate-200 p-1 rounded-xl border border-slate-300">
                    <button @click="setMode('online')" class="px-4 py-2 rounded-lg text-[10px] font-black uppercase transition-all" 
                            :style="mode === 'online' ? 'background-color: #10b981 !important; color: white !important; box-shadow: 0 10px 15px -3px rgba(16, 185, 129, 0.3);' : 'color: #475569;'">Online</button>
                    <button @click="setMode('offline')" class="px-4 py-2 rounded-lg text-[10px] font-black uppercase transition-all" 
                            :style="mode === 'offline' ? 'background-color: #10b981 !important; color: white !important; box-shadow: 0 10px 15px -3px rgba(16, 185, 129, 0.3);' : 'color: #475569;'">Offline</button>
                </div>
            </div>

            <!-- Offline Controls -->
            <div x-show="mode === 'offline'" class="pt-3 border-t border-slate-100 flex gap-3">
                <button @click="downloadData" :disabled="downloading" style="background-color: #10b981 !important;" class="flex-1 py-4 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest hover:brightness-110 transition-all shadow-xl flex items-center justify-center gap-2 border border-emerald-500 disabled:opacity-50">
                    <template x-if="!downloading">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            Unduh Data Tiket
                        </div>
                    </template>
                    <template x-if="downloading">
                        <div class="flex items-center gap-2">
                            <div class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
                            Sedang Mengunduh...
                        </div>
                    </template>
                </button>
                <button @click="syncData" x-show="pendingSync.length > 0" :disabled="syncing" style="background-color: #10b981 !important;" class="flex-1 py-4 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest hover:brightness-110 transition-all shadow-xl flex items-center justify-center gap-2 animate-pulse border border-emerald-500 disabled:opacity-50">
                    <template x-if="!syncing">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                            Sync (<span x-text="pendingSync.length"></span>)
                        </div>
                    </template>
                    <template x-if="syncing">
                        <div class="flex items-center gap-2">
                            <div class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
                            Sinkronisasi...
                        </div>
                    </template>
                </button>
            </div>
        </div>

        <!-- Notification Toast -->
        <template x-if="notification">
            <div class="fixed top-8 left-1/2 -translate-x-1/2 z-[100] w-[90%] max-w-sm">
                <div style="background-color: #111827 !important; border: 4px solid #10b981 !important; color: white !important;" class="p-6 rounded-[2rem] shadow-2xl animate-in slide-in-from-top-full duration-500 flex items-center gap-4">
                    <div style="background-color: #10b981 !important;" class="w-12 h-12 rounded-2xl flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <div>
                        <h4 class="font-black uppercase text-xs tracking-widest text-emerald-400">Berhasil</h4>
                        <p class="text-sm font-bold" x-text="notification.message"></p>
                    </div>
                </div>
            </div>
        </template>

        <!-- Scanner Viewport -->
        <div style="background-color: #000; border: 8px solid white; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5); position: relative; aspect-ratio: 1/1; overflow: hidden; border-radius: 3rem;" class="w-full">
            <div id="reader" style="width: 100%; height: 100%;"></div>
            
            <!-- Ready Indicator -->
            <div x-show="!processing && !result && !cameraError" class="absolute top-6 left-1/2 -translate-x-1/2 z-40">
                <div style="background-color: rgba(0,0,0,0.5); border: 1px solid rgba(255,255,255,0.2);" class="backdrop-blur-md px-4 py-2 rounded-full flex items-center gap-2">
                    <div class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse shadow-[0_0_8px_#10b981]"></div>
                    <span class="text-[10px] font-black text-white uppercase tracking-widest">Ready to Scan</span>
                </div>
            </div>

            <!-- Scanning Overlay -->
            <div class="absolute inset-0 pointer-events-none z-30">
                <div style="border: 40px solid rgba(0,0,0,0.3);" class="w-full h-full relative">
                    <div style="border: 2px solid rgba(79, 70, 229, 0.4);" class="w-full h-full rounded-3xl relative">
                        <div style="background: linear-gradient(to bottom, transparent, #4f46e5); opacity: 0.5;" class="absolute top-0 left-0 w-full h-1 animate-scanner-line shadow-[0_0_15px_#4f46e5]"></div>
                    </div>
                </div>
            </div>

            <!-- Loading Overlay -->
            <div x-show="processing" class="absolute inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50">
                <div class="text-center space-y-4">
                    <div style="border: 4px solid #4f46e5; border-top-color: transparent;" class="w-12 h-12 rounded-full animate-spin mx-auto"></div>
                    <p class="text-white font-bold text-sm tracking-widest uppercase">Memproses...</p>
                </div>
            </div>
        </div>

        <!-- Manual Start / Error Overlay (Moved Outside to be clickable) -->
        <div x-show="cameraError" style="background-color: #0f172a; border: 1px solid #1e293b;" class="rounded-[2.5rem] p-8 text-center space-y-6 shadow-2xl animate-in fade-in zoom-in duration-300">
            <div style="background-color: #fff1f2; color: #e11d48;" class="w-16 h-16 rounded-full flex items-center justify-center mx-auto">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <div class="space-y-2">
                <p class="text-white font-bold text-lg">Kamera Terblokir!</p>
                <p class="text-slate-400 text-sm">Chrome mewajibkan <b>HTTPS</b> untuk akses kamera. Gunakan <b>https://</b> atau gunakan tombol input manual di bawah.</p>
            </div>
            <div class="grid grid-cols-1 gap-4">
                <button @click="startScanner" style="background-color: #10b981 !important; cursor: pointer !important; pointer-events: auto !important;" class="w-full py-5 text-white rounded-2xl font-black uppercase tracking-widest shadow-lg hover:brightness-110 active:scale-95 transition-all">Coba Aktifkan Lagi</button>
                <button @click="showManualInput = true" style="background-color: #4f46e5 !important; cursor: pointer !important; pointer-events: auto !important;" class="w-full py-5 text-white rounded-2xl font-black uppercase tracking-widest shadow-lg hover:brightness-110 active:scale-95 transition-all">Input Kode Manual</button>
            </div>
        </div>

        <!-- Camera for Photo Capture (Hidden) -->
        <video id="photo-camera" class="hidden"></video>
        <canvas id="photo-canvas" class="hidden"></canvas>

        <template x-if="result">
            <div 
                class="rounded-[2rem] p-8 shadow-2xl animate-in zoom-in duration-300"
                :style="result.success 
                    ? 'background-color: white; border: 4px solid #10b981; box-shadow: 0 25px 50px -12px rgba(16, 185, 129, 0.2);' 
                    : 'background-color: white; border: 4px solid #ef4444; box-shadow: 0 25px 50px -12px rgba(239, 68, 68, 0.2);'"
            >
                <div class="text-center space-y-6">
                    <div class="w-20 h-20 rounded-full flex items-center justify-center mx-auto shadow-lg"
                         :style="result.success ? 'background-color: #10b981; color: white;' : 'background-color: #ef4444; color: white;'">
                        <svg x-show="result.success" class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                        <svg x-show="!result.success" class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/></svg>
                    </div>

                    <div class="space-y-1">
                        <h3 class="text-2xl font-black font-outfit uppercase tracking-tight" 
                            :style="result.success ? 'color: #059669;' : 'color: #dc2626;'"
                            x-text="result.success ? 'Berhasil!' : 'Gagal!'"></h3>
                        <p style="color: #64748b;" class="font-bold" x-text="result.message"></p>
                    </div>

                    <div x-show="result.success" style="background-color: #f8fafc; border: 1px solid #e2e8f0;" class="rounded-2xl p-6 text-left space-y-3">
                        <div style="border-bottom: 1px solid #e2e8f0;" class="flex justify-between items-center pb-3">
                            <span style="color: #94a3b8;" class="text-[10px] font-black uppercase">Nama Pembeli</span>
                            <span style="color: #0f172a;" class="text-sm font-black" x-text="result.customer_name"></span>
                        </div>
                        <div style="border-bottom: 1px solid #e2e8f0;" class="flex justify-between items-center pb-3">
                            <span style="color: #94a3b8;" class="text-[10px] font-black uppercase">Kategori</span>
                            <span style="background-color: #4f46e5; color: white;" class="px-2 py-1 text-[10px] font-black rounded" x-text="result.category_name"></span>
                        </div>
                        <div class="mt-4">
                            <span style="color: #94a3b8;" class="text-[10px] font-black uppercase block mb-2 text-center">Foto Pengunjung</span>
                            <img :src="result.photo_url" style="border: 2px solid white;" class="w-full aspect-video object-cover rounded-xl shadow-md">
                        </div>
                    </div>

                    <div x-show="!result.success && result.reason === 'already_redeemed'" style="background-color: #fff1f2; border: 1px solid #fecdd3;" class="rounded-2xl p-6 text-left space-y-4">
                        <div class="text-center">
                            <p style="color: #f43f5e;" class="text-[10px] font-black uppercase mb-2">Data Redeem Sebelumnya</p>
                            <p style="color: #881337;" class="text-sm font-bold" x-text="result.customer"></p>
                            <p style="color: #fb7185;" class="text-xs" x-text="result.redeemed_at"></p>
                        </div>
                        <img x-show="result.redeem_photo" :src="result.redeem_photo" style="border: 2px solid white;" class="w-full aspect-video object-cover rounded-xl shadow-md grayscale">
                    </div>

                    <div x-show="!result.success && result.reason === 'invalid'" style="background-color: #fff1f2; border: 1px solid #fecdd3;" class="rounded-2xl p-6 text-center space-y-2">
                        <svg class="w-12 h-12 text-rose-500 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <p style="color: #881337;" class="text-sm font-bold uppercase tracking-tight">E-Voucher Not Valid</p>
                        <p style="color: #fb7185;" class="text-xs">Data tidak ditemukan di database</p>
                    </div>

                    <button @click="resetScanner" style="background-color: #10b981 !important;" class="w-full py-5 text-white rounded-2xl font-black uppercase tracking-widest hover:brightness-110 transition-all shadow-2xl flex items-center justify-center gap-3 border border-emerald-500 relative overflow-hidden">
                        <span x-text="result.success ? 'Scan Berikutnya' : 'Coba Lagi'"></span>
                        <span x-show="autoResetTimer > 0" class="text-[10px] opacity-70" x-text="'(' + autoResetTimer + 's)'"></span>
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        
                        <!-- Progress bar for auto reset -->
                        <div x-show="autoResetTimer > 0" class="absolute bottom-0 left-0 h-1 bg-white/30" :style="'width: ' + (autoResetTimer/4 * 100) + '%'"></div>
                    </button>
                </div>
            </div>
        </template>

        <!-- Manual Input Modal (Premium Redesign) -->
        <template x-if="showManualInput">
            <div style="position: fixed; inset: 0; background-color: rgba(15, 23, 42, 0.9); backdrop-filter: blur(12px); z-index: 9999; display: flex !important; align-items: center !important; justify-content: center !important; padding: 20px;"
                 x-effect="if(showManualInput) { setTimeout(() => $refs.manualInput.focus(), 100) }">
                
                <div style="background-color: white; width: 100%; max-width: 420px; border-radius: 3rem; padding: 40px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5); border: 2px solid #f1f5f9;" 
                     class="animate-in zoom-in duration-300">
                    
                    <div class="text-center mb-8">
                        <div style="background-color: #f1f5f9; color: #4f46e5;" class="w-16 h-16 rounded-3xl flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </div>
                        <h3 style="color: #0f172a;" class="text-2xl font-black uppercase tracking-tight">Input Manual</h3>
                        <p style="color: #64748b;" class="text-xs font-bold uppercase tracking-widest mt-1">Ketik kode atau scan barcode fisik</p>
                    </div>
                    
                    <div class="space-y-6">
                        <div class="relative">
                            <input 
                                type="text" 
                                x-model="manualCode" 
                                x-ref="manualInput"
                                @input="if(manualCode.length >= 10) processManual()"
                                @keyup.enter="processManual"
                                style="background-color: #f8fafc; border: 3px solid #f1f5f9; color: #0f172a; font-family: monospace;"
                                class="w-full rounded-[1.5rem] p-6 text-center text-3xl font-black uppercase tracking-[0.2em] focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none"
                                placeholder="........"
                            >
                            <div x-show="manualCode.length > 0" class="absolute right-4 top-1/2 -translate-y-1/2">
                                <span style="background-color: #f1f5f9; color: #94a3b8;" class="px-2 py-1 rounded text-[10px] font-bold" x-text="manualCode.length"></span>
                            </div>
                        </div>

                        <div style="display: flex !important; gap: 16px; align-items: center;">
                            <button @click="showManualInput = false" 
                                    style="cursor: pointer !important; color: #94a3b8; background: none; border: none;" 
                                    class="flex-1 py-4 font-black uppercase text-xs tracking-widest hover:text-slate-600 transition-colors">
                                BATAL
                            </button>
                            <button @click="processManual" 
                                    style="background-color: #10b981 !important; cursor: pointer !important; color: white !important; border: none; box-shadow: 0 10px 20px -5px rgba(16, 185, 129, 0.4);" 
                                    class="flex-[2] py-5 rounded-2xl font-black uppercase tracking-[0.2em] hover:brightness-110 active:scale-95 transition-all flex items-center justify-center gap-2">
                                PROSES
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </button>
                        </div>
                    </div>

                    <p class="text-center text-[10px] font-bold text-slate-400 uppercase tracking-tighter mt-8">
                        Tips: Tekan <span class="bg-slate-100 px-1 rounded text-slate-600">ENTER</span> atau masukkan 10 karakter untuk auto-send
                    </p>
                </div>
            </div>
        </template>

        <!-- Sounds -->
        <audio id="sound-success" src="https://assets.mixkit.co/active_storage/sfx/2013/2013-preview.mp3"></audio>
        <audio id="sound-fail" src="https://assets.mixkit.co/active_storage/sfx/2019/2019-preview.mp3"></audio>
    </div>

    <script src="https://unpkg.com/html5-qrcode"></script>
    <script>
        function redeemScanner() {
            return {
                html5QrCode: null,
                processing: false,
                result: null,
                downloading: false,
                syncing: false,
                notification: null,
                autoResetTimer: 0,
                autoResetInterval: null,
                cameraError: false,
                showManualInput: false,
                manualCode: '',
                mode: localStorage.getItem('redeem_mode_{{ $event->id }}') || 'online',
                offlineTickets: JSON.parse(localStorage.getItem('offline_tickets_{{ $event->id }}')) || [],
                pendingSync: JSON.parse(localStorage.getItem('pending_sync_{{ $event->id }}')) || [],
                videoStream: null,

                init() {
                    this.startScanner();
                    this.initCamera();
                },

                showNotification(message) {
                    this.notification = { message: message };
                    setTimeout(() => {
                        this.notification = null;
                    }, 3000);
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
                        console.error("Camera error:", err);
                    }
                },

                startScanner() {
                    this.cameraError = false;
                    this.html5QrCode = new Html5Qrcode("reader");
                    const config = { fps: 10, qrbox: { width: 250, height: 250 } };
                    this.html5QrCode.start(
                        { facingMode: "environment" }, 
                        config, 
                        (text) => this.onScanSuccess(text)
                    ).catch(err => {
                        console.error(err);
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
                        this.showNotification("Data tiket (" + data.tickets.length + ") berhasil diunduh!");
                        this.playSuccessSound();
                    } catch (err) {
                        alert("Gagal mengunduh data!");
                    } finally {
                        this.downloading = false;
                    }
                },

                async onScanSuccess(decodedText) {
                    if (this.processing || this.result || this.downloading || this.syncing) return;
                    this.processing = true;
                    
                    // Pause only if scanner is actually running
                    if (this.html5QrCode && this.html5QrCode.getState() === 2) {
                        try { this.html5QrCode.pause(); } catch(e) {}
                    }
                    
                    const photo = this.takePhoto();

                    try {
                        if (this.mode === 'online') {
                            await this.processOnline(decodedText, photo);
                        } else {
                            await this.processOffline(decodedText, photo);
                        }
                    } catch (err) {
                        console.error(err);
                    } finally {
                        this.processing = false;
                    }

                    // Start Auto Reset Countdown
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
                        this.result = { success: false, reason: 'invalid', message: 'E-Voucher Tidak Valid (Offline)' };
                    } else {
                        const ticket = this.offlineTickets[ticketIndex];
                        if (ticket.status === 'redeemed') {
                            this.result = { 
                                success: false, 
                                reason: 'already_redeemed', 
                                message: 'Sudah pernah di-redeem (Offline)!',
                                customer: ticket.customer,
                                redeemed_at: ticket.redeemed_at,
                                redeem_photo: ticket.redeem_photo
                            };
                        } else {
                            // Success Offline
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
                    
                    let successCount = 0;
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
                                successCount++;
                                this.pendingSync = this.pendingSync.filter(i => i.ticket_code !== item.ticket_code);
                                localStorage.setItem('pending_sync_{{ $event->id }}', JSON.stringify(this.pendingSync));
                            }
                        } catch (err) {
                            console.error("Sync failed for", item.ticket_code);
                        }
                    }
                    
                    if (successCount > 0) {
                        this.showNotification(successCount + " data berhasil disinkronisasi!");
                        this.playSuccessSound();
                    }
                    this.syncing = false;
                },

                playResultSound() {
                    const sound = document.getElementById(this.result.success ? 'sound-success' : 'sound-fail');
                    sound.volume = 1.0;
                    sound.currentTime = 0;
                    sound.play();
                },

                playSuccessSound() {
                    const sound = document.getElementById('sound-success');
                    sound.volume = 1.0;
                    sound.currentTime = 0;
                    sound.play();
                },

                processManual() {
                    if (!this.manualCode) return;
                    const code = this.manualCode.trim().toUpperCase();
                    this.showManualInput = false;
                    this.manualCode = '';
                    this.onScanSuccess(code);
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
                        return null; // Fallback if camera not ready
                    }
                },

                resetScanner() {
                    clearInterval(this.autoResetInterval);
                    this.autoResetTimer = 0;
                    this.result = null;
                    this.html5QrCode.resume();
                }
            }
        }
    </script>

    <style>
        @keyframes scanner-line { 0% { top: 0; } 100% { top: 100%; } }
        .animate-scanner-line { animation: scanner-line 2s linear infinite; }
        #reader { border: none !important; }
        #reader video { object-fit: cover !important; border-radius: 2rem; }
        #reader__dashboard { display: none !important; }
    </style>
</x-app-layout>
