<x-app-layout>
    <x-slot name="hideNav">{{ true }}</x-slot>
    <x-slot name="title">Verifikasi Keamanan - {{ $event->name }}</x-slot>

    <div class="h-full w-full flex flex-col bg-slate-50 overflow-hidden font-sans">
        <!-- Header Bar -->
        <header class="h-auto min-h-[5.5rem] pt-10 sm:pt-0 flex items-center justify-between px-4 sm:px-6 bg-white border-b border-slate-200 shrink-0 z-50">
            <div class="flex items-center gap-3 py-4 sm:py-0">
                <a href="{{ route('organizer.redeem.index') }}" class="flex items-center gap-2 bg-slate-100 hover:bg-slate-200 px-3 py-2 rounded-xl transition text-slate-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                    <span class="text-[10px] font-black uppercase tracking-widest">BACK</span>
                </a>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">REDEEM SYSTEM</span>
            </div>
        </header>

        <main class="flex-1 overflow-y-auto p-6 flex items-center justify-center">
            <div class="w-full max-w-md">
                <div class="bg-white rounded-[2.5rem] shadow-2xl shadow-slate-200/60 border border-slate-100 overflow-hidden">
                    <form action="{{ route('organizer.redeem.verify.post', $event) }}" method="POST" x-data="{ role: 'redeem' }" class="p-8 sm:p-10 space-y-8">
                        @csrf
                        
                        <div class="text-center space-y-2 mb-6">
                            <div class="w-20 h-20 bg-emerald-600 text-white rounded-3xl flex items-center justify-center mx-auto shadow-xl shadow-emerald-200 mb-6 transform rotate-3">
                                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            </div>
                            <h2 class="text-2xl font-black text-slate-900 font-outfit uppercase tracking-tight">Otorisasi Loket</h2>
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">{{ $event->name }}</p>
                        </div>

                        @if(session('error'))
                            <div class="p-4 rounded-2xl bg-rose-50 border border-rose-100 text-rose-600 text-sm font-bold animate-shake">
                                {{ session('error') }}
                            </div>
                        @endif

                        <div class="space-y-6">
                            <div>
                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Tugas Operasional</label>
                                <div class="grid grid-cols-2 gap-3">
                                    <label class="relative flex cursor-pointer">
                                        <input type="radio" name="role" value="redeem" x-model="role" class="sr-only">
                                        <div 
                                            :class="role === 'redeem' 
                                                ? 'bg-emerald-50 border-2 border-emerald-500 shadow-lg shadow-emerald-500/10' 
                                                : 'bg-white border-2 border-slate-100'"
                                            class="w-full p-4 rounded-2xl text-center transition-all"
                                        >
                                            <div :class="role === 'redeem' ? 'text-emerald-700' : 'text-slate-400'" class="text-[10px] font-black uppercase tracking-widest">Redeem</div>
                                        </div>
                                    </label>
                                    <label class="relative flex cursor-pointer">
                                        <input type="radio" name="role" value="gate" x-model="role" class="sr-only">
                                        <div 
                                            :class="role === 'gate' 
                                                ? 'bg-emerald-50 border-2 border-emerald-500 shadow-lg shadow-emerald-500/10' 
                                                : 'bg-white border-2 border-slate-100'"
                                            class="w-full p-4 rounded-2xl text-center transition-all"
                                        >
                                            <div :class="role === 'gate' ? 'text-emerald-700' : 'text-slate-400'" class="text-[10px] font-black uppercase tracking-widest">Gate In</div>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <div>
                                <label for="security_code" class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Kode Keamanan Event</label>
                                <input 
                                    type="password" 
                                    name="security_code" 
                                    id="security_code" 
                                    class="w-full bg-slate-50 border-2 border-slate-100 rounded-2xl p-5 text-center text-2xl font-black tracking-[0.5em] focus:border-emerald-500 focus:bg-white transition-all outline-none"
                                    placeholder="······"
                                    required
                                >
                            </div>
                        </div>

                        <button type="submit" class="w-full py-5 bg-emerald-600 text-white rounded-2xl font-black uppercase tracking-widest text-xs hover:bg-emerald-700 transition shadow-xl shadow-emerald-200 flex items-center justify-center gap-3">
                            <span>Mulai Operasional</span>
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </button>
                    </form>
                </div>
            </div>
        </main>
    </div>
</x-app-layout>
