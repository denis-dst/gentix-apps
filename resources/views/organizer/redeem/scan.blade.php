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

            <div class="flex items-center gap-2 sm:gap-3">
                <!-- Sync Badge (Offline Only) -->
                <div x-show="mode === 'offline' && pendingSync.length > 0" class="flex items-center gap-2 bg-amber-500/10 border border-amber-500/20 px-3 py-1.5 rounded-xl animate-pulse">
                    <div class="w-1.5 h-1.5 bg-amber-500 rounded-full"></div>
                    <span class="text-[9px] font-black text-amber-500 uppercase tracking-wider" x-text="pendingSync.length + ' Pending'"></span>
                </div>

                <!-- Input Type Switcher -->
                <div class="flex bg-black p-1 rounded-xl border border-white/5">
                    <button @click="setInputType('camera')"
                        :class="inputType === 'camera' ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-600/20' : 'text-slate-500 hover:text-slate-300'"
                        class="p-1.5 sm:p-2 rounded-lg transition-all" title="Kamera Standby">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </button>
                    <button @click="setInputType('auto')"
                        :class="inputType === 'auto' ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-600/20' : 'text-slate-500 hover:text-slate-300'"
                        class="p-1.5 sm:p-2 rounded-lg transition-all" title="Auto / IR Scanner">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                        </svg>
                    </button>
                    <button @click="setInputType('manual')"
                        :class="inputType === 'manual' ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-600/20' : 'text-slate-500 hover:text-slate-300'"
                        class="p-1.5 sm:p-2 rounded-lg transition-all" title="Manual Input">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                    </button>
                </div>

                <!-- Mode Switcher (Online/Offline) -->
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
                    <div class="absolute inset-0 opacity-20 transition-all duration-500 pointer-events-none"
                         :class="{
                            'bg-emerald-500': result && result.success,
                            'bg-rose-500': result && !result.success,
                            'bg-slate-900': !result
                         }">
                    </div>

                    <!-- IR Hidden Input -->
                    <input type="text" x-ref="ticketInput" @keydown.enter="processScannedCode()"
                        class="fixed opacity-0 pointer-events-none -left-[9999px]" 
                        x-model="scannedCode" autocomplete="off"
                        x-show="inputType === 'auto'">

                    <!-- Camera Container & Tap-to-Refocus -->
                    <div x-show="inputType === 'camera'" 
                        class="absolute inset-0 w-full h-full bg-black overflow-hidden select-none cursor-pointer"
                        @click="triggerRefocus($event)">
                        
                        <div id="reader" class="w-full h-full relative"></div>

                        <!-- Tap to Focus Reticle -->
                        <div x-show="focusRing.visible" x-cloak
                            class="absolute pointer-events-none transition-all duration-200 transform -translate-x-1/2 -translate-y-1/2 z-30"
                            :style="`left: ${focusRing.x}px; top: ${focusRing.y}px;`">
                            <div class="w-16 h-16 border-2 border-emerald-400 rounded-2xl animate-ping duration-500 opacity-60"></div>
                            <div class="w-16 h-16 border-2 border-emerald-400 rounded-2xl -mt-16 shadow-[0_0_15px_rgba(52,211,153,0.9)] flex items-center justify-center">
                                <div class="w-1.5 h-1.5 bg-emerald-400 rounded-full"></div>
                            </div>
                        </div>

                        <!-- Floating Camera Toolbar (Top) -->
                        <div x-show="cameraStarted && !result" 
                            class="absolute top-4 left-4 right-4 z-40 flex items-center justify-between pointer-events-auto"
                            @click.stop>
                            
                            <!-- Switch Camera Button -->
                            <div class="flex items-center gap-2">
                                <button type="button" @click="switchCamera()" 
                                    x-show="cameraDevices && cameraDevices.length > 1"
                                    class="flex items-center gap-2 bg-black/70 hover:bg-black/90 backdrop-blur-md px-3 py-2 rounded-xl border border-white/15 text-white text-xs font-bold transition shadow-lg active:scale-95"
                                    title="Ganti Lensa Kamera">
                                    <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                    </svg>
                                    <span class="text-[10px] font-bold text-slate-200" x-text="getCameraShortName()"></span>
                                </button>
                                
                                <div x-show="!cameraDevices || cameraDevices.length <= 1"
                                    class="bg-black/70 backdrop-blur-md px-3 py-2 rounded-xl border border-white/10 text-[10px] font-bold text-slate-300 flex items-center gap-1.5">
                                    <span class="w-2 h-2 rounded-full bg-emerald-500 inline-block animate-pulse"></span>
                                    <span x-text="getCameraShortName()"></span>
                                </div>
                            </div>

                            <!-- Right Controls: Zoom & Torch -->
                            <div class="flex items-center gap-2">
                                <!-- Zoom Toggle -->
                                <template x-if="hasZoom && maxZoom > 1">
                                    <div class="flex bg-black/70 backdrop-blur-md p-1 rounded-xl border border-white/15">
                                        <button type="button" @click="setZoom(1)" 
                                            :class="currentZoom <= 1.2 ? 'bg-emerald-600 text-white' : 'text-slate-400 hover:text-white'"
                                            class="px-2.5 py-1 rounded-lg text-[9px] font-black transition">1x</button>
                                        <button type="button" @click="setZoom(2)" 
                                            :class="currentZoom > 1.2 ? 'bg-emerald-600 text-white' : 'text-slate-400 hover:text-white'"
                                            class="px-2.5 py-1 rounded-lg text-[9px] font-black transition">2x</button>
                                    </div>
                                </template>

                                <!-- Torch Toggle -->
                                <button type="button" @click="toggleTorch()" 
                                    x-show="hasTorch"
                                    :class="torchOn ? 'bg-amber-500 text-black border-amber-400 shadow-amber-500/40' : 'bg-black/70 text-slate-300 border-white/15 hover:bg-black/90'"
                                    class="flex items-center gap-1.5 px-3 py-2 rounded-xl border backdrop-blur-md text-xs font-bold transition shadow-lg active:scale-95"
                                    title="Nyalakan/Matikan Flashlight">
                                    <svg class="w-4 h-4" :class="torchOn ? 'text-black' : 'text-amber-400'" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/>
                                    </svg>
                                    <span class="text-[10px] font-black" x-text="torchOn ? 'FLASH ON' : 'FLASH'"></span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Camera Error / Retry Modal -->
                    <div x-show="cameraError" x-cloak class="absolute inset-0 flex items-center justify-center z-40 pointer-events-auto p-4 bg-black/90">
                        <div class="bg-slate-900 p-6 sm:p-8 rounded-3xl border border-white/10 text-center max-w-md w-full shadow-2xl backdrop-blur-xl">
                            <div class="w-12 h-12 rounded-full bg-rose-500/10 text-rose-500 flex items-center justify-center mx-auto mb-4 border border-rose-500/20">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                            </div>
                            <div class="text-base font-black text-white uppercase tracking-wider">Kamera Bermasalah</div>
                            <p class="text-xs text-slate-300 mt-2 leading-relaxed" x-text="cameraErrorMessage || 'Periksa izin kamera pada browser atau pilih lensa kamera lain.'"></p>

                            <template x-if="cameraDevices && cameraDevices.length">
                                <div class="mt-5 text-left">
                                    <label class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Pilih Lensa Kamera</label>
                                    <select x-model="selectedCameraId" class="w-full mt-1.5 p-2.5 rounded-xl bg-white/5 border border-white/10 text-xs text-white focus:ring-emerald-500 focus:border-emerald-500">
                                        <template x-for="dev in cameraDevices" :key="dev.id">
                                            <option :value="dev.id" x-text="dev.label || dev.id" class="bg-slate-900 text-white"></option>
                                        </template>
                                    </select>
                                </div>
                            </template>

                            <div class="mt-6 flex flex-col sm:flex-row gap-2.5 justify-center">
                                <button @click="startCamera(true)" class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition">Coba Kamera Utama</button>
                                <button @click="switchCamera(selectedCameraId)" x-show="selectedCameraId" class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold transition">Pakai Lensa Terpilih</button>
                                <button @click="setInputType('manual')" class="px-4 py-2.5 bg-white/10 hover:bg-white/20 text-white rounded-xl text-xs font-bold transition">Gunakan Manual</button>
                            </div>
                        </div>
                    </div>

                    <!-- Manual Entry Card -->
                    <div x-show="inputType === 'manual' && !result"
                        class="z-20 w-full max-w-sm p-6 animate-in zoom-in duration-300">
                        <div class="bg-slate-900/90 backdrop-blur-xl border border-white/10 p-8 rounded-[2.5rem] shadow-2xl">
                            <h3 class="text-[10px] font-black text-center uppercase tracking-[0.4em] text-emerald-400 mb-8">Manual Entry</h3>
                            <input type="text" x-model="manualCode" placeholder="KODE TIKET"
                                @keydown.enter="processManualScan()"
                                class="w-full bg-white/5 border-2 border-white/10 rounded-2xl px-6 py-5 text-2xl font-black text-center tracking-[0.2em] text-white focus:border-emerald-500 focus:ring-0 mb-6 uppercase placeholder:text-white/10">
                            <button @click="processManualScan()"
                                class="w-full py-5 bg-emerald-600 rounded-2xl font-black uppercase tracking-widest text-sm hover:bg-emerald-700 transition shadow-lg shadow-emerald-600/20 active:scale-[0.98]">
                                Validasi Tiket
                            </button>
                        </div>
                    </div>

                    <!-- Idle Overlay (Auto Scan Mode) -->
                    <div x-show="!processing && !result && !cameraError && inputType === 'auto'"
                        class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none z-30">
                        <div class="relative group">
                            <div class="absolute inset-0 rounded-full bg-emerald-500/20 animate-ping duration-1000"></div>
                            <div class="absolute -inset-8 rounded-full bg-emerald-500/5 animate-pulse duration-700"></div>
                            <div class="w-56 h-56 sm:w-80 sm:h-80 border-2 border-white/10 rounded-[3rem] relative bg-white/5 backdrop-blur-sm shadow-2xl shadow-emerald-500/10">
                                <div class="absolute top-0 left-0 w-16 h-16 border-t-4 border-l-4 border-emerald-500 rounded-tl-[2.5rem]"></div>
                                <div class="absolute top-0 right-0 w-16 h-16 border-t-4 border-r-4 border-emerald-500 rounded-tr-[2.5rem]"></div>
                                <div class="absolute bottom-0 left-0 w-16 h-16 border-b-4 border-l-4 border-emerald-500 rounded-bl-[2.5rem]"></div>
                                <div class="absolute bottom-0 right-0 w-16 h-16 border-b-4 border-r-4 border-emerald-500 rounded-br-[2.5rem]"></div>
                                <div class="absolute top-1/2 left-6 right-6 h-[2px] bg-emerald-500 shadow-[0_0_15px_rgba(16,185,129,0.8)] blur-[0.5px] animate-scan-line"></div>
                            </div>
                        </div>
                        <div class="mt-12 text-center">
                            <p class="text-xs sm:text-sm font-black uppercase tracking-[0.6em] text-white/60 animate-pulse">Ready to Redeem</p>
                            <p class="mt-2 text-[8px] font-bold text-slate-500 uppercase tracking-widest opacity-50">Connect Infrared Scanner or Type Code</p>
                        </div>
                    </div>

                    <!-- Idle Overlay (Camera Mode Target Reticle) -->
                    <div x-show="!processing && !result && !cameraError && inputType === 'camera'"
                        class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none z-20">
                        <div class="w-64 h-64 sm:w-80 sm:h-80 border border-white/20 rounded-[2.5rem] relative">
                            <div class="absolute top-0 left-0 w-8 h-8 border-t-4 border-l-4 border-emerald-400 rounded-tl-2xl"></div>
                            <div class="absolute top-0 right-0 w-8 h-8 border-t-4 border-r-4 border-emerald-400 rounded-tr-2xl"></div>
                            <div class="absolute bottom-0 left-0 w-8 h-8 border-b-4 border-l-4 border-emerald-400 rounded-bl-2xl"></div>
                            <div class="absolute bottom-0 right-0 w-8 h-8 border-b-4 border-r-4 border-emerald-400 rounded-br-2xl"></div>
                            <div class="absolute top-1/2 left-6 right-6 h-[2px] bg-emerald-400/80 shadow-[0_0_12px_rgba(52,211,153,0.9)] animate-scan-line"></div>
                        </div>
                        <div class="mt-8 text-center bg-black/60 backdrop-blur-md px-6 py-2 rounded-full border border-white/10">
                            <p class="text-[11px] font-black uppercase tracking-[0.3em] text-white">Standby Scanner</p>
                            <p class="text-[8px] font-bold text-emerald-400 uppercase tracking-widest mt-0.5">Arahkan Kamera ke QR Tiket</p>
                        </div>
                    </div>

                    <!-- Feedback Overlays (Results) -->
                    <div class="absolute inset-0 z-50 pointer-events-auto" x-show="result" x-cloak @click="resetScanner()">
                        <!-- Success Overlay (Centered Elevated Photo Layout) -->
                        <template x-if="result && result.success">
                            <div class="bg-[#044e39]/95 backdrop-blur-md fixed inset-0 flex flex-col items-center justify-center p-4 sm:p-6 animate-in fade-in duration-200 cursor-pointer select-none">
                                <!-- Top Status Badge -->
                                <div class="inline-flex items-center gap-2 px-5 py-2 rounded-full bg-emerald-400/20 text-emerald-200 border border-emerald-400/30 font-black text-xs sm:text-sm uppercase tracking-widest mb-3 backdrop-blur-md shadow-lg shadow-emerald-950/40 animate-bounce">
                                    <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                    <span>REDEEM BERHASIL</span>
                                </div>

                                <!-- Customer Name & Category -->
                                <h3 class="text-2xl sm:text-4xl font-black text-white uppercase tracking-tight text-center max-w-lg font-outfit" x-text="result.customer_name"></h3>
                                <div class="mt-1 px-4 py-1 bg-white text-emerald-950 rounded-full font-black text-[11px] sm:text-xs uppercase tracking-wider shadow" x-text="result.category_name"></div>

                                <!-- Center Photo Verification (Elevated Center) -->
                                <div class="mt-4 sm:mt-5 w-full max-w-[280px] sm:max-w-[340px] aspect-[4/3] bg-black/50 rounded-3xl border-2 border-emerald-400/40 overflow-hidden shadow-2xl shadow-black/80 relative">
                                    <img :src="result.photo_url" class="w-full h-full object-cover">
                                    <div class="absolute bottom-2 left-2 right-2 px-3 py-1.5 bg-black/70 backdrop-blur-md rounded-xl text-[10px] font-bold text-slate-200 flex items-center justify-between border border-white/10">
                                        <span>FOTO REDEEM</span>
                                        <span class="text-emerald-400 font-black">✓ TERSIMPAN</span>
                                    </div>
                                </div>

                                <!-- Auto-reset countdown & dismiss guide -->
                                <div class="mt-4 sm:mt-5 flex flex-col items-center gap-1.5 text-center">
                                    <div class="px-4 py-1.5 bg-black/40 backdrop-blur-md rounded-full border border-white/10 text-[10px] font-bold text-emerald-200 flex items-center gap-2">
                                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                                        <span>Lanjut scan dalam <span x-text="autoResetTimer" class="font-black text-white"></span>s</span>
                                    </div>
                                    <p class="text-[9px] font-semibold text-white/60 tracking-wider">Ketuk layar atau tekan Spasi untuk lanjut scan</p>
                                </div>
                            </div>
                        </template>

                        <!-- Error Overlay (Centered Elevated Photo Layout) -->
                        <template x-if="result && !result.success">
                            <div class="bg-[#881337]/95 backdrop-blur-md fixed inset-0 flex flex-col items-center justify-center p-4 sm:p-6 animate-in shake duration-300 cursor-pointer select-none">
                                <!-- Top Status Badge -->
                                <div class="inline-flex items-center gap-2 px-5 py-2 rounded-full bg-rose-400/20 text-rose-200 border border-rose-400/30 font-black text-xs sm:text-sm uppercase tracking-widest mb-3 backdrop-blur-md shadow-lg">
                                    <svg class="w-4 h-4 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/></svg>
                                    <span x-text="result.message || 'REDEEM GAGAL'"></span>
                                </div>

                                <p class="text-white/80 font-bold uppercase tracking-wider text-xs sm:text-sm mb-3 text-center" x-text="result.sub_message || ''"></p>

                                <!-- Visitor Info Box -->
                                <div class="bg-black/50 px-6 py-3 rounded-2xl border border-white/15 text-center max-w-sm w-full mb-3" x-show="result.details">
                                    <div class="text-[10px] text-white/50 uppercase tracking-widest">Data Tiket</div>
                                    <div class="text-base sm:text-lg font-black text-white" x-text="result.details ? result.details.customer : ''"></div>
                                    <div class="text-xs text-emerald-400 font-bold" x-text="result.details ? result.details.category : ''"></div>
                                </div>

                                <!-- Previous Redeem Photo if already redeemed (Center Elevated) -->
                                <template x-if="result.details && result.details.photo">
                                    <div class="w-full max-w-[260px] sm:max-w-[300px] aspect-[4/3] bg-black/50 rounded-2xl border-2 border-rose-400/40 overflow-hidden shadow-2xl relative mb-3">
                                        <img :src="result.details.photo" class="w-full h-full object-cover">
                                        <div class="absolute bottom-2 left-2 right-2 px-2.5 py-1 bg-black/75 backdrop-blur-md rounded-lg text-[9px] font-bold text-rose-200 flex items-center justify-between">
                                            <span>FOTO SEBELUMNYA</span>
                                            <span class="text-rose-400">SUDAH REDEEM</span>
                                        </div>
                                    </div>
                                </template>

                                <!-- Redeem metadata -->
                                <div class="flex flex-col items-center gap-1 text-center" x-show="result.details">
                                    <p class="text-white/70 font-bold uppercase tracking-wider text-xs" x-text="result.details && result.details.redeemed_at ? 'Redeem pada: ' + result.details.redeemed_at : ''"></p>
                                    <p class="text-white/40 font-bold uppercase tracking-wider text-[10px]" x-text="result.details && result.details.redeemed_by ? 'Oleh: ' + result.details.redeemed_by : ''"></p>
                                </div>

                                <!-- Auto-reset countdown & dismiss guide -->
                                <div class="mt-4 flex flex-col items-center gap-1.5 text-center">
                                    <div class="px-4 py-1.5 bg-black/40 backdrop-blur-md rounded-full border border-white/10 text-[10px] font-bold text-rose-200 flex items-center gap-2">
                                        <span>Reset otomatis dalam <span x-text="autoResetTimer" class="font-black text-white"></span>s</span>
                                    </div>
                                    <p class="text-[9px] font-semibold text-white/60 tracking-wider">Ketuk layar atau tekan Spasi untuk coba lagi</p>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- Processing Overlay -->
                    <div x-show="processing" class="bg-black/90 fixed inset-0 flex flex-col items-center justify-center backdrop-blur-xl z-50">
                        <div class="w-16 h-16 border-4 border-white/10 border-t-emerald-500 rounded-full animate-spin mb-4"></div>
                        <p class="text-xs font-black uppercase tracking-[0.4em] text-emerald-400">Verifikasi Tiket...</p>
                    </div>
                </div>

                <!-- Hidden Canvas for Photo Capture Snapshot -->
                <canvas id="photo-canvas" class="fixed opacity-0 pointer-events-none -left-[9999px]"></canvas>
            </div>

            <!-- Side Controls (Offline Management) -->
            <aside x-show="mode === 'offline'" class="w-full lg:w-96 bg-[#010409] border-l border-white/5 flex flex-col shrink-0 h-full overflow-hidden z-40">
                <div class="h-16 px-6 bg-[#0d1117] border-b border-white/5 flex items-center justify-between shrink-0">
                    <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em]">Offline Data</h4>
                    <span class="bg-emerald-500/10 text-emerald-400 px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-wider" x-text="offlineTickets.length + ' Tiket Ready'"></span>
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
                cameraStarted: false,
                cameraError: false,
                cameraErrorMessage: '',
                isStartingCamera: false,
                cameraDevices: [],
                selectedCameraId: null,
                hasTorch: false,
                torchOn: false,
                hasZoom: false,
                currentZoom: 1,
                minZoom: 1,
                maxZoom: 1,
                focusRing: { visible: false, x: 0, y: 0, timer: null },
                inputType: 'camera',
                scannedCode: '',
                manualCode: '',
                lastScannedCode: '',
                lastScanTime: 0,
                autoResetTimer: 0,
                autoResetInterval: null,
                mode: localStorage.getItem('redeem_mode_{{ $event->id }}') || 'online',
                offlineTickets: JSON.parse(localStorage.getItem('offline_tickets_{{ $event->id }}')) || [],
                pendingSync: JSON.parse(localStorage.getItem('pending_sync_{{ $event->id }}')) || [],

                init() {
                    this.$nextTick(() => {
                        if (this.inputType === 'camera') {
                            setTimeout(() => this.startCamera(true), 250);
                        } else {
                            this.focusInput();
                        }
                    });

                    document.addEventListener('click', () => { 
                        if (this.inputType === 'auto') this.focusInput(); 
                    });
                    setInterval(() => { 
                        if (this.inputType === 'auto' && !this.result && !this.processing) this.focusInput(); 
                    }, 1000);

                    // Dismiss by Space, Enter, or Escape key
                    window.addEventListener('keydown', (e) => {
                        if ((e.key === ' ' || e.key === 'Enter' || e.key === 'Escape') && this.result) {
                            e.preventDefault();
                            this.resetScanner();
                        }
                    });
                },

                setMode(newMode) {
                    this.mode = newMode;
                    localStorage.setItem('redeem_mode_{{ $event->id }}', newMode);
                },

                setInputType(val) {
                    if (this.inputType === 'camera' && val !== 'camera') {
                        this.stopCamera();
                    }
                    this.inputType = val;
                    if (val === 'camera') {
                        this.result = null;
                        setTimeout(() => this.startCamera(true), 250);
                    }
                    if (val === 'auto') {
                        this.$nextTick(() => this.focusInput());
                    }
                },

                focusInput() { 
                    if (this.$refs.ticketInput) this.$refs.ticketInput.focus(); 
                },

                processScannedCode() {
                    const code = this.scannedCode.trim();
                    if (code) {
                        this.scannedCode = '';
                        this.onScanSuccess(code);
                    }
                },

                processManualScan() {
                    const code = this.manualCode.trim();
                    if (code) {
                        this.manualCode = '';
                        this.onScanSuccess(code);
                    }
                },

                async startCamera(preferFacingMode = true) {
                    if (this.isStartingCamera) return;
                    this.isStartingCamera = true;
                    this.cameraError = false;
                    this.cameraErrorMessage = '';

                    if (this.html5QrCode) {
                        await this.stopCamera();
                    }

                    this.html5QrCode = new Html5Qrcode("reader");

                    const config = {
                        fps: 15,
                        qrbox: (viewfinderWidth, viewfinderHeight) => {
                            const minEdge = Math.min(viewfinderWidth, viewfinderHeight);
                            const size = Math.max(220, Math.floor(minEdge * 0.75));
                            return { width: Math.min(size, 380), height: Math.min(size, 380) };
                        },
                        experimentalFeatures: {
                            useBarCodeDetectorIfSupported: true
                        }
                    };

                    // Enumerate available cameras in background
                    Html5Qrcode.getCameras().then(devices => {
                        this.cameraDevices = devices || [];
                        if (!this.selectedCameraId && devices && devices.length > 0) {
                            const mainBack = devices.find(d => /back|rear|environment/i.test(d.label) && !/wide|ultra|0\.5|tele|macro/i.test(d.label));
                            const anyBack = devices.find(d => /back|rear|environment/i.test(d.label));
                            this.selectedCameraId = (mainBack || anyBack || devices[0]).id;
                        }
                    }).catch(e => {
                        console.warn('getCameras warning:', e);
                    });

                    const onScanSuccess = (text) => {
                        if (!this.processing && !this.result) {
                            const now = Date.now();
                            // Debounce duplicate scans within 2.5 seconds
                            if (text === this.lastScannedCode && (now - this.lastScanTime) < 2500) {
                                return;
                            }
                            this.lastScanTime = now;
                            this.lastScannedCode = text;
                            this.onScanSuccess(text);
                        }
                    };

                    const onScanFailure = () => {};

                    let cameraConstraint = { facingMode: "environment" };
                    if (!preferFacingMode && this.selectedCameraId) {
                        cameraConstraint = { deviceId: { exact: this.selectedCameraId } };
                    }

                    try {
                        await this.html5QrCode.start(cameraConstraint, config, onScanSuccess, onScanFailure);
                        this.cameraStarted = true;
                        this.cameraError = false;
                        this.isStartingCamera = false;
                        setTimeout(() => this.updateCameraCapabilities(), 500);
                    } catch (err) {
                        console.warn("Camera start with facingMode failed, falling back to deviceId:", err);
                        try {
                            const devices = await Html5Qrcode.getCameras();
                            this.cameraDevices = devices || [];
                            if (devices && devices.length > 0) {
                                const mainBack = devices.find(d => /back|rear|environment/i.test(d.label) && !/wide|ultra|0\.5|tele|macro/i.test(d.label));
                                const chosenId = (mainBack || devices[0]).id;
                                this.selectedCameraId = chosenId;
                                await this.html5QrCode.start({ deviceId: { exact: chosenId } }, config, onScanSuccess, onScanFailure);
                                this.cameraStarted = true;
                                this.cameraError = false;
                                this.isStartingCamera = false;
                                setTimeout(() => this.updateCameraCapabilities(), 500);
                                return;
                            }
                        } catch (fallbackErr) {
                            console.error("Camera fallback failed:", fallbackErr);
                        }
                        this.cameraError = true;
                        this.cameraErrorMessage = err?.message || 'Gagal mengakses kamera. Izinkan akses kamera pada browser.';
                        this.cameraStarted = false;
                        this.isStartingCamera = false;
                    }
                },

                async stopCamera() {
                    if (this.html5QrCode && this.cameraStarted) {
                        try {
                            await this.html5QrCode.stop();
                        } catch (e) {
                            console.warn("Error stopping scanner:", e);
                        }
                        this.cameraStarted = false;
                    }
                },

                async switchCamera(targetId = null) {
                    if (this.cameraDevices.length <= 1 && !targetId) return;
                    if (targetId) {
                        this.selectedCameraId = targetId;
                    } else {
                        const currentIndex = this.cameraDevices.findIndex(d => d.id === this.selectedCameraId);
                        const nextIndex = (currentIndex + 1) % this.cameraDevices.length;
                        this.selectedCameraId = this.cameraDevices[nextIndex].id;
                    }
                    await this.startCamera(false);
                },

                getCameraShortName() {
                    if (!this.selectedCameraId || !this.cameraDevices.length) return 'Kamera Utama';
                    const dev = this.cameraDevices.find(d => d.id === this.selectedCameraId);
                    if (!dev) return 'Kamera Aktif';
                    const label = dev.label || '';
                    if (/back|rear|environment/i.test(label)) return 'Belakang (1x)';
                    if (/front|user|selfie/i.test(label)) return 'Depan';
                    return label.substring(0, 14);
                },

                getVideoTrack() {
                    try {
                        const video = document.querySelector('#reader video');
                        if (video && video.srcObject) {
                            const tracks = video.srcObject.getVideoTracks();
                            if (tracks && tracks.length) return tracks[0];
                        }
                    } catch (e) {}
                    return null;
                },

                updateCameraCapabilities() {
                    const track = this.getVideoTrack();
                    if (!track) return;
                    try {
                        const caps = track.getCapabilities ? track.getCapabilities() : {};
                        this.hasTorch = !!caps.torch;
                        this.torchOn = false;
                        if (caps.zoom) {
                            this.hasZoom = true;
                            this.minZoom = caps.zoom.min || 1;
                            this.maxZoom = caps.zoom.max || 1;
                            this.currentZoom = 1;
                        } else {
                            this.hasZoom = false;
                        }
                    } catch (e) {
                        console.warn("Capabilities query failed:", e);
                    }
                },

                async toggleTorch() {
                    const track = this.getVideoTrack();
                    if (!track || !this.hasTorch) return;
                    try {
                        this.torchOn = !this.torchOn;
                        await track.applyConstraints({ advanced: [{ torch: this.torchOn }] });
                    } catch (e) {
                        console.warn("Torch failed:", e);
                        this.torchOn = false;
                    }
                },

                async setZoom(level) {
                    const track = this.getVideoTrack();
                    if (!track || !this.hasZoom) return;
                    try {
                        const targetZoom = Math.max(this.minZoom, Math.min(level, this.maxZoom));
                        await track.applyConstraints({ advanced: [{ zoom: targetZoom }] });
                        this.currentZoom = targetZoom;
                    } catch (e) {
                        console.warn("Zoom failed:", e);
                    }
                },

                async triggerRefocus(event) {
                    if (event) {
                        const rect = event.currentTarget.getBoundingClientRect();
                        this.focusRing.x = event.clientX - rect.left;
                        this.focusRing.y = event.clientY - rect.top;
                        this.focusRing.visible = true;
                        if (this.focusRing.timer) clearTimeout(this.focusRing.timer);
                        this.focusRing.timer = setTimeout(() => { this.focusRing.visible = false; }, 800);
                    }

                    const track = this.getVideoTrack();
                    if (!track) return;
                    try {
                        const caps = track.getCapabilities ? track.getCapabilities() : {};
                        if (caps.focusMode && caps.focusMode.includes('continuous')) {
                            await track.applyConstraints({ advanced: [{ focusMode: 'continuous' }] });
                        }
                    } catch (e) {}
                },

                takePhoto() {
                    try {
                        const video = document.querySelector('#reader video');
                        if (!video) return null;
                        const canvas = document.getElementById('photo-canvas') || document.createElement('canvas');
                        canvas.width = video.videoWidth || 640;
                        canvas.height = video.videoHeight || 480;
                        const ctx = canvas.getContext('2d');
                        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
                        return canvas.toDataURL('image/jpeg', 0.85);
                    } catch (e) {
                        console.error("Take photo error:", e);
                        return null;
                    }
                },

                async onScanSuccess(decodedText) {
                    if (this.processing || this.result) return;
                    this.processing = true;
                    
                    if (this.mode === 'online') {
                        try {
                            const checkResponse = await fetch("{{ route('organizer.redeem.check') }}", {
                                method: "POST",
                                headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": "{{ csrf_token() }}" },
                                body: JSON.stringify({ ticket_code: decodedText, event_id: "{{ $event->id }}" })
                            });
                            const checkData = await checkResponse.json();
                            
                            if (!checkData.success) {
                                this.result = checkData;
                                this.playResultSound();
                                this.startAutoReset(5);
                                this.processing = false;
                                return;
                            }
                            
                            const photo = this.takePhoto();
                            await this.processOnline(decodedText, photo);
                            
                        } catch (err) {
                            console.error("Check failed", err);
                            this.result = { success: false, message: 'Kesalahan Jaringan!' };
                            this.playResultSound();
                            this.startAutoReset(4);
                        }
                    } else {
                        const photo = this.takePhoto();
                        await this.processOffline(decodedText, photo);
                    }

                    this.processing = false;
                },

                startAutoReset(seconds = 4) {
                    if (this.autoResetInterval) clearInterval(this.autoResetInterval);
                    this.autoResetTimer = seconds;
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
                        const data = await response.json();
                        this.result = data;
                        this.playResultSound();
                        this.startAutoReset(data.success ? 4 : 5);
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
                                details: {
                                    redeemed_at: ticket.redeemed_at,
                                    redeemed_by: 'Offline Mode',
                                    photo: ticket.redeem_photo,
                                    customer: ticket.customer,
                                    category: ticket.category
                                }
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
                    this.startAutoReset(this.result.success ? 4 : 5);
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
                    try {
                        const sound = document.getElementById(this.result && this.result.success ? 'sound-success' : 'sound-fail');
                        if (sound) {
                            sound.currentTime = 0;
                            sound.play().catch(() => {});
                        }
                    } catch (e) {}
                },

                resetScanner() {
                    if (this.autoResetInterval) {
                        clearInterval(this.autoResetInterval);
                        this.autoResetInterval = null;
                    }
                    this.result = null;
                    if (this.inputType === 'auto') {
                        this.$nextTick(() => this.focusInput());
                    }
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
