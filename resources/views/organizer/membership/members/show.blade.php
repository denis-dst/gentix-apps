<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('organizer.membership.members.index') }}" class="p-2 text-slate-400 hover:text-slate-700 bg-white border border-slate-200 rounded-xl transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div>
                <h2 class="text-2xl font-black text-slate-900 font-outfit uppercase tracking-tight">
                    Detail Profil & Verifikasi KYC Suporter
                </h2>
                <p class="text-sm text-slate-500 font-medium">{{ $member->member_number }} • {{ $member->full_name_ktp ?: $member->user->name }}</p>
            </div>
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

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <!-- Left Column: Member Card & Profile -->
                <div class="space-y-6">
                    <!-- Digital Card Preview -->
                    <div class="bg-gradient-to-br from-slate-900 via-slate-800 to-black text-white rounded-3xl p-6 shadow-xl border border-white/10 relative overflow-hidden">
                        <div class="absolute -right-8 -bottom-8 w-40 h-40 bg-orange-500/20 rounded-full blur-3xl"></div>

                        <div class="flex items-center justify-between mb-8">
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 bg-orange-500 rounded-lg flex items-center justify-center font-black text-xs">G</div>
                                <span class="font-outfit font-black tracking-wider text-sm">{{ $member->tenant->name ?? 'GENTIX CLUB' }}</span>
                            </div>
                            <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider text-white" style="background-color: {{ $member->tier->badge_color ?? '#f97316' }}">
                                {{ $member->tier->name ?? 'Free Fan' }}
                            </span>
                        </div>

                        <div class="mb-6">
                            <p class="text-[10px] font-mono uppercase text-slate-400 tracking-widest">Nomor Anggota</p>
                            <p class="font-mono text-lg font-black tracking-widest text-orange-400">{{ $member->member_number }}</p>
                        </div>

                        <div class="flex items-end justify-between border-t border-white/10 pt-4">
                            <div>
                                <p class="text-[10px] uppercase text-slate-400 font-bold">Nama Suporter</p>
                                <p class="font-bold text-sm truncate max-w-[180px]">{{ $member->full_name_ktp ?: $member->user->name }}</p>
                                <p class="text-[10px] text-slate-400 font-mono mt-0.5">NIK: {{ $member->nik ?: '-' }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-[10px] uppercase text-slate-400 font-bold">Poin Fan</p>
                                <p class="font-black text-sm text-amber-400">★ {{ number_format($member->points_balance) }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Personal Info Card -->
                    <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm space-y-4">
                        <h3 class="text-sm font-black uppercase tracking-wider text-slate-900 border-b border-slate-100 pb-3">
                            Informasi Akun
                        </h3>
                        <div class="space-y-3 text-xs">
                            <div>
                                <span class="text-slate-400 font-medium">Email:</span>
                                <p class="font-bold text-slate-800">{{ $member->user->email }}</p>
                            </div>
                            <div>
                                <span class="text-slate-400 font-medium">Nomor WhatsApp:</span>
                                <p class="font-bold text-slate-800">{{ $member->phone ?: $member->user->phone ?: '-' }}</p>
                            </div>
                            <div>
                                <span class="text-slate-400 font-medium">Tanggal Lahir & Gender:</span>
                                <p class="font-bold text-slate-800">{{ $member->birth_date ? $member->birth_date->format('d M Y') : '-' }} • {{ $member->gender === 'male' ? 'Laki-laki' : ($member->gender === 'female' ? 'Perempuan' : '-') }}</p>
                            </div>
                            <div>
                                <span class="text-slate-400 font-medium">Alamat Domisili:</span>
                                <p class="font-medium text-slate-800 leading-relaxed">{{ $member->address ?: '-' }}</p>
                            </div>
                            <div>
                                <span class="text-slate-400 font-medium">Bergabung Sejak:</span>
                                <p class="font-bold text-slate-800">{{ $member->created_at->format('d F Y, H:i') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Middle & Right Column: KYC Dokumen & Action -->
                <div class="lg:col-span-2 space-y-6">

                    <!-- KYC Verification Box -->
                    <div class="bg-white rounded-3xl p-8 border border-slate-200/80 shadow-sm space-y-6">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                            <div>
                                <h3 class="text-base font-black uppercase tracking-tight text-slate-900 font-outfit">
                                    Dokumen Verifikasi Identitas (KYC)
                                </h3>
                                <p class="text-xs text-slate-500 mt-0.5">Pemeriksaan kecocokan NIK, Foto KTP, dan Foto Wajah asli suporter.</p>
                            </div>
                            <div>
                                @if($member->kyc_status === 'verified')
                                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
                                        ✓ Terverifikasi pada {{ $member->kyc_verified_at?->format('d/m/Y') }}
                                    </span>
                                @elseif($member->kyc_status === 'pending')
                                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 animate-pulse">
                                        ● Menunggu Keputusan
                                    </span>
                                @elseif($member->kyc_status === 'rejected')
                                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-800">
                                        ✕ Ditolak ({{ $member->kyc_reject_reason }})
                                    </span>
                                @else
                                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-600">
                                        Belum Upload Dokumen
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Foto KTP -->
                            <div>
                                <p class="text-xs font-black uppercase text-slate-600 mb-2">Foto e-KTP / Kartu Identitas</p>
                                <div class="bg-slate-50 border border-slate-200 rounded-2xl p-2 aspect-[16/10] flex items-center justify-center overflow-hidden">
                                    @if($member->ktp_photo)
                                        <a href="{{ asset('storage/' . $member->ktp_photo) }}" target="_blank" class="block w-full h-full">
                                            <img src="{{ asset('storage/' . $member->ktp_photo) }}" alt="KTP" class="w-full h-full object-cover rounded-xl hover:scale-105 transition">
                                        </a>
                                    @else
                                        <p class="text-xs text-slate-400 font-medium">Foto KTP belum diunggah</p>
                                    @endif
                                </div>
                            </div>

                            <!-- Foto Wajah / Selfie -->
                            <div>
                                <p class="text-xs font-black uppercase text-slate-600 mb-2">Foto Wajah Asli (Selfie)</p>
                                <div class="bg-slate-50 border border-slate-200 rounded-2xl p-2 aspect-[16/10] flex items-center justify-center overflow-hidden">
                                    @if($member->face_photo)
                                        <a href="{{ asset('storage/' . $member->face_photo) }}" target="_blank" class="block w-full h-full">
                                            <img src="{{ asset('storage/' . $member->face_photo) }}" alt="Face" class="w-full h-full object-cover rounded-xl hover:scale-105 transition">
                                        </a>
                                    @else
                                        <p class="text-xs text-slate-400 font-medium">Foto Wajah belum diunggah</p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Verification Action Form -->
                        @if($member->kyc_status === 'pending' || $member->kyc_status === 'unverified' || $member->kyc_status === 'rejected')
                            <div class="bg-slate-50 rounded-2xl p-6 border border-slate-200/80 space-y-4">
                                <h4 class="text-xs font-black uppercase tracking-wider text-slate-800">Tindakan Verifikasi Petugas</h4>
                                
                                <div class="flex flex-col md:flex-row gap-4">
                                    <!-- Approve Button -->
                                    <form action="{{ route('organizer.membership.members.verify-kyc', $member) }}" method="POST" class="inline">
                                        @csrf
                                        <input type="hidden" name="action" value="approve">
                                        <button type="submit" onclick="return confirm('Setujui verifikasi KYC suporter ini? Suporter akan mendapatkan bonus 100 poin.');" class="w-full md:w-auto px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-black uppercase tracking-wider shadow-lg shadow-emerald-500/20 transition">
                                            ✓ Setujui Verifikasi KYC
                                        </button>
                                    </form>

                                    <!-- Reject Form -->
                                    <form action="{{ route('organizer.membership.members.verify-kyc', $member) }}" method="POST" class="flex-1 flex gap-2">
                                        @csrf
                                        <input type="hidden" name="action" value="reject">
                                        <input type="text" name="reject_reason" placeholder="Alasan penolakan (e.g. Foto KTP buram/tidak terbaca)..." required class="flex-1 rounded-xl border-slate-200 text-xs font-semibold">
                                        <button type="submit" class="px-5 py-3 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-black uppercase tracking-wider transition">
                                            Tolak
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Point & Transaction History -->
                    <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm space-y-4">
                        <h3 class="text-sm font-black uppercase tracking-wider text-slate-900 border-b border-slate-100 pb-3">
                            Mutasi Poin Terakhir
                        </h3>
                        <div class="divide-y divide-slate-100">
                            @forelse($member->pointsLedger as $p)
                                <div class="py-3 flex items-center justify-between text-xs">
                                    <div>
                                        <p class="font-bold text-slate-800">{{ $p->description }}</p>
                                        <p class="text-[11px] text-slate-400 mt-0.5">{{ $p->created_at->format('d M Y, H:i') }}</p>
                                    </div>
                                    <span class="font-black text-sm {{ $p->type === 'earn' ? 'text-emerald-600' : 'text-rose-600' }}">
                                        {{ $p->type === 'earn' ? '+' : '-' }}{{ number_format(abs($p->points)) }} Pts
                                    </span>
                                </div>
                            @empty
                                <p class="text-xs text-slate-400 py-3">Belum ada riwayat mutasi poin.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
