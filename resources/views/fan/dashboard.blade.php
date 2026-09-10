<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-black text-slate-900 font-outfit uppercase tracking-tight">
                    Portal Keanggotaan Suporter (Fan Zone)
                </h2>
                <p class="text-sm text-slate-500 font-medium">
                    Kartu identitas suporter resmi {{ $member->tenant->name ?? 'Klub' }}, poin loyalitas, dan tiket pertandingan.
                </p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('fan.game-zone') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-amber-500 hover:bg-amber-600 text-black font-black text-xs uppercase tracking-wider rounded-xl shadow-lg shadow-amber-500/20 transition">
                    ★ Game Zone & Poin
                </a>
                <a href="{{ route('fan.season-pass') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white font-bold text-xs uppercase tracking-wider rounded-xl shadow-lg shadow-orange-500/20 transition">
                    Tiket Terusan (Season Pass)
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

            @if(session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl flex items-center gap-3 font-semibold text-sm">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-2xl flex items-center gap-3 font-semibold text-sm">
                    <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    {{ session('error') }}
                </div>
            @endif

            <!-- KYC Status Alert Banner -->
            @if($member->kyc_status === 'unverified')
                <div class="bg-gradient-to-r from-orange-500 to-amber-600 text-white rounded-3xl p-6 shadow-lg flex flex-col md:flex-row items-center justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-white/20 rounded-2xl flex items-center justify-center shrink-0">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        </div>
                        <div>
                            <h3 class="font-outfit font-black text-lg">Lengkapi Verifikasi NIK & KYC Anda</h3>
                            <p class="text-xs text-white/80 mt-0.5">Sesuai regulasi PSSI & Single Fan Identity, verifikasi identitas diperlukan untuk membeli dan mengklaim tiket pertandingan.</p>
                        </div>
                    </div>
                    <a href="{{ route('fan.kyc') }}" class="px-5 py-2.5 bg-white text-orange-600 font-black text-xs uppercase tracking-wider rounded-xl shadow hover:bg-orange-50 transition shrink-0">
                        Upload KYC Sekarang (+100 Pts)
                    </a>
                </div>
            @elseif($member->kyc_status === 'pending')
                <div class="bg-amber-50 border border-amber-200 text-amber-900 rounded-3xl p-6 flex items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <div class="w-3 h-3 rounded-full bg-amber-500 animate-ping"></div>
                        <p class="text-xs font-bold">Data KYC & NIK Anda sedang dalam proses verifikasi oleh admin klub (1x24 jam).</p>
                    </div>
                    <a href="{{ route('fan.kyc') }}" class="text-xs font-bold text-amber-700 underline">Lihat Data</a>
                </div>
            @elseif($member->kyc_status === 'rejected')
                <div class="bg-rose-50 border border-rose-200 text-rose-900 rounded-3xl p-6 flex items-center justify-between gap-4">
                    <div>
                        <p class="text-xs font-bold text-rose-700">Verifikasi KYC Anda Ditolak: {{ $member->kyc_reject_reason }}</p>
                        <p class="text-[11px] text-rose-500 mt-0.5">Silakan unggah ulang foto KTP atau foto wajah yang lebih jelas.</p>
                    </div>
                    <a href="{{ route('fan.kyc') }}" class="px-4 py-2 bg-rose-600 text-white rounded-xl text-xs font-bold">Upload Ulang</a>
                </div>
            @endif

            <!-- Pending Korwil Consents Banner -->
            @if($pendingConsents->isNotEmpty())
                <div class="bg-indigo-900 text-white rounded-3xl p-6 shadow-lg space-y-4">
                    <h3 class="font-outfit font-black text-base flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                        Konfirmasi Kuota Tiket Rombongan Korwil
                    </h3>
                    <p class="text-xs text-indigo-200">
                        Korwil Anda mengajukan alokasi tiket rombongan menggunakan NIK Anda. Anda dapat menyetujui untuk ikut rombongan atau menolak untuk memesan tiket secara mandiri.
                    </p>
                    <div class="space-y-2">
                        @foreach($pendingConsents as $c)
                            <div class="bg-indigo-800/60 rounded-2xl p-4 flex flex-col md:flex-row md:items-center justify-between gap-3 border border-indigo-700/50">
                                <div>
                                    <p class="font-bold text-sm">{{ $c->allocation->event->name }}</p>
                                    <p class="text-xs text-indigo-300">{{ $c->allocation->korwil->name }} • Tribun: {{ $c->allocation->category->name }}</p>
                                </div>
                                <div class="flex gap-2">
                                    <form action="{{ route('fan.korwil-consent.respond', $c) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="action" value="approve">
                                        <button type="submit" class="px-4 py-2 bg-emerald-500 hover:bg-emerald-600 text-white rounded-xl text-xs font-black uppercase">
                                            ✓ Setujui Rombongan
                                        </button>
                                    </form>
                                    <form action="{{ route('fan.korwil-consent.respond', $c) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="action" value="reject">
                                        <button type="submit" class="px-4 py-2 bg-white/10 hover:bg-white/20 text-white rounded-xl text-xs font-bold">
                                            Beli Mandiri
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Main Layout: Digital Card & Upcoming Matches -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                <!-- Left: Digital Member Card -->
                <div class="space-y-6">
                    <div class="bg-gradient-to-br from-slate-950 via-slate-900 to-black text-white rounded-[2rem] p-7 shadow-2xl border border-white/10 relative overflow-hidden">
                        <div class="absolute -right-10 -bottom-10 w-48 h-48 bg-orange-500/20 rounded-full blur-3xl"></div>

                        <!-- Card Header -->
                        <div class="flex items-center justify-between mb-8 relative z-10">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-gradient-to-br from-orange-500 to-amber-600 rounded-xl flex items-center justify-center font-black text-base shadow-lg shadow-orange-500/30">
                                    G
                                </div>
                                <div>
                                    <h4 class="font-outfit font-black tracking-wider text-sm">{{ $member->tenant->name ?? 'GENTIX CLUB' }}</h4>
                                    <p class="text-[9px] uppercase tracking-widest text-slate-400">Official Member Card</p>
                                </div>
                            </div>
                            <span class="px-3.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider text-white shadow-sm" style="background-color: {{ $member->tier->badge_color ?? '#f97316' }}">
                                {{ $member->tier->name ?? 'Free Fan' }}
                            </span>
                        </div>

                        <!-- Card Member Number & Chip -->
                        <div class="mb-8 relative z-10">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-[10px] font-mono uppercase text-slate-400 tracking-widest">Card ID Number</span>
                                @if($member->kyc_status === 'verified')
                                    <span class="text-[10px] font-bold text-emerald-400 flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                        Verified KYC
                                    </span>
                                @endif
                            </div>
                            <p class="font-mono text-xl font-black tracking-widest text-orange-400">{{ $member->member_number }}</p>
                        </div>

                        <!-- Card Footer -->
                        <div class="flex items-end justify-between border-t border-white/10 pt-4 relative z-10">
                            <div>
                                <p class="text-[9px] uppercase text-slate-400 font-bold tracking-wider">Nama Suporter</p>
                                <p class="font-bold text-sm text-white truncate max-w-[160px]">{{ $member->full_name_ktp ?: $member->user->name }}</p>
                                <p class="text-[10px] text-slate-400 font-mono mt-0.5">NIK: {{ $member->nik ? substr($member->nik, 0, 6) . '******' . substr($member->nik, -4) : 'Belum input' }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-[9px] uppercase text-slate-400 font-bold tracking-wider">Fan Points</p>
                                <p class="font-black text-base text-amber-400">★ {{ number_format($member->points_balance) }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Stats & Point Info -->
                    <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm space-y-4">
                        <div class="flex items-center justify-between">
                            <h4 class="text-xs font-black uppercase tracking-wider text-slate-900">Dompet Poin & Rewards</h4>
                            <a href="{{ route('fan.game-zone') }}" class="text-xs font-bold text-orange-600 hover:underline">+ Kumpulkan Poin</a>
                        </div>
                        <div class="p-4 bg-amber-50/50 border border-amber-200/60 rounded-2xl flex items-center justify-between">
                            <div>
                                <span class="text-xs text-amber-700 font-semibold">Total Saldo Poin</span>
                                <p class="text-2xl font-black text-amber-800 font-outfit mt-0.5">★ {{ number_format($member->points_balance) }}</p>
                            </div>
                            <div class="text-right text-[11px] text-slate-500">
                                1 Poin = Rp 1<br>
                                <span class="text-emerald-600 font-bold">Bisa digunakan saat checkout</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Middle & Right: Upcoming Matches & Season Pass Action -->
                <div class="lg:col-span-2 space-y-6">

                    <!-- Section: Jadwal Match & Klaim E-Ticket -->
                    <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm space-y-6">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                            <div>
                                <h3 class="text-lg font-black uppercase tracking-tight text-slate-900 font-outfit">
                                    Jadwal Match Kandang Mendatang
                                </h3>
                                <p class="text-xs text-slate-500 mt-0.5">Klaim tiket pertandingan (khusus Season Pass) atau beli tiket dengan potongan diskon member.</p>
                            </div>
                        </div>

                        <div class="space-y-4">
                            @forelse($upcomingMatches as $match)
                                @php
                                    $claimDays = $match->season_pass_claim_days_before ?: 5;
                                    $claimWindowStart = $match->event_start_date->copy()->subDays($claimDays);
                                    $isClaimOpen = now()->gte($claimWindowStart);
                                    $activeSeasonPass = $member->seasonPasses->where('status', 'active')->first();
                                    $hasClaimed = $activeSeasonPass ? $activeSeasonPass->hasClaimedForEvent($match->id) : false;
                                @endphp

                                <div class="bg-slate-50 rounded-2xl p-5 border border-slate-200/80 flex flex-col md:flex-row md:items-center justify-between gap-4 hover:border-orange-200 transition">
                                    <div class="space-y-1">
                                        <div class="flex items-center gap-2 text-xs font-semibold text-slate-400">
                                            <span>{{ $match->event_start_date->format('l, d F Y • H:i') }} WIB</span>
                                            <span>•</span>
                                            <span>{{ $match->venue }}</span>
                                        </div>
                                        <h4 class="text-base font-black text-slate-900 font-outfit">
                                            {{ $match->name }}
                                        </h4>
                                        <p class="text-xs text-slate-500 font-medium">
                                            {{ $match->home_team_name ?: 'Home Team' }} vs {{ $match->away_team_name ?: 'Away Team' }}
                                        </p>
                                    </div>

                                    <div class="flex items-center gap-3 shrink-0">
                                        @if($activeSeasonPass && $match->allow_season_pass)
                                            @if($hasClaimed)
                                                <span class="px-4 py-2 bg-emerald-100 text-emerald-800 font-bold text-xs rounded-xl flex items-center gap-1.5">
                                                    ✓ E-Ticket Terklaim
                                                </span>
                                            @elseif($isClaimOpen)
                                                <form action="{{ route('fan.season-pass.claim', ['event' => $match, 'seasonPass' => $activeSeasonPass]) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" onclick="return confirm('Klaim e-ticket matchday ini untuk Season Pass Anda?');" class="px-5 py-2.5 bg-orange-600 hover:bg-orange-700 text-white font-black text-xs uppercase tracking-wider rounded-xl shadow-lg shadow-orange-500/20 transition">
                                                        🎟 Klaim E-Ticket
                                                    </button>
                                                </form>
                                            @else
                                                <span class="px-3.5 py-2 bg-slate-200 text-slate-600 font-bold text-xs rounded-xl" title="Buka klaim: {{ $claimWindowStart->format('d M H:i') }}">
                                                    Klaim Buka H-{{ $claimDays }}
                                                </span>
                                            @endif
                                        @endif

                                        <a href="{{ route('events.show', $match->slug) }}" class="px-4 py-2.5 bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs rounded-xl transition">
                                            Beli Tiket
                                        </a>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-8 text-slate-400 text-sm">
                                    Belum ada jadwal pertandingan kandang dalam waktu dekat.
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <!-- Section: Mini Tebak Skor Promo Card -->
                    <div class="bg-gradient-to-r from-amber-500 via-orange-500 to-rose-600 text-white rounded-3xl p-6 shadow-md flex flex-col md:flex-row items-center justify-between gap-4">
                        <div>
                            <span class="px-2.5 py-0.5 rounded-full bg-black/20 text-white text-[10px] font-black uppercase tracking-wider">Mini Game Matchday</span>
                            <h4 class="font-outfit font-black text-lg mt-1">Tebak Skor Pertandingan & Kuis Klub</h4>
                            <p class="text-xs text-white/90 mt-0.5">Tebak skor pertandingan kandang berikutnya untuk mendapatkan hingga 100 Poin gratis.</p>
                        </div>
                        <a href="{{ route('fan.game-zone') }}" class="px-5 py-2.5 bg-black text-white hover:bg-slate-900 font-black text-xs uppercase tracking-wider rounded-xl shadow transition shrink-0">
                            Mainkan Sekarang
                        </a>
                    </div>

                </div>
            </div>

        </div>
    </div>
</x-app-layout>
