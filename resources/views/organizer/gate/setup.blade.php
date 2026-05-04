<x-app-layout>
    <x-slot name="title">Setup Operasional Gate - {{ $event->name }}</x-slot>
    <x-slot name="header">Gate System - Setup</x-slot>

    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-[2.5rem] shadow-xl shadow-slate-200/50 border border-slate-100 overflow-hidden">
            <div class="p-8 sm:p-12">
                <div class="text-center mb-10">
                    <div class="w-20 h-20 bg-indigo-100 rounded-3xl flex items-center justify-center text-indigo-600 mx-auto mb-6 shadow-lg shadow-indigo-100">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 9v3m-3-3v3m6-3v3" /></svg>
                    </div>
                    <h3 class="text-2xl font-black text-slate-900 font-outfit uppercase tracking-wider">Konfigurasi Gate</h3>
                    <p class="text-slate-500 mt-2 font-medium">Tentukan lokasi gate dan mode operasional saat ini.</p>
                </div>

                <form action="{{ route('organizer.gate.setup.post', $event) }}" method="POST" class="space-y-8">
                    @csrf
                    
                    <div class="space-y-6">
                        <!-- Gate Category Selection -->
                        <div>
                            <label for="gate_category_id" class="block text-xs font-black text-slate-700 uppercase tracking-[0.2em] mb-3 ml-1">Pilih Kategori Gate</label>
                            <select name="gate_category_id" id="gate_category_id" required
                                    class="w-full px-6 py-4 bg-slate-50 border-2 border-slate-100 rounded-2xl focus:border-indigo-500 focus:ring-0 text-lg font-bold text-slate-900 transition-all">
                                <option value="" disabled selected>-- Pilih Kategori Tiket --</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">Tiket {{ $category->name }}</option>
                                @endforeach
                            </select>
                            @error('gate_category_id')
                                <p class="mt-2 text-sm text-rose-500 font-bold">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Gate Mode -->
                        <div>
                            <label class="block text-xs font-black text-slate-700 uppercase tracking-[0.2em] mb-3 ml-1">Mode Operasional</label>
                            <div class="grid grid-cols-2 gap-4">
                                <label class="relative cursor-pointer">
                                    <input type="radio" name="gate_mode" value="IN" class="peer sr-only" checked>
                                    <div class="p-6 rounded-2xl border-2 border-slate-100 bg-slate-50 peer-checked:border-emerald-500 peer-checked:bg-emerald-50 transition-all">
                                        <div class="flex flex-col items-center gap-3">
                                            <div class="w-10 h-10 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
                                            </div>
                                            <div class="text-center">
                                                <span class="block font-black text-slate-900 uppercase tracking-widest text-sm">Check-In</span>
                                                <span class="text-[10px] font-bold text-slate-400 uppercase">Masuk Area</span>
                                            </div>
                                        </div>
                                    </div>
                                </label>

                                <label class="relative cursor-pointer">
                                    <input type="radio" name="gate_mode" value="OUT" class="peer sr-only">
                                    <div class="p-6 rounded-2xl border-2 border-slate-100 bg-slate-50 peer-checked:border-orange-500 peer-checked:bg-orange-50 transition-all">
                                        <div class="flex flex-col items-center gap-3">
                                            <div class="w-10 h-10 rounded-full bg-orange-100 text-orange-600 flex items-center justify-center">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4-4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
                                            </div>
                                            <div class="text-center">
                                                <span class="block font-black text-slate-900 uppercase tracking-widest text-sm">Check-Out</span>
                                                <span class="text-[10px] font-bold text-slate-400 uppercase">Keluar Area</span>
                                            </div>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="pt-4">
                        <button type="submit" class="w-full py-4 bg-indigo-600 text-white rounded-2xl font-black shadow-xl shadow-indigo-200 hover:bg-indigo-700 transition transform active:scale-[0.98] flex items-center justify-center gap-3">
                            <span>Buka Scanner Gate</span>
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
