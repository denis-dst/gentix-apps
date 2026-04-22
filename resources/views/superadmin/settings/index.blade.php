<x-app-layout>
    <x-slot name="title">Global Site Settings</x-slot>

    @if(session('success'))
    <div class="mb-4 px-4 py-3 bg-[#d4edda] border border-[#c3e6cb] text-[#155724] rounded text-sm font-bold shadow-sm">
        {{ session('success') }}
    </div>
    @endif

    <form action="{{ route('superadmin.settings.update') }}" method="POST" class="space-y-6">
        @csrf
        
        @foreach($settings as $group => $items)
        <div class="bg-white rounded-sm border-t-4 border-[#343a40] shadow-md overflow-hidden">
            <div class="px-4 py-3 border-b border-[#dee2e6] bg-[#f8f9fa]">
                <h3 class="text-lg font-medium text-[#343a40] uppercase tracking-wide">{{ $group }} Settings</h3>
            </div>
            <div class="p-6 space-y-4">
                @foreach($items as $item)
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-center">
                    <label class="font-bold text-sm text-[#495057]">{{ str_replace('_', ' ', $item->key) }}</label>
                    <div class="md:col-span-3">
                        @if(Str::contains($item->key, 'description') || Str::contains($item->key, 'subtitle') || Str::contains($item->key, 'address'))
                        <textarea name="{{ $item->key }}" class="w-full bg-white border border-[#ced4da] rounded px-3 py-2 text-sm focus:border-purple-600 focus:ring-0 min-h-[80px]">{{ $item->value }}</textarea>
                        @else
                        <input type="text" name="{{ $item->key }}" value="{{ $item->value }}" class="w-full bg-white border border-[#ced4da] rounded px-3 py-2 text-sm focus:border-purple-600 focus:ring-0">
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endforeach

        <div class="flex justify-end pb-10">
            <button type="submit" class="px-6 py-2.5 bg-purple-600 hover:bg-purple-700 text-white font-bold rounded shadow-md transition">
                <i class="fas fa-save mr-1"></i> Save Configuration
            </button>
        </div>
    </form>
</x-app-layout>
