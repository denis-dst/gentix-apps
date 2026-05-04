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
                    <p class="text-slate-500 mt-2 font-medium">Tentukan kategori gate yang akan dikelola hari ini.</p>
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

                        <!-- Event Information -->
                        <div>
                            <label class="block text-xs font-black text-slate-700 uppercase tracking-[0.2em] mb-3 ml-1">Detail Event Operasional</label>
                            <div class="p-6 rounded-3xl bg-slate-50 border-2 border-slate-100 flex flex-col sm:flex-row items-center gap-6">
                                <div class="w-20 h-20 rounded-2xl overflow-hidden shrink-0 shadow-sm border-2 border-white">
                                    @if($event->background_image)
                                        <img src="{{ Storage::url($event->background_image) }}" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-black text-2xl font-outfit">
                                            {{ substr($event->name, 0, 1) }}
                                        </div>
                                    @endif
                                </div>
                                <div class="text-center sm:text-left min-w-0">
                                    <h4 class="text-lg font-black text-slate-900 font-outfit truncate leading-tight mb-1">{{ $event->name }}</h4>
                                    <div class="flex flex-col gap-1">
                                        <div class="flex items-center justify-center sm:justify-start gap-2 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                            <span class="truncate">{{ $event->venue }}</span>
                                        </div>
                                        <div class="flex items-center justify-center sm:justify-start gap-2 text-[10px] font-black text-orange-500 uppercase tracking-widest">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                            <span>{{ $event->event_start_date->format('d M Y, H:i') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="gate_mode" value="IN">
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
