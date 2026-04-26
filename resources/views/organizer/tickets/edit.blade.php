<x-app-layout>
    <x-slot name="title">Edit Category: {{ $category->name }} - {{ $event->name }}</x-slot>

    <div class="max-w-4xl mx-auto space-y-8 animate-in fade-in slide-in-from-bottom-4 duration-700">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-3xl font-black text-gray-800">Edit Ticket Category</h2>
                <p class="text-gray-500 font-medium">Updating {{ $category->name }} for {{ $event->name }}.</p>
            </div>
            <a href="{{ route('organizer.events.edit', $event) }}" class="text-sm font-bold text-gray-400 hover:text-gray-600 transition flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                Back to Event
            </a>
        </div>

        <form action="{{ route('organizer.events.categories.update', [$event, $category]) }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
            @csrf
            @method('PUT')
            <div class="p-8 space-y-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Basic Info -->
                    <div class="space-y-6">
                        <h3 class="text-lg font-bold text-gray-800 border-b border-gray-50 pb-2">Basic Information</h3>
                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Category Name</label>
                            <input type="text" name="name" value="{{ old('name', $category->name) }}" required class="w-full rounded-xl border-gray-200 focus:border-purple-500 focus:ring-purple-500 transition px-4 py-3">
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Price (IDR)</label>
                                <input type="number" name="price" value="{{ old('price', (int)$category->price) }}" required class="w-full rounded-xl border-gray-200 focus:border-purple-500 focus:ring-purple-500 transition px-4 py-3">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Total Quota</label>
                                <input type="number" name="quota" value="{{ old('quota', $category->quota) }}" required class="w-full rounded-xl border-gray-200 focus:border-purple-500 focus:ring-purple-500 transition px-4 py-3">
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Theme Color</label>
                            <div class="flex items-center gap-4">
                                <input type="color" name="hex_color" value="{{ old('hex_color', $category->hex_color ?? '#6366F1') }}" class="w-16 h-12 rounded-xl border-gray-200 p-1 cursor-pointer">
                                <span class="text-sm text-gray-500">Update the color theme for this tier.</span>
                            </div>
                        </div>

                        <div class="pt-4 border-t border-gray-50 space-y-4">
                            <div>
                                <label class="block text-xs font-bold text-purple-600 uppercase tracking-wider mb-2">Pembatasan NIK (Opsional)</label>
                                <input type="text" name="nik_restriction" value="{{ old('nik_restriction', $category->nik_restriction) }}" placeholder="Contoh: 18 (untuk Lampung)" class="w-full rounded-xl border-gray-200 focus:border-purple-500 focus:ring-purple-500 transition px-4 py-3 bg-purple-50/30">
                                <p class="mt-2 text-[10px] text-gray-500 italic leading-relaxed">
                                    Masukkan 2-4 digit awal NIK untuk membatasi area pembeli. Pisahkan dengan koma jika lebih dari satu (misal: 18, 31).
                                </p>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Pesan Error Kustom</label>
                                <input type="text" name="nik_restriction_message" value="{{ old('nik_restriction_message', $category->nik_restriction_message) }}" placeholder="Contoh: Maaf, tiket ini khusus KTP Lampung" class="w-full rounded-xl border-gray-200 focus:border-purple-500 focus:ring-purple-500 transition px-4 py-3">
                                <p class="mt-2 text-[10px] text-gray-500 italic">
                                    Pesan yang muncul jika NIK pembeli tidak sesuai. Kosongkan untuk menggunakan pesan default.
                                </p>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Teks Badge (Keterangan Tiket)</label>
                                <input type="text" name="badge_text" value="{{ old('badge_text', $category->badge_text) }}" placeholder="Contoh: Khusus Loyal Supporters" class="w-full rounded-xl border-gray-200 focus:border-purple-500 focus:ring-purple-500 transition px-4 py-3">
                                <p class="mt-2 text-[10px] text-gray-500 italic">
                                    Label teks kecil yang muncul di bawah harga tiket (misal: "Promo Terbatas", "Khusus Member").
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Scheduling & Design -->
                    <div class="space-y-6">
                        <h3 class="text-lg font-bold text-gray-800 border-b border-gray-50 pb-2">Scheduling & Design</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Sale Start</label>
                                <input type="datetime-local" name="sale_start_at" value="{{ old('sale_start_at', $category->sale_start_at ? $category->sale_start_at->format('Y-m-d\TH:i') : '') }}" class="w-full rounded-xl border-gray-200 focus:border-purple-500 focus:ring-purple-500 transition px-4 py-3">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Sale End</label>
                                <input type="datetime-local" name="sale_end_at" value="{{ old('sale_end_at', $category->sale_end_at ? $category->sale_end_at->format('Y-m-d\TH:i') : '') }}" class="w-full rounded-xl border-gray-200 focus:border-purple-500 focus:ring-purple-500 transition px-4 py-3">
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">E-Voucher Background</label>
                            @if($category->background_image)
                                <div class="mb-4 relative group">
                                    <img src="{{ Storage::url($category->background_image) }}" class="w-full h-32 object-cover rounded-2xl shadow-sm">
                                    <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition rounded-2xl flex items-center justify-center text-white text-xs font-bold">Current Background</div>
                                </div>
                            @endif
                            <input type="file" name="background_image" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-bold file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100 transition cursor-pointer">
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-gray-50/50 p-8 flex justify-end gap-4 border-t border-gray-100">
                <a href="{{ route('organizer.events.edit', $event) }}" class="px-8 py-3 bg-white border border-gray-200 text-gray-600 rounded-xl font-bold hover:bg-gray-100 transition">Cancel</a>
                <button type="submit" class="px-10 py-3 bg-purple-600 text-white rounded-xl font-bold hover:bg-purple-700 transition shadow-lg shadow-purple-200">
                    Update Category
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
