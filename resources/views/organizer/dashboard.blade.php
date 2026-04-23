<x-app-layout>
    <x-slot name="title">Partner Dashboard</x-slot>

    <div class="space-y-8 animate-in fade-in slide-in-from-bottom-4 duration-700">
        <!-- Welcome Hero -->
        <div class="relative overflow-hidden bg-gradient-to-br from-purple-600 to-indigo-700 rounded-2xl p-8 sm:p-12 text-white shadow-xl">
            <div class="relative z-10 max-w-2xl">
                <h2 class="text-3xl sm:text-4xl font-black mb-4 leading-tight">
                    Welcome back, <br>
                    <span class="text-purple-200">{{ Auth::user()->tenant->name ?? 'Partner' }}</span>!
                </h2>
                <p class="text-lg text-white/90 font-medium mb-8 leading-relaxed">
                    Manage your events, track sales, and grow your audience with GenTix.
                </p>
                <div class="flex flex-wrap gap-4">
                    <button class="px-6 py-3 bg-white text-purple-700 rounded-xl font-bold hover:bg-gray-50 transition shadow-lg flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" /></svg>
                        Create New Event
                    </button>
                </div>
            </div>
            
            <!-- Decorative Elements -->
            <div class="absolute top-0 right-0 -mt-20 -mr-20 w-96 h-96 bg-white/10 rounded-full blur-3xl"></div>
            <div class="absolute bottom-0 right-0 -mb-20 -mr-20 w-64 h-64 bg-black/10 rounded-full blur-2xl"></div>
        </div>

        <!-- Events List -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 border-bottom border-gray-50 flex justify-between items-center">
                <h3 class="text-xl font-bold text-gray-800">Your Events</h3>
                <span class="px-3 py-1 bg-purple-50 text-purple-600 text-xs font-bold rounded-full uppercase tracking-wider">
                    {{ $events->count() }} Total
                </span>
            </div>

            @if($events->isEmpty())
                <div class="p-12 text-center">
                    <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
                    </div>
                    <h4 class="text-lg font-bold text-gray-700">No events yet</h4>
                    <p class="text-gray-500 mb-8">Start by creating your first event to see it here.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50/50">
                                <th class="p-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Event Name</th>
                                <th class="p-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Date & Venue</th>
                                <th class="p-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Status</th>
                                <th class="p-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Stats</th>
                                <th class="p-4 text-xs font-bold text-gray-400 uppercase tracking-wider text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($events as $event)
                                <tr class="hover:bg-gray-50/50 transition">
                                    <td class="p-4">
                                        <div class="flex items-center gap-4">
                                            @if($event->background_image)
                                                <img src="{{ Storage::url($event->background_image) }}" class="w-12 h-12 rounded-lg object-cover shadow-sm">
                                            @else
                                                <div class="w-12 h-12 rounded-lg bg-purple-100 flex items-center justify-center text-purple-600 font-bold">
                                                    {{ substr($event->name, 0, 1) }}
                                                </div>
                                            @endif
                                            <div>
                                                <div class="font-bold text-gray-800">{{ $event->name }}</div>
                                                <div class="text-xs text-gray-400">{{ $event->slug }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="p-4">
                                        <div class="text-sm font-medium text-gray-700">{{ $event->event_start_date->format('d M Y') }}</div>
                                        <div class="text-xs text-gray-400">{{ $event->venue }}</div>
                                    </td>
                                    <td class="p-4">
                                        @php
                                            $statusColors = [
                                                'published' => 'bg-emerald-100 text-emerald-700',
                                                'draft' => 'bg-gray-100 text-gray-600',
                                                'cancelled' => 'bg-rose-100 text-rose-700',
                                            ];
                                            $color = $statusColors[$event->status] ?? 'bg-blue-100 text-blue-700';
                                        @endphp
                                        <span class="px-2 py-1 {{ $color }} text-[10px] font-black uppercase rounded-md tracking-wider">
                                            {{ $event->status }}
                                        </span>
                                    </td>
                                    <td class="p-4">
                                        <div class="flex gap-4">
                                            <div class="text-center">
                                                <div class="text-sm font-bold text-gray-800">{{ $event->ticket_categories_count }}</div>
                                                <div class="text-[10px] text-gray-400 uppercase font-bold">Tiers</div>
                                            </div>
                                            <div class="text-center">
                                                <div class="text-sm font-bold text-gray-800">{{ $event->tickets_count }}</div>
                                                <div class="text-[10px] text-gray-400 uppercase font-bold">Sold</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="p-4 text-right">
                                        <div class="flex justify-end gap-2">
                                            <a href="{{ route('organizer.events.edit', $event) }}" class="p-2 hover:bg-white hover:shadow-sm rounded-lg transition text-gray-400 hover:text-purple-600">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                            </a>
                                            <button class="p-2 hover:bg-white hover:shadow-sm rounded-lg transition text-gray-400 hover:text-indigo-600">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
