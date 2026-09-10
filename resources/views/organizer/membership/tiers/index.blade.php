<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-black text-slate-900 font-outfit uppercase tracking-tight">
                    Tingkatan Keanggotaan (Membership Tiers)
                </h2>
                <p class="text-sm text-slate-500 font-medium">
                    Atur kategori membership suporter, benefit diskon tiket, dan masa aktif keanggotaan klub.
                </p>
            </div>
            <a href="{{ route('organizer.membership.tiers.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-orange-600 hover:bg-orange-700 text-white rounded-xl text-sm font-bold shadow-lg shadow-orange-500/20 transition duration-150">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Tambah Tier Baru
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl flex items-center gap-3 font-semibold text-sm">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    {{ session('success') }}
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @forelse($tiers as $tier)
                    <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm hover:shadow-md transition relative flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between mb-4">
                                <span class="px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider text-white" style="background-color: {{ $tier->badge_color ?: '#f97316' }}">
                                    {{ $tier->name }}
                                </span>
                                <span class="text-xs font-bold {{ $tier->is_active ? 'text-emerald-600' : 'text-slate-400' }}">
                                    {{ $tier->is_active ? '● Aktif' : '○ Nonaktif' }}
                                </span>
                            </div>

                            <div class="mb-4">
                                <span class="text-3xl font-black text-slate-900 font-outfit">
                                    Rp {{ number_format($tier->price, 0, ',', '.') }}
                                </span>
                                <span class="text-xs text-slate-400 font-semibold"> / {{ $tier->validity_days }} Hari</span>
                            </div>

                            @if($tier->description)
                                <p class="text-xs text-slate-500 font-medium mb-4 leading-relaxed">{{ $tier->description }}</p>
                            @endif

                            <div class="space-y-2 border-t border-slate-100 pt-4 mb-6">
                                <div class="flex items-center gap-2 text-xs font-semibold text-slate-700">
                                    <svg class="w-4 h-4 text-orange-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    Early Access Tiket: <strong class="text-slate-900">{{ $tier->early_access_hours }} Jam</strong> sebelum umum
                                </div>
                                <div class="flex items-center gap-2 text-xs font-semibold text-slate-700">
                                    <svg class="w-4 h-4 text-orange-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                                    Diskon Tiket: <strong class="text-emerald-600">{{ $tier->ticket_discount_percent }}%</strong>
                                </div>
                                <div class="flex items-center gap-2 text-xs font-semibold text-slate-700">
                                    <svg class="w-4 h-4 text-orange-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                    Jumlah Member: <strong class="text-slate-900">{{ $tier->members_count }} Suporter</strong>
                                </div>

                                @if($tier->benefits && count($tier->benefits) > 0)
                                    <div class="pt-2">
                                        <p class="text-[11px] font-black uppercase text-slate-400 tracking-wider mb-1.5">Perks & Benefit:</p>
                                        <ul class="space-y-1">
                                            @foreach($tier->benefits as $b)
                                                <li class="flex items-center gap-1.5 text-xs text-slate-600">
                                                    <svg class="w-3.5 h-3.5 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                                    {{ $b }}
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="flex items-center gap-2 border-t border-slate-100 pt-4">
                            <a href="{{ route('organizer.membership.tiers.edit', $tier) }}" class="flex-1 text-center py-2 bg-slate-100 hover:bg-slate-200 text-slate-800 rounded-xl text-xs font-bold transition">
                                Edit Tier
                            </a>
                            <form action="{{ route('organizer.membership.tiers.destroy', $tier) }}" method="POST" onsubmit="return confirm('Hapus tier membership ini?');" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 text-rose-500 hover:bg-rose-50 rounded-xl transition" title="Hapus">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="col-span-3 bg-white rounded-3xl p-12 text-center border border-slate-200">
                        <div class="w-16 h-16 bg-orange-50 text-orange-600 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        </div>
                        <h3 class="text-lg font-bold text-slate-800 font-outfit mb-1">Belum Ada Tier Membership</h3>
                        <p class="text-sm text-slate-500 mb-6 max-w-sm mx-auto">Buat tingkatan keanggotaan klub seperti Free Supporter, Silver, Gold, atau VIP Member.</p>
                        <a href="{{ route('organizer.membership.tiers.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-orange-600 hover:bg-orange-700 text-white rounded-xl text-sm font-bold shadow-lg shadow-orange-500/20 transition">
                            Tambah Tier Pertama
                        </a>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
