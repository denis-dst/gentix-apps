<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-black text-slate-900 font-outfit uppercase tracking-tight">
                    Basis Komunitas Suporter (Korwil) & Alokasi Kuota
                </h2>
                <p class="text-sm text-slate-500 font-medium">
                    Manajemen koordinator wilayah suporter, alokasi kuota matchday, dan monitoring persetujuan NIK rombongan.
                </p>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8" x-data="{ showKorwilModal: false, showAllocModal: false }">

            @if(session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-900 rounded-xl flex items-center gap-3 font-semibold text-sm">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    {{ session('success') }}
                </div>
            @endif

            <!-- Section 1: Daftar Korwil -->
            <div class="bg-white rounded-2xl p-6 sm:p-8 border border-slate-200 shadow-sm space-y-6">
                <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                    <div>
                        <h3 class="text-base font-black uppercase tracking-tight text-slate-900 font-outfit">
                            Daftar Korwil / Chapter Suporter Resmi
                        </h3>
                        <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Basis komunitas suporter yang terdaftar dan diakui oleh manajemen klub.</p>
                    </div>
                    <button type="button" @click="showKorwilModal = true" class="min-h-[44px] px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-xl text-xs font-bold transition focus:ring-2 focus:ring-orange-500">
                        + Daftarkan Korwil
                    </button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @forelse($korwils as $k)
                        <div class="bg-slate-50 rounded-xl p-5 border border-slate-200 space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="font-mono text-xs font-black text-orange-600">{{ $k->code }}</span>
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-900">Resmi Terverifikasi</span>
                            </div>
                            <h4 class="font-bold text-slate-900 text-base">{{ $k->name }}</h4>
                            <div class="text-xs text-slate-600 space-y-1">
                                <p>Wilayah: <strong class="text-slate-900">{{ $k->region ?: 'Semua Wilayah' }}</strong></p>
                                <p>Kontak: <strong class="text-slate-900">{{ $k->phone ?: '-' }}</strong></p>
                                <p>Anggota Terdaftar: <strong class="text-slate-900">{{ $k->members_count }} Member</strong></p>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-3 text-center py-8 text-slate-500 text-sm">
                            Belum ada Korwil suporter yang didaftarkan.
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Section 2: Alokasi Kuota Matchday -->
            <div class="bg-white rounded-2xl p-6 sm:p-8 border border-slate-200 shadow-sm space-y-6">
                <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                    <div>
                        <h3 class="text-base font-black uppercase tracking-tight text-slate-900 font-outfit">
                            Alokasi Kuota Tiket Matchday per Korwil
                        </h3>
                        <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Penetapan kuota tribun khusus rombongan suporter dengan proteksi persetujuan NIK anggota.</p>
                    </div>
                    <button type="button" @click="showAllocModal = true" class="min-h-[44px] px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-bold transition focus:ring-2 focus:ring-slate-700">
                        + Set Alokasi Match
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-600">
                        <thead class="bg-slate-50 text-slate-700 text-xs uppercase font-black tracking-wider border-b border-slate-200">
                            <tr>
                                <th class="px-6 py-4">Korwil</th>
                                <th class="px-6 py-4">Pertandingan</th>
                                <th class="px-6 py-4">Tribun</th>
                                <th class="px-6 py-4">Alokasi Kuota</th>
                                <th class="px-6 py-4">Persetujuan NIK Anggota</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($allocations as $a)
                                <tr class="hover:bg-slate-50 transition">
                                    <td class="px-6 py-4 font-bold text-slate-900">{{ $a->korwil->name }}</td>
                                    <td class="px-6 py-4">{{ $a->event->name }}</td>
                                    <td class="px-6 py-4">{{ $a->category->name }}</td>
                                    <td class="px-6 py-4 font-bold text-slate-900">{{ $a->used_quota }} / {{ $a->allocated_quota }} Tiket</td>
                                    <td class="px-6 py-4 text-xs">
                                        <span class="text-emerald-700 font-bold">{{ $a->memberConsents->where('consent_status', 'approved_by_member')->count() }} Disetujui</span> · 
                                        <span class="text-amber-800 font-semibold">{{ $a->memberConsents->where('consent_status', 'pending')->count() }} Pending</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-8 text-center text-slate-500">
                                        Belum ada alokasi kuota matchday untuk Korwil.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Modal Tambah Korwil -->
            <div x-show="showKorwilModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/80 backdrop-blur-sm" x-cloak>
                <div class="bg-white rounded-2xl p-6 sm:p-8 max-w-lg w-full space-y-4 shadow-2xl">
                    <h3 class="text-base font-black uppercase text-slate-900 font-outfit">Daftarkan Korwil Suporter</h3>
                    <form action="{{ route('organizer.korwil.store-korwil') }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label for="korwil_name" class="block text-xs font-bold uppercase text-slate-700 mb-1">Nama Korwil / Basis</label>
                            <input type="text" id="korwil_name" name="name" required placeholder="Contoh: Korwil Jayapura Barat" class="w-full rounded-xl border-slate-300 text-xs font-semibold py-2.5">
                        </div>
                        <div>
                            <label for="korwil_code" class="block text-xs font-bold uppercase text-slate-700 mb-1">Kode / Singkatan</label>
                            <input type="text" id="korwil_code" name="code" placeholder="Contoh: KRW-JPR" class="w-full rounded-xl border-slate-300 text-xs font-semibold py-2.5">
                        </div>
                        <div>
                            <label for="korwil_region" class="block text-xs font-bold uppercase text-slate-700 mb-1">Wilayah / Regional</label>
                            <input type="text" id="korwil_region" name="region" placeholder="Contoh: Papua" class="w-full rounded-xl border-slate-300 text-xs font-semibold py-2.5">
                        </div>
                        <div>
                            <label for="korwil_phone" class="block text-xs font-bold uppercase text-slate-700 mb-1">No. WhatsApp Koordinator</label>
                            <input type="text" id="korwil_phone" name="phone" placeholder="08xxxxxxxx" class="w-full rounded-xl border-slate-300 text-xs font-semibold py-2.5">
                        </div>
                        <div class="flex justify-end gap-2 pt-4 border-t border-slate-100">
                            <button type="button" @click="showKorwilModal = false" class="min-h-[44px] px-4 py-2 bg-slate-100 text-slate-700 rounded-xl text-xs font-bold">Batal</button>
                            <button type="submit" class="min-h-[44px] px-5 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-xl text-xs font-bold shadow transition">Daftarkan</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Modal Set Alokasi -->
            <div x-show="showAllocModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/80 backdrop-blur-sm" x-cloak>
                <div class="bg-white rounded-2xl p-6 sm:p-8 max-w-lg w-full space-y-4 shadow-2xl">
                    <h3 class="text-base font-black uppercase text-slate-900 font-outfit">Set Alokasi Tiket Matchday</h3>
                    <form action="{{ route('organizer.korwil.store-allocation') }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label for="alloc_korwil_id" class="block text-xs font-bold uppercase text-slate-700 mb-1">Pilih Korwil</label>
                            <select id="alloc_korwil_id" name="korwil_id" required class="w-full rounded-xl border-slate-300 text-xs font-semibold py-2.5">
                                @foreach($korwils as $k)
                                    <option value="{{ $k->id }}">{{ $k->name }} ({{ $k->code }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="alloc_event_id" class="block text-xs font-bold uppercase text-slate-700 mb-1">Pertandingan</label>
                            <select id="alloc_event_id" name="event_id" required class="w-full rounded-xl border-slate-300 text-xs font-semibold py-2.5">
                                @foreach($events as $e)
                                    <option value="{{ $e->id }}">{{ $e->name }} ({{ $e->event_start_date->format('d M Y') }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="alloc_category_id" class="block text-xs font-bold uppercase text-slate-700 mb-1">Kategori Tribun</label>
                            <select id="alloc_category_id" name="ticket_category_id" required class="w-full rounded-xl border-slate-300 text-xs font-semibold py-2.5">
                                @foreach($categories as $c)
                                    <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->event->name ?? '' }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label for="allocated_quota" class="block text-xs font-bold uppercase text-slate-700 mb-1">Alokasi Kuota Tiket</label>
                                <input type="number" id="allocated_quota" name="allocated_quota" value="50" min="1" required class="w-full rounded-xl border-slate-300 text-xs font-semibold py-2.5">
                            </div>
                            <div>
                                <label for="special_price" class="block text-xs font-bold uppercase text-slate-700 mb-1">Harga Khusus (Opsional)</label>
                                <input type="number" id="special_price" name="special_price" placeholder="Default tarif" class="w-full rounded-xl border-slate-300 text-xs font-semibold py-2.5">
                            </div>
                        </div>
                        <div class="flex justify-end gap-2 pt-4 border-t border-slate-100">
                            <button type="button" @click="showAllocModal = false" class="min-h-[44px] px-4 py-2 bg-slate-100 text-slate-700 rounded-xl text-xs font-bold">Batal</button>
                            <button type="submit" class="min-h-[44px] px-5 py-2 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-bold transition">Tetapkan Alokasi</button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
