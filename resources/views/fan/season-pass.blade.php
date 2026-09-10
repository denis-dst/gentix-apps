<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('fan.dashboard') }}" class="min-h-[44px] min-w-[44px] inline-flex items-center justify-center p-2 text-slate-500 hover:text-slate-800 bg-white border border-slate-200 rounded-xl transition focus:ring-2 focus:ring-orange-500">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div>
                <h2 class="text-2xl font-black text-slate-900 font-outfit uppercase tracking-tight">
                    Tiket Terusan (Season Pass)
                </h2>
                <p class="text-sm text-slate-500 font-medium">Akses tiket seluruh pertandingan kandang resmi klub selama 1 musim penuh.</p>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

            @if(session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-900 rounded-2xl flex items-center gap-3 font-semibold text-sm">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="p-4 bg-rose-50 border border-rose-200 text-rose-900 rounded-2xl flex items-center gap-3 font-semibold text-sm">
                    <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    {{ session('error') }}
                </div>
            @endif

            <!-- Season Pass List -->
            @forelse($seasonPasses as $sp)
                <div class="bg-white rounded-2xl p-6 sm:p-8 border border-slate-200 shadow-sm space-y-6">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-slate-100 pb-6">
                        <div class="space-y-1">
                            <span class="px-3 py-1 bg-orange-100 text-orange-900 rounded-full text-xs font-black uppercase tracking-wider">
                                {{ $sp->season_name }} · {{ $sp->pass_type === 'full_season' ? '1 Musim Penuh' : 'Setengah Musim' }}
                            </span>
                            <h3 class="text-xl font-black text-slate-900 font-outfit mt-2">{{ $sp->name }}</h3>
                            <p class="text-xs font-mono text-slate-500">Kode Pass: <strong class="text-slate-900">{{ $sp->pass_code }}</strong> · Kategori: <strong class="text-slate-900">{{ $sp->category->name ?? 'Semua Tribun' }}</strong></p>
                        </div>
                        <div class="text-left md:text-right">
                            <span class="text-xs text-slate-500 font-bold uppercase">Sisa Kuota Pertandingan</span>
                            <p class="text-3xl font-black text-slate-900 font-outfit">{{ $sp->total_matches - $sp->claimed_matches }} <span class="text-sm font-semibold text-slate-500">/ {{ $sp->total_matches }} Match</span></p>
                        </div>
                    </div>

                    <!-- Available Matches to Claim -->
                    <div>
                        <h4 class="text-xs font-black uppercase tracking-wider text-slate-700 mb-4">Pertandingan Kandang Mendatang</h4>
                        <div class="space-y-3">
                            @forelse($eligibleMatches as $match)
                                @php
                                    $hasClaimed = $sp->hasClaimedForEvent($match->id);
                                    $claimDays = $match->season_pass_claim_days_before ?: $sp->claim_window_days_before ?: 5;
                                    $claimStart = $match->event_start_date->copy()->subDays($claimDays);
                                    $isOpen = now()->gte($claimStart);
                                    $claimedRecord = $sp->claims->where('event_id', $match->id)->first();
                                @endphp

                                <div class="bg-slate-50 rounded-xl p-4 flex flex-col md:flex-row md:items-center justify-between gap-3 border border-slate-200">
                                    <div>
                                        <div class="text-xs text-slate-500 font-semibold">{{ $match->event_start_date->format('l, d M Y - H:i') }} WIB · {{ $match->venue }}</div>
                                        <h5 class="text-sm font-bold text-slate-900 mt-0.5">{{ $match->name }}</h5>
                                    </div>

                                    <div class="flex items-center gap-2 shrink-0">
                                        @if($hasClaimed)
                                            <span class="min-h-[44px] inline-flex items-center px-4 py-2 bg-emerald-100 text-emerald-900 rounded-xl text-xs font-bold">
                                                ✓ Tiket Terklaim
                                            </span>
                                            @if($claimedRecord && $claimedRecord->ticket)
                                                <a href="{{ route('evoucher.public', $claimedRecord->ticket->transaction->reference_no ?? '') }}" target="_blank" class="min-h-[44px] inline-flex items-center px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-bold transition focus:ring-2 focus:ring-slate-700">
                                                    Buka E-Voucher
                                                </a>
                                            @endif
                                        @elseif($isOpen)
                                            <form action="{{ route('fan.season-pass.claim', ['event' => $match, 'seasonPass' => $sp]) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="min-h-[44px] px-5 py-2.5 bg-orange-600 hover:bg-orange-700 text-white font-black text-xs uppercase tracking-wider rounded-xl shadow transition focus:ring-2 focus:ring-orange-500">
                                                    Klaim E-Ticket
                                                </button>
                                            </form>
                                        @else
                                            <span class="min-h-[44px] inline-flex items-center px-4 py-2 bg-slate-200 text-slate-700 rounded-xl text-xs font-bold">
                                                Klaim Buka {{ $claimStart->format('d M, H:i') }} (H-{{ $claimDays }})
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <p class="text-xs text-slate-500 py-4">Belum ada jadwal pertandingan kandang yang terdaftar.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-2xl p-12 text-center border border-slate-200">
                    <div class="w-16 h-16 bg-slate-100 text-slate-600 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 font-outfit mb-1">Anda Belum Memiliki Season Pass</h3>
                    <p class="text-sm text-slate-500 mb-6 max-w-sm mx-auto">Dapatkan kemudahan akses semua pertandingan kandang selama 1 musim tanpa repot berebut tiket di setiap matchday.</p>
                    <a href="{{ route('fan.dashboard') }}" class="min-h-[44px] inline-flex items-center px-5 py-2.5 bg-orange-600 hover:bg-orange-700 text-white rounded-xl text-sm font-bold shadow transition focus:ring-2 focus:ring-orange-500">
                        Kembali ke Dashboard
                    </a>
                </div>
            @endforelse

        </div>
    </div>
</x-app-layout>
