<x-app-layout>
    <x-slot name="title">Edit Event: {{ $event->name }}</x-slot>

    <div class="max-w-7xl mx-auto space-y-8 animate-in fade-in slide-in-from-bottom-4 duration-700">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="text-3xl font-black text-gray-800 tracking-tight">{{ $event->name }}</h2>
                <p class="text-gray-500 font-medium">Manage your event details and ticket tiers.</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('organizer.dashboard') }}" class="px-5 py-2.5 bg-white border border-gray-200 text-gray-600 rounded-xl font-bold hover:bg-gray-50 transition shadow-sm">
                    Back to Dashboard
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Column: Event Details Form -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden sticky top-8">
                    <div class="p-6 border-b border-gray-50 bg-gray-50/50">
                        <h3 class="font-bold text-gray-800">Event Information</h3>
                    </div>
                    <form action="{{ route('organizer.events.update', $event) }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
                        @csrf
                        @method('PATCH')
                        
                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Event Name</label>
                            <input type="text" name="name" value="{{ old('name', $event->name) }}" class="w-full rounded-xl border-gray-200 focus:border-purple-500 focus:ring-purple-500 transition">
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Venue</label>
                                <input type="text" name="venue" value="{{ old('venue', $event->venue) }}" class="w-full rounded-xl border-gray-200 focus:border-purple-500 focus:ring-purple-500 transition">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">City</label>
                                <input type="text" name="city" value="{{ old('city', $event->city) }}" class="w-full rounded-xl border-gray-200 focus:border-purple-500 focus:ring-purple-500 transition">
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Start Date</label>
                                <input type="datetime-local" name="event_start_date" value="{{ old('event_start_date', $event->event_start_date->format('Y-m-d\TH:i')) }}" class="w-full rounded-xl border-gray-200 focus:border-purple-500 focus:ring-purple-500 transition">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">End Date</label>
                                <input type="datetime-local" name="event_end_date" value="{{ old('event_end_date', $event->event_end_date->format('Y-m-d\TH:i')) }}" class="w-full rounded-xl border-gray-200 focus:border-purple-500 focus:ring-purple-500 transition">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Status</label>
                            <select name="status" class="w-full rounded-xl border-gray-200 focus:border-purple-500 focus:ring-purple-500 transition">
                                <option value="draft" {{ $event->status == 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="published" {{ $event->status == 'published' ? 'selected' : '' }}>Published</option>
                                <option value="cancelled" {{ $event->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </div>

                        <button type="submit" class="w-full py-3 bg-purple-600 text-white rounded-xl font-bold hover:bg-purple-700 transition shadow-lg shadow-purple-200">
                            Save Event Details
                        </button>
                    </form>
                </div>
            </div>

            <!-- Right Column: Ticket Categories -->
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-6 border-b border-gray-50 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div>
                            <h3 class="font-bold text-gray-800 text-xl">Ticket Categories</h3>
                            <p class="text-sm text-gray-500">Configure tiers, quotas, and release schedules.</p>
                        </div>
                        <a href="{{ route('organizer.events.categories.create', $event) }}" class="px-5 py-2.5 bg-purple-600 text-white rounded-xl font-bold hover:bg-purple-700 transition shadow-lg shadow-purple-200 flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" /></svg>
                            Add Category
                        </a>
                    </div>

                    <div class="p-6">
                        @if($event->ticketCategories->isEmpty())
                            <div class="text-center py-12">
                                <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 text-gray-300">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" /></svg>
                                </div>
                                <p class="text-gray-400 font-medium">No ticket categories yet. Click "Add Category" to start.</p>
                            </div>
                        @else
                            <div class="space-y-4">
                                @foreach($event->ticketCategories as $category)
                                    <div class="p-5 border border-gray-100 rounded-2xl flex items-center justify-between hover:border-purple-200 transition bg-gray-50/30 group">
                                        <div class="flex items-center gap-4">
                                            <div class="w-3 h-12 rounded-full" style="background-color: {{ $category->hex_color ?? '#6366F1' }}"></div>
                                            <div>
                                                <h4 class="font-bold text-gray-800 text-lg">{{ $category->name }}</h4>
                                                <div class="flex items-center gap-3 mt-1">
                                                    <span class="text-sm font-medium text-gray-600">Rp {{ number_format($category->price, 0, ',', '.') }}</span>
                                                    <span class="text-xs text-gray-300">|</span>
                                                    <span class="text-xs font-bold text-purple-600 uppercase">{{ $category->quota }} Quota</span>
                                                </div>
                                                @if($category->sale_start_at)
                                                    <div class="text-[10px] text-gray-400 mt-1 uppercase font-bold tracking-wider">
                                                        Release: {{ $category->sale_start_at->format('d M Y H:i') }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="flex gap-2">
                                            <a href="{{ route('organizer.events.categories.edit', [$event, $category]) }}" class="p-2 bg-white rounded-lg border border-gray-200 text-gray-400 hover:text-blue-600 hover:border-blue-200 transition shadow-sm">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                            </a>
                                            <form action="{{ route('organizer.events.categories.destroy', [$event, $category]) }}" method="POST" onsubmit="return confirm('Are you sure?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-2 bg-white rounded-lg border border-gray-200 text-gray-400 hover:text-rose-600 hover:border-rose-200 transition shadow-sm">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
