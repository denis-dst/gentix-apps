<x-app-layout>
    <x-slot name="title">Kelola Event</x-slot>
    <x-slot name="header">Kelola Event</x-slot>

    <div class="space-y-6">
        <!-- Stats Row -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-slate-500">Total Event</p>
                        <h4 class="text-2xl font-bold text-slate-900">{{ $events->total() }}</h4>
                    </div>
                </div>
            </div>
            <!-- More stats can be added here -->
        </div>

        <!-- Event List -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="p-6 border-b border-slate-50 flex justify-between items-center bg-slate-50/30">
                <h3 class="font-bold text-slate-800">Daftar Event</h3>
                <div class="flex gap-2">
                    <!-- Search/Filter can be added here -->
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/50">
                            <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider">Event</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider">Waktu & Lokasi</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($events as $event)
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-4">
                                        <div class="w-16 h-12 rounded-lg bg-slate-100 overflow-hidden shrink-0">
                                            <img src="{{ $event->background_image ? asset('storage/' . $event->background_image) : 'https://images.unsplash.com/photo-1492684223066-81342ee5ff30?auto=format&fit=crop&q=80' }}" class="w-full h-full object-cover">
                                        </div>
                                        <div>
                                            <div class="font-bold text-slate-900">{{ $event->name }}</div>
                                            <div class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-0.5">ID: #{{ $event->id }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-slate-600 font-medium">{{ $event->event_start_date->format('d M Y, H:i') }}</div>
                                    <div class="text-xs text-slate-400">{{ $event->venue }}, {{ $event->city }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $statusClasses = [
                                            'published' => 'bg-emerald-50 text-emerald-600 border-emerald-100',
                                            'draft' => 'bg-slate-50 text-slate-500 border-slate-100',
                                            'cancelled' => 'bg-rose-50 text-rose-600 border-rose-100',
                                        ];
                                    @endphp
                                    <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase border {{ $statusClasses[$event->status] ?? 'bg-slate-50 text-slate-500 border-slate-100' }}">
                                        {{ $event->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('events.show', $event->slug) }}" target="_blank" class="p-2 text-slate-400 hover:text-blue-600 bg-slate-50 rounded-lg border border-slate-100 transition">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                        </a>
                                        <a href="{{ route('organizer.events.edit', $event) }}" class="p-2 text-slate-400 hover:text-purple-600 bg-slate-50 rounded-lg border border-slate-100 transition">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center text-slate-400">
                                    Belum ada event yang dibuat.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="p-6 bg-slate-50/30 border-t border-slate-50">
                {{ $events->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
