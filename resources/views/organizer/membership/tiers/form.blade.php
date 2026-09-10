<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('organizer.membership.tiers.index') }}" class="p-2 text-slate-400 hover:text-slate-700 bg-white border border-slate-200 rounded-xl transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div>
                <h2 class="text-2xl font-black text-slate-900 font-outfit uppercase tracking-tight">
                    {{ isset($tier) ? 'Edit Tier: ' . $tier->name : 'Tambah Tier Membership Baru' }}
                </h2>
                <p class="text-sm text-slate-500 font-medium">Konfigurasi harga, durasi, dan keuntungan keanggotaan klub.</p>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <form action="{{ isset($tier) ? route('organizer.membership.tiers.update', $tier) : route('organizer.membership.tiers.store') }}" method="POST" class="bg-white rounded-3xl p-8 border border-slate-200/80 shadow-sm space-y-6">
                @csrf
                @if(isset($tier))
                    @method('PUT')
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-black uppercase tracking-wider text-slate-700 mb-2">Nama Tier <span class="text-rose-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $tier->name ?? '') }}" placeholder="e.g. Member Silver / Gold / VIP" required class="w-full rounded-xl border-slate-200 focus:border-orange-500 focus:ring-orange-500 text-sm font-semibold">
                    </div>

                    <div>
                        <label class="block text-xs font-black uppercase tracking-wider text-slate-700 mb-2">Warna Label / Badge</label>
                        <div class="flex items-center gap-3">
                            <input type="color" name="badge_color" value="{{ old('badge_color', $tier->badge_color ?? '#f97316') }}" class="h-10 w-16 rounded-xl border-slate-200 p-1 cursor-pointer">
                            <input type="text" name="badge_color_text" value="{{ old('badge_color', $tier->badge_color ?? '#f97316') }}" readonly class="flex-1 rounded-xl border-slate-200 bg-slate-50 text-xs font-mono">
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-xs font-black uppercase tracking-wider text-slate-700 mb-2">Biaya Keanggotaan (Rp) <span class="text-rose-500">*</span></label>
                        <input type="number" name="price" value="{{ old('price', $tier->price ?? 0) }}" min="0" step="1000" placeholder="0 jika gratis" required class="w-full rounded-xl border-slate-200 focus:border-orange-500 focus:ring-orange-500 text-sm font-semibold">
                    </div>

                    <div>
                        <label class="block text-xs font-black uppercase tracking-wider text-slate-700 mb-2">Masa Aktif (Hari) <span class="text-rose-500">*</span></label>
                        <input type="number" name="validity_days" value="{{ old('validity_days', $tier->validity_days ?? 365) }}" min="1" required class="w-full rounded-xl border-slate-200 focus:border-orange-500 focus:ring-orange-500 text-sm font-semibold">
                    </div>

                    <div>
                        <label class="block text-xs font-black uppercase tracking-wider text-slate-700 mb-2">Diskon Tiket (%)</label>
                        <input type="number" name="ticket_discount_percent" value="{{ old('ticket_discount_percent', $tier->ticket_discount_percent ?? 0) }}" min="0" max="100" step="0.5" class="w-full rounded-xl border-slate-200 focus:border-orange-500 focus:ring-orange-500 text-sm font-semibold">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-black uppercase tracking-wider text-slate-700 mb-2">Early Access Tiket (Jam Lebih Awal)</label>
                        <input type="number" name="early_access_hours" value="{{ old('early_access_hours', $tier->early_access_hours ?? 48) }}" min="0" placeholder="e.g. 48 (2 hari sebelum general sale)" class="w-full rounded-xl border-slate-200 focus:border-orange-500 focus:ring-orange-500 text-sm font-semibold">
                        <p class="text-[11px] text-slate-400 mt-1">Berapa jam sebelum General Public Sale tiket dibuka khusus tier ini.</p>
                    </div>

                    <div class="flex items-center pt-6">
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $tier->is_active ?? true) ? 'checked' : '' }} class="w-5 h-5 rounded text-orange-600 focus:ring-orange-500 border-slate-300">
                            <span class="text-sm font-bold text-slate-800">Aktifkan Tier Ini</span>
                        </label>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-black uppercase tracking-wider text-slate-700 mb-2">Deskripsi Ringkas</label>
                    <textarea name="description" rows="3" placeholder="Jelaskan mengenai tier ini..." class="w-full rounded-xl border-slate-200 focus:border-orange-500 focus:ring-orange-500 text-sm">{{ old('description', $tier->description ?? '') }}</textarea>
                </div>

                <div x-data="{ benefits: {{ json_encode(old('benefits', $tier->benefits ?? ['Kartu Digital Member', 'Diskon Tiket Match Kandang'])) }} }">
                    <div class="flex items-center justify-between mb-2">
                        <label class="block text-xs font-black uppercase tracking-wider text-slate-700">Daftar Benefit / Fasilitas Member</label>
                        <button type="button" @click="benefits.push('')" class="text-xs font-bold text-orange-600 hover:text-orange-700 flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            Tambah Benefit
                        </button>
                    </div>

                    <div class="space-y-2">
                        <template x-for="(benefit, index) in benefits" :key="index">
                            <div class="flex items-center gap-2">
                                <input type="text" :name="'benefits['+index+']'" x-model="benefits[index]" placeholder="e.g. Diskon 10% Official Store Merchandise" class="flex-1 rounded-xl border-slate-200 text-sm font-medium">
                                <button type="button" @click="benefits.splice(index, 1)" class="p-2.5 text-rose-500 hover:bg-rose-50 rounded-xl transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </template>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-6 border-t border-slate-100">
                    <a href="{{ route('organizer.membership.tiers.index') }}" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-sm font-bold transition">
                        Batal
                    </a>
                    <button type="submit" class="px-6 py-2.5 bg-orange-600 hover:bg-orange-700 text-white rounded-xl text-sm font-bold shadow-lg shadow-orange-500/20 transition">
                        Simpan Tier
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
