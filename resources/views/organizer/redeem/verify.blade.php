<x-app-layout>
    <x-slot name="title">Verifikasi Keamanan - {{ $event->name }}</x-slot>

    <div class="max-w-md mx-auto space-y-8 animate-in fade-in slide-in-from-bottom-4 duration-700 pt-12">
        <div class="text-center space-y-2">
            <div class="w-20 h-20 bg-indigo-600 text-white rounded-3xl flex items-center justify-center mx-auto shadow-xl shadow-indigo-200 mb-6 transform rotate-3">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
            </div>
            <h2 class="text-2xl font-black text-slate-900 font-outfit uppercase">Gate Security</h2>
            <p class="text-slate-500">{{ $event->name }}</p>
        </div>

        <div style="background-color: white; border: 1px solid #e2e8f0; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.1);" class="rounded-[2.5rem] overflow-hidden">
            <form action="{{ route('organizer.redeem.verify.post', $event) }}" method="POST" x-data="{ role: 'redeem' }" class="p-8 sm:p-10 space-y-8">
                @csrf
                
                @if(session('error'))
                    <div style="background-color: #fff1f2; border: 1px solid #fecdd3; color: #e11d48;" class="p-4 rounded-2xl text-sm font-bold animate-shake">
                        {{ session('error') }}
                    </div>
                @endif

                <div class="space-y-6">
                    <div>
                        <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-3">Tugas Operasional</label>
                        <div class="grid grid-cols-2 gap-3">
                            <label class="relative flex cursor-pointer">
                                <input type="radio" name="role" value="redeem" x-model="role" class="sr-only">
                                <div 
                                    :style="role === 'redeem' 
                                        ? 'background-color: #ecfdf5; border: 2px solid #10b981; box-shadow: 0 4px 6px -1px rgba(16, 185, 129, 0.1);' 
                                        : 'background-color: white; border: 2px solid #e2e8f0;'"
                                    class="w-full p-4 rounded-2xl text-center transition-all"
                                >
                                    <div :style="role === 'redeem' ? 'color: #059669;' : 'color: #64748b;'" class="text-xs font-black uppercase">Redeem</div>
                                </div>
                            </label>
                            <label class="relative flex cursor-pointer">
                                <input type="radio" name="role" value="gate" x-model="role" class="sr-only">
                                <div 
                                    :style="role === 'gate' 
                                        ? 'background-color: #ecfdf5; border: 2px solid #10b981; box-shadow: 0 4px 6px -1px rgba(16, 185, 129, 0.1);' 
                                        : 'background-color: white; border: 2px solid #e2e8f0;'"
                                    class="w-full p-4 rounded-2xl text-center transition-all"
                                >
                                    <div :style="role === 'gate' ? 'color: #059669;' : 'color: #64748b;'" class="text-xs font-black uppercase">Gate In</div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div>
                        <label for="security_code" class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-3">Kode Keamanan Event</label>
                        <input 
                            type="password" 
                            name="security_code" 
                            id="security_code" 
                            style="background-color: #f8fafc; border: 2px solid #f1f5f9; color: #0f172a;"
                            class="w-full rounded-2xl p-4 text-center text-2xl font-black tracking-[0.5em] focus:border-emerald-500 focus:bg-white transition-all outline-none"
                            placeholder="······"
                            required
                        >
                    </div>
                </div>

                <button type="submit" style="background-color: #10b981 !important; color: white !important; border: 1px solid #059669; box-shadow: 0 10px 15px -3px rgba(16, 185, 129, 0.3);" class="w-full py-5 rounded-2xl font-black uppercase tracking-widest hover:brightness-110 transition transform active:scale-95 flex items-center justify-center gap-3">
                    Masuk Mode Scan
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </button>
            </form>
        </div>

        <div class="text-center">
            <a href="{{ route('organizer.redeem.index') }}" style="background-color: white; border: 1px solid #e2e8f0; color: #334155;" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl text-sm font-bold hover:bg-slate-50 transition shadow-sm">
                <svg style="color: #64748b;" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Kembali pilih event
            </a>
        </div>
    </div>
</x-app-layout>
