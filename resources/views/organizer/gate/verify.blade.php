<x-app-layout>
    <x-slot name="title">Verifikasi Keamanan - Gate System</x-slot>
    <x-slot name="header">Gate System - Verifikasi</x-slot>

    <div class="max-w-xl mx-auto">
        <div class="bg-white rounded-[2.5rem] shadow-xl shadow-slate-200/50 border border-slate-100 overflow-hidden">
            <div class="p-8 sm:p-12">
                <div class="text-center mb-10">
                    <div class="w-20 h-20 bg-orange-100 rounded-3xl flex items-center justify-center text-orange-600 mx-auto mb-6 shadow-lg shadow-orange-100 animate-bounce">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                    </div>
                    <h3 class="text-2xl font-black text-slate-900 font-outfit uppercase tracking-wider">Otorisasi Event</h3>
                    <p class="text-slate-500 mt-2 font-medium">Masukkan kode keamanan untuk mulai mengoperasikan gate <br><span class="text-orange-600 font-bold">"{{ $event->name }}"</span></p>
                </div>

                @if(session('error'))
                    <div class="mb-6 bg-rose-50 border border-rose-100 text-rose-600 p-4 rounded-2xl flex items-center gap-3 animate-in fade-in slide-in-from-top-2 duration-300">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        <span class="text-sm font-bold">{{ session('error') }}</span>
                    </div>
                @endif

                <form action="{{ route('organizer.gate.verify.post', $event) }}" method="POST" class="space-y-6">
                    @csrf
                    <div>
                        <label for="security_code" class="block text-xs font-black text-slate-700 uppercase tracking-[0.2em] mb-3 ml-1">Pin Keamanan Event</label>
                        <input type="password" name="security_code" id="security_code" required autofocus
                               class="w-full px-6 py-4 bg-slate-50 border-2 border-slate-100 rounded-2xl focus:border-orange-500 focus:ring-0 text-center text-3xl font-black tracking-[1em] text-slate-900 transition-all placeholder:text-slate-200"
                               placeholder="····">
                        @error('security_code')
                            <p class="mt-2 text-sm text-rose-500 font-bold">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="pt-4">
                        <button type="submit" class="w-full py-4 bg-orange-600 text-white rounded-2xl font-black shadow-xl shadow-orange-200 hover:bg-orange-700 transition transform active:scale-[0.98] flex items-center justify-center gap-3">
                            <span>Verifikasi & Lanjutkan</span>
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" /></svg>
                        </button>
                    </div>
                </form>

                <div class="mt-8 text-center">
                    <a href="{{ route('organizer.gate.index') }}" class="text-xs font-bold text-slate-400 hover:text-slate-600 transition uppercase tracking-widest">
                        <i class="fas fa-arrow-left mr-2"></i> Kembali ke List Event
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
