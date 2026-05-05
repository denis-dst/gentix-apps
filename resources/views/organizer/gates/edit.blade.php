<x-app-layout>
    <x-slot name="title">Edit Gate: {{ $gate->name }}</x-slot>

    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .select2-container--default .select2-selection--multiple {
            border-radius: 1rem;
            border-color: #e5e7eb;
            padding: 0.5rem;
        }
        .select2-container--default.select2-container--focus .select2-selection--multiple {
            border-color: #ea580c;
        }
        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            background-color: #fff7ed;
            border-color: #ffedd5;
            color: #ea580c;
            border-radius: 0.5rem;
            font-weight: 700;
            font-size: 0.75rem;
            padding: 2px 8px;
        }
        .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
            color: #ea580c;
            margin-right: 5px;
        }
    </style>

    <div class="max-w-3xl mx-auto space-y-8 animate-in fade-in slide-in-from-bottom-4 duration-700">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-3xl font-black text-gray-800 tracking-tight">Edit Gate</h2>
                <p class="text-gray-500 font-medium">Update gate details and ticket category mapping.</p>
            </div>
            <a href="{{ route('organizer.events.gates.index', $event) }}" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
            </a>
        </div>

        <form action="{{ route('organizer.events.gates.update', [$event, $gate]) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8 space-y-6">
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Gate Name</label>
                    <input type="text" name="name" value="{{ old('name', $gate->name) }}" placeholder="e.g. Main Entrance, VIP Gate, North Gate" 
                           class="w-full rounded-2xl border-gray-200 focus:border-orange-500 focus:ring-orange-500 transition-all py-3 px-4 text-lg font-bold" required>
                    @error('name') <p class="text-rose-500 text-xs mt-1 font-bold">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Description (Optional)</label>
                    <textarea name="description" rows="2" class="w-full rounded-2xl border-gray-200 focus:border-orange-500 focus:ring-orange-500 transition-all py-3 px-4" placeholder="Briefly describe the gate location or purpose...">{{ old('description', $gate->description) }}</textarea>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Allowed Ticket Categories</label>
                    @php $gateCategoryIds = $gate->ticketCategories->pluck('id')->toArray(); @endphp
                    <select name="category_ids[]" id="category_ids" class="w-full select2" multiple="multiple" required>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ in_array($category->id, old('category_ids', $gateCategoryIds)) ? 'selected' : '' }}>
                                {{ $category->name }} (Rp {{ number_format($category->price, 0, ',', '.') }})
                            </option>
                        @endforeach
                    </select>
                    @error('category_ids') <p class="text-rose-500 text-xs mt-2 font-bold">{{ $message }}</p> @enderror
                </div>

                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-2xl">
                    <div>
                        <div class="font-bold text-gray-800">Gate Active</div>
                        <div class="text-xs text-gray-400">Enable or disable this gate for scanning.</div>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" class="sr-only peer" {{ old('is_active', $gate->is_active) ? 'checked' : '' }}>
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-orange-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-orange-600"></div>
                    </label>
                </div>
            </div>

            <div class="flex gap-4">
                <button type="submit" class="flex-1 py-4 bg-orange-600 text-white rounded-2xl font-black text-lg hover:bg-orange-700 transition shadow-xl shadow-orange-100 uppercase tracking-widest">
                    Update Gate
                </button>
            </div>
        </form>
    </div>

    <!-- jQuery and Select2 JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.select2').select2({
                placeholder: "Select allowed categories",
                allowClear: true
            });
        });
    </script>
</x-app-layout>
