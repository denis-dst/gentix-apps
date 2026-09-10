<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-black text-slate-900 font-outfit uppercase tracking-tight">
                    Tiket Terusan (Season Pass) 1 Musim / Setengah Musim
                </h2>
                <p class="text-sm text-slate-500 font-medium">
                    Penerbitan pass suporter, pengaturan jendela klaim tiket (H-X hari sebelum kick-off), dan monitoring pemakaian.
                </p>
            </div>
            <a href="{{ route('organizer.season-passes.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-orange-600 hover:bg-orange-700 text-white rounded-xl text-sm font-bold shadow-lg shadow-orange-500/20 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Terbitkan Season Pass
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

            <!-- Stats Counters -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm">
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Season Pass Terbit</p>
                    <p class="text-2xl font-black text-slate-900 font-outfit mt-1">{{ number_format($stats['total_passes']) }}</p>
                </div>
                <div class="bg-white rounded-2xl p-5 border border-emerald-200/80 shadow-sm bg-emerald-50/20">
                    <p class="text-xs font-bold text-emerald-600 uppercase tracking-wider">Pass Aktif</p>
                    <p class="text-2xl font-black text-emerald-700 font-outfit mt-1">{{ number_format($stats['active_passes']) }}</p>
                </div>
                <div class="bg-white rounded-2xl p-5 border border-orange-200/80 shadow-sm bg-orange-50/20">
                    <p class="text-xs font-bold text-orange-600 uppercase tracking-wider">Total Tiket Match Terklaim</p>
                    <p class="text-2xl font-black text-orange-700 font-outfit mt-1">{{ number_format($stats['total_claims']) }} Match</p>
                </div>
            </div>

            <!-- Table Pass -->
            <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <h3 class="text-base font-black uppercase tracking-tight text-slate-900 font-outfit">
                        Daftar Pemegang Season Pass
                    </h3>
                    <form method="GET" class="flex gap-2">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Kode Pass, Nama, atau NIK..." class="rounded-xl border-slate-200 text-xs font-semibold">
                        <button type="submit" class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-bold transition">
                            Cari
                        </button>
                    </form>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-600">
                        <thead class="bg-slate-50 text-slate-700 text-xs uppercase font-black tracking-wider border-b border-slate-200">
                            <tr>
                                <th class="px-6 py-4">Kode Pass & Paket</th>
                                <th class="px-6 py-4">Suporter / NIK</th>
                                <th class="px-6 py-4">Kategori Tribun</th>
                                <th class="px-6 py-4">Kuota Match</th>
                                <th class="px-6 py-4">Jendela Klaim</th>
                                <th class="px-6 py-4">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($passes as $p)
                                <tr class="hover:bg-slate-50/80 transition">
                                    <td class="px-6 py-4">
                                        <p class="font-mono text-xs font-black text-orange-600">{{ $p->pass_code }}</p>
                                        <p class="font-bold text-slate-900 text-sm mt-0.5">{{ $p->name }}</p>
                                        <p class="text-[11px] text-slate-400 font-semibold">{{ $p->season_name }} • Rp {{ number_format($p->price_paid, 0, ',', '.') }}</p>
                                    </td>
                                    <td class="px-6 py-4">
                                        <p class="font-bold text-slate-900">{{ $p->member->full_name_ktp ?: $p->user->name }}</p>
                                        <p class="text-xs text-slate-400 font-mono">NIK: {{ $p->member->nik ?: '-' }}</p>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="px-2.5 py-1 rounded-lg bg-slate-100 text-slate-800 text-xs font-bold">
                                            {{ $p->category->name ?? 'Semua Tribun' }}
                                        </span>
                                        @if($p->seat_number)
                                            <div class="text-xs text-slate-500 font-semibold mt-1">Seat: {{ $p->seat_number }}</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-2">
                                            <span class="font-black text-slate-900 text-sm">{{ $p->claimed_matches }} / {{ $p->total_matches }}</span>
                                            <span class="text-xs text-slate-400 font-semibold">Terklaim</span>
                                        </div>
                                        <div class="w-24 bg-slate-100 rounded-full h-1.5 mt-1 overflow-hidden">
                                            @php $pct = $p->total_matches > 0 ? min(100, round(($p->claimed_matches / $p->total_matches) * 100)) : 0; @endphp
                                            <div class="bg-orange-500 h-full rounded-full" style="width: {{ $pct }}%"></div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-xs font-semibold text-slate-700">
                                        H-{{ $p->claim_window_days_before }} Hari Sebelum Match
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($p->status === 'active')
                                            <span class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">● Aktif</span>
                                        @else
                                            <span class="px-3 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-600">{{ ucfirst($p->status) }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center text-slate-400 font-medium">
                                        Belum ada Season Pass yang diterbitkan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($passes->hasPages())
                    <div class="p-6 border-t border-slate-100">
                        {{ $passes->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
