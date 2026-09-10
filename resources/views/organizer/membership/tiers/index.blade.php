<x-app-layout>
    <x-slot name="title">Tingkatan Keanggotaan (Membership Tiers)</x-slot>
    <x-slot name="header">Tingkatan Keanggotaan (Membership Tiers)</x-slot>
    <x-slot name="actions">
        <a href="{{ route('organizer.membership.tiers.create') }}" class="min-h-[44px] inline-flex items-center gap-2 px-5 py-2.5 bg-orange-600 hover:bg-orange-700 text-white rounded-xl text-sm font-bold shadow transition focus:ring-2 focus:ring-orange-500">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Tambah Tier Baru
        </a>
    </x-slot>

    <div class="space-y-6">

            @if(session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-900 rounded-xl flex items-center gap-3 font-semibold text-sm">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    {{ session('success') }}
                </div>
            @endif

            <!-- Public Fan Registration URL Box -->
            @php $tenantSlug = Auth::user()->tenant->slug ?? 'club'; @endphp
            <div class="bg-slate-900 text-white rounded-2xl p-5 border border-slate-800 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <span class="px-2.5 py-0.5 rounded-full bg-orange-600/20 text-orange-400 text-[10px] font-bold uppercase tracking-wider">Tautan Pendaftaran Suporter</span>
                    <p class="text-xs sm:text-sm text-slate-300 mt-1">Bagikan link ini di media sosial klub agar suporter dapat mendaftar langsung menjadi anggota resmi.</p>
                </div>
                <div class="flex items-center gap-2 w-full md:w-auto">
                    <input type="text" readonly value="{{ url('/join/' . $tenantSlug) }}" id="joinLinkInput" class="bg-slate-950 border border-slate-700 text-white text-xs font-mono rounded-xl px-3 py-2.5 w-full md:w-80 select-all focus:ring-2 focus:ring-orange-500">
                    <button type="button" onclick="navigator.clipboard.writeText(document.getElementById('joinLinkInput').value); alert('Tautan pendaftaran berhasil disalin!');" class="min-h-[44px] px-4 py-2.5 bg-orange-600 hover:bg-orange-700 text-white rounded-xl text-xs font-bold shrink-0 transition focus:ring-2 focus:ring-orange-500">
                        Salin
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @forelse($tiers as $tier)
                    <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between mb-4">
                                <span class="px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider text-white" style="background-color: {{ $tier->badge_color ?: '#f97316' }}">
                                    {{ $tier->name }}
                                </span>
                                <span class="text-xs font-bold {{ $tier->is_active ? 'text-emerald-700' : 'text-slate-500' }}">
                                    {{ $tier->is_active ? '● Aktif' : '○ Nonaktif' }}
                                </span>
                            </div>

                            <div class="mb-4">
                                <span class="text-3xl font-black text-slate-900 font-outfit">
                                    Rp {{ number_format($tier->price, 0, ',', '.') }}
                                </span>
                                <span class="text-xs text-slate-500 font-semibold"> / {{ $tier->validity_days }} Hari</span>
                            </div>

                            @if($tier->description)
                                <p class="text-xs text-slate-600 font-medium mb-4 leading-relaxed">{{ $tier->description }}</p>
                            @endif

                            <div class="space-y-2 border-t border-slate-100 pt-4 mb-6">
                                <div class="flex items-center gap-2 text-xs font-semibold text-slate-700">
                                    <svg class="w-4 h-4 text-orange-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    Early Access Tiket: <strong class="text-slate-900">{{ $tier->early_access_hours }} Jam</strong> sebelum umum
                                </div>
                                <div class="flex items-center gap-2 text-xs font-semibold text-slate-700">
                                    <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                                    Diskon Tiket: <strong class="text-emerald-700">{{ $tier->ticket_discount_percent }}%</strong>
                                </div>
                                <div class="flex items-center gap-2 text-xs font-semibold text-slate-700">
                                    <svg class="w-4 h-4 text-slate-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                    Jumlah Member: <strong class="text-slate-900">{{ $tier->members_count }} Suporter</strong>
                                </div>

                                @if($tier->benefits && count($tier->benefits) > 0)
                                    <div class="pt-2">
                                        <p class="text-[11px] font-bold uppercase text-slate-500 tracking-wider mb-1.5">Fasilitas Member:</p>
                                        <ul class="space-y-1">
                                            @foreach($tier->benefits as $b)
                                                <li class="flex items-center gap-1.5 text-xs text-slate-700">
                                                    <svg class="w-3.5 h-3.5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                                    {{ $b }}
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="flex items-center gap-2 border-t border-slate-100 pt-4">
                            <a href="{{ route('organizer.membership.tiers.edit', $tier) }}" class="min-h-[44px] flex-1 inline-flex items-center justify-center text-center py-2 bg-slate-100 hover:bg-slate-200 text-slate-800 rounded-xl text-xs font-bold transition focus:ring-2 focus:ring-slate-400">
                                Edit Tier
                            </a>
                            <form action="{{ route('organizer.membership.tiers.destroy', $tier) }}" method="POST" onsubmit="return confirm('Hapus tier membership ini?');" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="min-h-[44px] min-w-[44px] inline-flex items-center justify-center p-2.5 text-rose-600 hover:bg-rose-50 rounded-xl transition focus:ring-2 focus:ring-rose-500" title="Hapus">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="col-span-3 bg-white rounded-2xl p-12 text-center border border-slate-200">
                        <div class="w-16 h-16 bg-slate-100 text-slate-600 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        </div>
                        <h3 class="text-lg font-bold text-slate-900 font-outfit mb-1">Belum Ada Tier Membership</h3>
                        <p class="text-sm text-slate-500 mb-6 max-w-sm mx-auto">Buat tingkatan keanggotaan klub seperti Free Supporter, Silver, Gold, atau VIP Member.</p>
                        <a href="{{ route('organizer.membership.tiers.create') }}" class="min-h-[44px] inline-flex items-center gap-2 px-5 py-2.5 bg-orange-600 hover:bg-orange-700 text-white rounded-xl text-sm font-bold shadow transition focus:ring-2 focus:ring-orange-500">
                            Tambah Tier Pertama
                        </a>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
