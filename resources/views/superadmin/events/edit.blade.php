<x-app-layout>
    <x-slot name="title">Edit Event: {{ $event->name }}</x-slot>

    <div class="bg-white rounded-sm border-t-4 border-purple-600 shadow-md overflow-hidden max-w-5xl mx-auto">
        <div class="px-6 py-4 border-b border-[#dee2e6] flex justify-between items-center bg-white">
            <h3 class="text-lg font-bold text-gray-800">Edit Event</h3>
            <a href="{{ route('superadmin.events.index') }}" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold rounded shadow-sm transition">
                <i class="fas fa-arrow-left mr-1"></i> Back to List
            </a>
        </div>
        
        <div class="p-6">
            <form action="{{ route('superadmin.events.update', $event) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                @include('superadmin.events.form')
                
                <div class="mt-8 flex justify-end gap-3 border-t border-gray-100 pt-5">
                    <a href="{{ route('superadmin.events.index') }}" class="px-5 py-2.5 bg-white border border-gray-300 text-gray-700 font-bold rounded-lg hover:bg-gray-50 transition shadow-sm">Cancel</a>
                    <button type="submit" class="px-5 py-2.5 bg-purple-600 text-white font-bold rounded-lg hover:bg-purple-700 transition shadow-md flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                        Update Event
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
