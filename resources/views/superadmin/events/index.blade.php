<x-app-layout>
    <x-slot name="title">Event Management</x-slot>

    @if(session('success'))
    <div class="mb-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 shadow-sm" role="alert">
        <p class="font-bold">Success</p>
        <p>{{ session('success') }}</p>
    </div>
    @endif

    <div class="bg-white rounded-sm border-t-4 border-purple-600 shadow-md overflow-hidden">
        <div class="px-4 py-3 border-b border-[#dee2e6] flex justify-between items-center bg-white">
            <h3 class="text-lg font-medium text-gray-800">All Events</h3>
            <div class="flex gap-2">
                <a href="{{ route('superadmin.events.trash') }}" class="px-3 py-1 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-bold rounded shadow-sm transition flex items-center gap-1">
                    <i class="fas fa-trash-alt"></i> Trash
                </a>
                <a href="{{ route('superadmin.events.create') }}" class="px-3 py-1 bg-purple-600 hover:bg-purple-700 text-white text-sm font-bold rounded shadow-sm transition">
                    <i class="fas fa-plus mr-1"></i> Add Event
                </a>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-[#f8f9fa] border-b border-[#dee2e6] text-[#495057] font-bold uppercase text-[11px] tracking-wider">
                    <tr>
                        <th class="px-4 py-3">Event</th>
                        <th class="px-4 py-3">Organizer</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Date</th>
                        <th class="px-4 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#dee2e6]">
                    @forelse($events as $event)
                    <tr class="hover:bg-[#f2f2f2] transition">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 rounded-lg bg-gray-100 overflow-hidden border border-gray-200 flex-shrink-0">
                                    @if($event->banner_image)
                                        <img src="{{ asset('storage/' . $event->banner_image) }}" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-gray-400">
                                            <i class="fas fa-image"></i>
                                        </div>
                                    @endif
                                </div>
                                <div>
                                    <div class="font-bold text-[#343a40]">{{ $event->name }}</div>
                                    <div class="text-xs text-[#6c757d]">{{ $event->city }}, {{ $event->venue }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <span class="text-sm font-medium text-gray-700">{{ $event->tenant->name }}</span>
                        </td>
                        <td class="px-4 py-3">
                            @php
                                $statusColors = [
                                    'draft' => 'bg-gray-100 text-gray-600',
                                    'published' => 'bg-green-100 text-green-700',
                                    'finished' => 'bg-blue-100 text-blue-700',
                                    'cancelled' => 'bg-red-100 text-red-700',
                                ];
                            @endphp
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $statusColors[$event->status] ?? 'bg-gray-100 text-gray-600' }}">
                                {{ $event->status }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="text-xs text-gray-600 font-bold">{{ $event->event_start_date->format('M d, Y') }}</div>
                            <div class="text-[10px] text-gray-400 italic">at {{ $event->event_start_date->format('H:i') }}</div>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex justify-end gap-1">
                                <a href="{{ route('superadmin.events.edit', $event) }}" class="p-1.5 rounded bg-[#f8f9fa] border border-[#dee2e6] text-[#6c757d] hover:bg-[#dee2e6] transition shadow-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                </a>
                                <form action="{{ route('superadmin.events.destroy', $event) }}" method="POST" onsubmit="return confirm('Move this event to trash?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 rounded bg-[#f8f9fa] border border-[#dee2e6] text-[#dc3545] hover:bg-[#dc3545] hover:text-white transition shadow-sm">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-4 py-12 text-center text-[#6c757d] italic">No events found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($events->hasPages())
        <div class="px-4 py-3 border-t border-[#dee2e6] bg-[#f8f9fa]">
            {{ $events->links() }}
        </div>
        @endif
    </div>
</x-app-layout>
