<x-app-layout>
    <x-slot name="hideNav">{{ true }}</x-slot>
    <x-slot name="title">Setup Gate - {{ $event->name }}</x-slot>

    <div class="h-full w-full flex flex-col bg-slate-50 overflow-hidden font-sans">
        <!-- Header Bar -->
        <header class="h-auto min-h-[5.5rem] pt-10 sm:pt-0 flex items-center justify-between px-4 sm:px-6 bg-white border-b border-slate-200 shrink-0 z-50">
            <div class="flex items-center gap-3 py-4 sm:py-0">
                <a href="{{ route('organizer.gate.verify', $event) }}" class="flex items-center gap-2 bg-slate-100 hover:bg-slate-200 px-3 py-2 rounded-xl transition text-slate-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                    <span class="text-[10px] font-black uppercase tracking-widest">BACK</span>
                </a>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">GATE CONFIGURATION</span>
            </div>
        </header>

        <main class="flex-1 overflow-y-auto p-6 flex items-center justify-center">
            <div class="w-full max-w-xl">
                <div class="bg-white rounded-[2.5rem] shadow-2xl shadow-slate-200/60 border border-slate-100 overflow-hidden">
                    <div class="p-8 sm:p-10">
                        <div class="text-center mb-10">
                            <div class="w-16 h-16 bg-indigo-100 rounded-2xl flex items-center justify-center text-indigo-600 mx-auto mb-6 shadow-lg shadow-indigo-50">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 9v3m-3-3v3m6-3v3" /></svg>
                            </div>
                            <h3 class="text-xl font-black text-slate-900 font-outfit uppercase tracking-wider">Konfigurasi Gate</h3>
                            <p class="text-slate-500 mt-2 text-sm font-medium">Tentukan kategori gate yang akan dikelola.</p>
                        </div>

                        <form action="{{ route('organizer.gate.setup.post', $event) }}" method="POST" class="space-y-8">
                            @csrf
                            
                            <div class="space-y-6">
                                <div>
                                    <label for="gate_category_id" class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 ml-1">Pilih Kategori Gate</label>
                                    <select name="gate_category_id" id="gate_category_id" required
                                            class="w-full px-6 py-4 bg-slate-50 border-2 border-slate-100 rounded-2xl focus:border-indigo-500 focus:ring-0 text-lg font-bold text-slate-900 transition-all outline-none">
                                        <option value="" disabled selected>-- Pilih Kategori Tiket --</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}">Tiket {{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="p-6 rounded-[2rem] bg-slate-50 border-2 border-slate-100 flex items-center gap-4">
                                    <div class="w-16 h-16 rounded-xl overflow-hidden shrink-0 border-2 border-white shadow-sm">
                                        @if($event->background_image)
                                            <img src="{{ Storage::url($event->background_image) }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-black text-xl">
                                                {{ substr($event->name, 0, 1) }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <h4 class="text-sm font-black text-slate-900 font-outfit truncate">{{ $event->name }}</h4>
                                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-0.5 truncate">{{ $event->venue }}</p>
                                    </div>
                                </div>
                                <input type="hidden" name="gate_mode" value="IN">
                            </div>

                            <div class="pt-4">
                                <button type="submit" class="w-full py-5 bg-indigo-600 text-white rounded-2xl font-black shadow-xl shadow-indigo-200 hover:bg-indigo-700 transition transform active:scale-[0.98] flex items-center justify-center gap-3 uppercase tracking-widest text-xs">
                                    <span>Buka Scanner Gate</span>
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </main>
    </div>
</x-app-layout>
