<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('organizer.season-passes.index') }}" class="p-2 text-slate-400 hover:text-slate-700 bg-white border border-slate-200 rounded-xl transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div>
                <h2 class="text-2xl font-black text-slate-900 font-outfit uppercase tracking-tight">
                    Terbitkan Tiket Terusan (Season Pass)
                </h2>
                <p class="text-sm text-slate-500 font-medium">Buat paket tiket 1 musim penuh atau setengah musim untuk suporter terdaftar.</p>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <form action="{{ route('organizer.season-passes.store') }}" method="POST" class="bg-white rounded-3xl p-8 border border-slate-200/80 shadow-sm space-y-6">
                @csrf

                <div>
                    <label class="block text-xs font-black uppercase tracking-wider text-slate-700 mb-2">Pilih Suporter / Member <span class="text-rose-500">*</span></label>
                    <select name="tenant_member_id" required class="w-full rounded-xl border-slate-200 focus:border-orange-500 focus:ring-orange-500 text-sm font-semibold">
                        <option value="">-- Pilih Suporter Terdaftar --</option>
                        @foreach($members as $m)
                            <option value="{{ $m->id }}" {{ old('tenant_member_id') == $m->id ? 'selected' : '' }}>
                                {{ $m->member_number }} - {{ $m->full_name_ktp ?: $m->user->name }} (NIK: {{ $m->nik ?: '-' }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-black uppercase tracking-wider text-slate-700 mb-2">Nama Paket Season Pass <span class="text-rose-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name', 'Tiket Terusan 1 Musim Penuh') }}" required class="w-full rounded-xl border-slate-200 focus:border-orange-500 focus:ring-orange-500 text-sm font-semibold">
                    </div>

                    <div>
                        <label class="block text-xs font-black uppercase tracking-wider text-slate-700 mb-2">Nama Musim / Kompetisi <span class="text-rose-500">*</span></label>
                        <input type="text" name="season_name" value="{{ old('season_name', 'Liga 1 2026/2027') }}" required class="w-full rounded-xl border-slate-200 focus:border-orange-500 focus:ring-orange-500 text-sm font-semibold">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-xs font-black uppercase tracking-wider text-slate-700 mb-2">Tipe Paket <span class="text-rose-500">*</span></label>
                        <select name="pass_type" required class="w-full rounded-xl border-slate-200 focus:border-orange-500 focus:ring-orange-500 text-sm font-semibold">
                            <option value="full_season">1 Musim Penuh (Full Season)</option>
                            <option value="half_season_1">Setengah Musim (Putaran 1)</option>
                            <option value="half_season_2">Setengah Musim (Putaran 2)</option>
                            <option value="custom_bundle">Paket Khusus / Multi-Match</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-black uppercase tracking-wider text-slate-700 mb-2">Total Kuota Pertandingan <span class="text-rose-500">*</span></label>
                        <input type="number" name="total_matches" value="{{ old('total_matches', 17) }}" min="1" max="50" required class="w-full rounded-xl border-slate-200 focus:border-orange-500 focus:ring-orange-500 text-sm font-semibold">
                    </div>

                    <div>
                        <label class="block text-xs font-black uppercase tracking-wider text-slate-700 mb-2">Jendela Buka Klaim (H-X Hari) <span class="text-rose-500">*</span></label>
                        <input type="number" name="claim_window_days_before" value="{{ old('claim_window_days_before', 5) }}" min="1" max="30" required class="w-full rounded-xl border-slate-200 focus:border-orange-500 focus:ring-orange-500 text-sm font-semibold">
                        <p class="text-[11px] text-slate-400 mt-1">Hari link e-ticket dibuka sebelum kick-off.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-xs font-black uppercase tracking-wider text-slate-700 mb-2">Kategori Tribun Default</label>
                        <select name="ticket_category_id" class="w-full rounded-xl border-slate-200 focus:border-orange-500 focus:ring-orange-500 text-sm font-semibold">
                            <option value="">-- Fleksibel / Semua Tribun --</option>
                            @foreach($categories as $c)
                                <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->event->name ?? '' }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-black uppercase tracking-wider text-slate-700 mb-2">Nomor Kursi (Seat Number)</label>
                        <input type="text" name="seat_number" value="{{ old('seat_number') }}" placeholder="e.g. VIP-A12 (opsional)" class="w-full rounded-xl border-slate-200 focus:border-orange-500 focus:ring-orange-500 text-sm font-semibold">
                    </div>

                    <div>
                        <label class="block text-xs font-black uppercase tracking-wider text-slate-700 mb-2">Harga Paket Terusan (Rp) <span class="text-rose-500">*</span></label>
                        <input type="number" name="price_paid" value="{{ old('price_paid', 1200000) }}" min="0" step="5000" required class="w-full rounded-xl border-slate-200 focus:border-orange-500 focus:ring-orange-500 text-sm font-semibold">
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-6 border-t border-slate-100">
                    <a href="{{ route('organizer.season-passes.index') }}" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-sm font-bold transition">
                        Batal
                    </a>
                    <button type="submit" class="px-6 py-2.5 bg-orange-600 hover:bg-orange-700 text-white rounded-xl text-sm font-bold shadow-lg shadow-orange-500/20 transition">
                        Terbitkan Pass
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
