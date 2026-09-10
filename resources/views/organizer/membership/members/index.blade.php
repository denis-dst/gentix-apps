<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-black text-slate-900 font-outfit uppercase tracking-tight">
                    Database Suporter & Verifikasi KYC
                </h2>
                <p class="text-sm text-slate-500 font-medium">
                    Manajemen data keanggotaan suporter, verifikasi NIK KTP, dan kepatuhan Single Fan Identity PSSI.
                </p>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-900 rounded-xl flex items-center gap-3 font-semibold text-sm">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    {{ session('success') }}
                </div>
            @endif

            <!-- KPI Counters -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm">
                    <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Suporter</p>
                    <p class="text-2xl font-black text-slate-900 font-outfit mt-1">{{ number_format($counts['total']) }}</p>
                </div>
                <div class="bg-white rounded-2xl p-5 border border-emerald-200 shadow-sm bg-emerald-50/30">
                    <p class="text-xs font-bold text-emerald-800 uppercase tracking-wider">KYC Terverifikasi</p>
                    <p class="text-2xl font-black text-emerald-900 font-outfit mt-1">{{ number_format($counts['verified']) }}</p>
                </div>
                <div class="bg-white rounded-2xl p-5 border border-amber-200 shadow-sm bg-amber-50/30">
                    <p class="text-xs font-bold text-amber-800 uppercase tracking-wider">Menunggu Verifikasi</p>
                    <p class="text-2xl font-black text-amber-900 font-outfit mt-1">{{ number_format($counts['pending']) }}</p>
                </div>
                <div class="bg-white rounded-2xl p-5 border border-rose-200 shadow-sm bg-rose-50/30">
                    <p class="text-xs font-bold text-rose-800 uppercase tracking-wider">KYC Ditolak</p>
                    <p class="text-2xl font-black text-rose-900 font-outfit mt-1">{{ number_format($counts['rejected']) }}</p>
                </div>
            </div>

            <!-- Filter & Search -->
            <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm">
                <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="md:col-span-2">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Nama, No. Member, NIK, atau No. WA..." class="w-full rounded-xl border-slate-300 text-sm font-semibold py-2.5 focus:ring-2 focus:ring-orange-500">
                    </div>
                    <div>
                        <select name="kyc_status" class="w-full rounded-xl border-slate-300 text-sm font-semibold py-2.5 focus:ring-2 focus:ring-orange-500">
                            <option value="">Semua Status KYC</option>
                            <option value="pending" {{ request('kyc_status') == 'pending' ? 'selected' : '' }}>Menunggu Verifikasi (Pending)</option>
                            <option value="verified" {{ request('kyc_status') == 'verified' ? 'selected' : '' }}>Terverifikasi</option>
                            <option value="rejected" {{ request('kyc_status') == 'rejected' ? 'selected' : '' }}>Ditolak</option>
                            <option value="unverified" {{ request('kyc_status') == 'unverified' ? 'selected' : '' }}>Belum Upload KYC</option>
                        </select>
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" class="min-h-[44px] flex-1 px-4 py-2.5 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-sm font-bold transition focus:ring-2 focus:ring-slate-700">
                            Filter
                        </button>
                        <a href="{{ route('organizer.membership.members.index') }}" class="min-h-[44px] inline-flex items-center px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-sm font-bold transition">
                            Reset
                        </a>
                    </div>
                </form>
            </div>

            <!-- Members Table -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-600">
                        <thead class="bg-slate-50 text-slate-700 text-xs uppercase font-black tracking-wider border-b border-slate-200">
                            <tr>
                                <th class="px-6 py-4">Suporter / Akun</th>
                                <th class="px-6 py-4">No. Member & NIK</th>
                                <th class="px-6 py-4">Tier & Poin</th>
                                <th class="px-6 py-4">Status KYC</th>
                                <th class="px-6 py-4">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($members as $m)
                                <tr class="hover:bg-slate-50 transition">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-full bg-slate-900 text-white font-bold flex items-center justify-center overflow-hidden shrink-0">
                                                @if($m->avatar)
                                                    <img src="{{ asset('storage/' . $m->avatar) }}" class="w-full h-full object-cover">
                                                @else
                                                    {{ substr($m->user->name ?? 'F', 0, 1) }}
                                                @endif
                                            </div>
                                            <div>
                                                <p class="font-bold text-slate-900">{{ $m->full_name_ktp ?: $m->user->name }}</p>
                                                <p class="text-xs text-slate-500">{{ $m->user->email }} · {{ $m->phone ?: '-' }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 font-mono text-xs">
                                        <span class="font-bold text-slate-900">{{ $m->member_number }}</span>
                                        <div class="text-slate-500 mt-0.5">{{ $m->nik ?: 'Belum input NIK' }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="px-2.5 py-1 rounded-full text-xs font-black uppercase text-white" style="background-color: {{ $m->tier->badge_color ?? '#64748b' }}">
                                            {{ $m->tier->name ?? 'Free Fan' }}
                                        </span>
                                        <div class="text-xs font-bold text-amber-800 mt-1">{{ number_format($m->points_balance) }} Pts</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($m->kyc_status === 'verified')
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-900">
                                                <svg class="w-3.5 h-3.5 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                                Terverifikasi
                                            </span>
                                        @elseif($m->kyc_status === 'pending')
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-950">
                                                ● Perlu Verifikasi
                                            </span>
                                        @elseif($m->kyc_status === 'rejected')
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-950">
                                                ✕ Ditolak
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-700">
                                                Belum Upload
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <a href="{{ route('organizer.membership.members.show', $m) }}" class="min-h-[44px] inline-flex items-center px-3.5 py-2 bg-slate-100 hover:bg-orange-600 hover:text-white text-slate-800 rounded-xl text-xs font-bold transition focus:ring-2 focus:ring-orange-500">
                                            Detail & KYC
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center text-slate-500 font-medium">
                                        Tidak ada data suporter yang sesuai dengan filter pencarian.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($members->hasPages())
                    <div class="p-6 border-t border-slate-100">
                        {{ $members->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
