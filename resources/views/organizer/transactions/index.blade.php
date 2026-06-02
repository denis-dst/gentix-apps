<x-app-layout>
    <x-slot name="title">Laporan Penjualan Tiket</x-slot>

    <div class="space-y-6 animate-in fade-in slide-in-from-bottom-4 duration-700">
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
            <div>
                <p class="text-[10px] font-black text-orange-500 uppercase tracking-[0.25em] mb-2">Finance & Sales</p>
                <h2 class="text-3xl font-black text-slate-800 font-outfit">Laporan Penjualan</h2>
                <p class="text-sm text-slate-500 font-medium mt-1">Cari dan kelola transaksi tiket, serta cetak ulang e-voucher pembeli.</p>
            </div>
        </div>

        @if(session('success'))
            <div class="bg-emerald-50 border border-emerald-100 text-emerald-600 px-4 py-3 rounded-2xl text-sm font-bold flex items-center gap-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                {{ session('success') }}
            </div>
        @endif

        <form method="GET" action="{{ route('organizer.transactions.index') }}" class="flex flex-col xl:flex-row gap-3 bg-white p-4 rounded-3xl border border-slate-100 shadow-sm">
            <div class="relative flex-1">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                </div>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Email, Nama, atau No. Invoice..." class="w-full pl-10 pr-4 py-2.5 rounded-xl border-slate-200 text-sm font-bold text-slate-600 focus:border-orange-500 focus:ring-orange-500" />
            </div>
            
            <select name="event_id" class="rounded-xl border-slate-200 text-sm font-bold text-slate-600 focus:border-orange-500 focus:ring-orange-500 xl:w-64">
                <option value="">Semua Event</option>
                @foreach($eventOptions as $event)
                    <option value="{{ $event->id }}" {{ request('event_id') == $event->id ? 'selected' : '' }}>{{ $event->name }}</option>
                @endforeach
            </select>

            <select name="ticket_category_id" class="rounded-xl border-slate-200 text-sm font-bold text-slate-600 focus:border-orange-500 focus:ring-orange-500 xl:w-64">
                <option value="">Semua Kategori Tiket</option>
                @foreach($ticketCategories as $cat)
                    <option value="{{ $cat->id }}" {{ request('ticket_category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>

            <button type="submit" class="px-8 py-2.5 bg-orange-600 text-white rounded-xl text-xs font-black uppercase tracking-wider hover:bg-orange-700 transition shadow-lg shadow-orange-200">Cari Transaksi</button>
            
            @if(request()->anyFilled(['q', 'event_id', 'ticket_category_id']))
                <a href="{{ route('organizer.transactions.index') }}" class="px-5 py-2.5 bg-slate-100 text-slate-500 rounded-xl text-xs font-black uppercase tracking-wider hover:bg-slate-200 transition text-center">Reset</a>
            @endif
        </form>

        <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/70 border-b border-slate-100">
                            <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Waktu & Invoice</th>
                            <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Customer</th>
                            <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Tiket</th>
                            <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Promo</th>
                            <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Total Bayar</th>
                            <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Status</th>
                            <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse($transactions as $tx)
                            <tr class="hover:bg-slate-50/40 transition group">
                                <td class="px-8 py-5">
                                    <div class="text-xs font-black text-slate-600 font-mono">{{ $tx->reference_no }}</div>
                                    <div class="text-[10px] font-bold text-slate-400 mt-1 uppercase">{{ $tx->created_at->format('d M Y H:i') }}</div>
                                </td>
                                <td class="px-8 py-5">
                                    <div class="text-sm font-black text-slate-800">{{ $tx->customer_name }}</div>
                                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-tight mt-0.5">{{ $tx->customer_email }}</div>
                                </td>
                                <td class="px-8 py-5 text-right">
                                    <div class="text-sm font-black text-slate-700">{{ $tx->tickets->count() }}</div>
                                    <div class="text-[9px] font-bold text-orange-500 uppercase">{{ $tx->tickets->first()->category->name ?? 'Mixed' }}</div>
                                </td>
                                <td class="px-8 py-5 text-right">
                                    @if($tx->promoCode)
                                        <div class="text-xs font-black text-orange-600 font-mono">{{ $tx->promoCode->code }}</div>
                                        <div class="text-[9px] font-bold text-slate-400 uppercase">-Rp {{ number_format($tx->discount_amount, 0, ',', '.') }}</div>
                                    @else
                                        <div class="text-xs font-bold text-slate-300">-</div>
                                    @endif
                                </td>
                                <td class="px-8 py-5 text-right">
                                    <div class="text-sm font-black text-green-600">Rp {{ number_format($tx->total_amount, 0, ',', '.') }}</div>
                                    <div class="text-[9px] font-bold text-slate-400 uppercase">{{ $tx->payment_method ?? 'Unknown' }}</div>
                                </td>
                                <td class="px-8 py-5">
                                    @if($tx->payment_status === 'paid')
                                        <span class="px-2.5 py-1.5 rounded-lg bg-emerald-50 text-emerald-600 text-[9px] font-black uppercase tracking-wider">PAID</span>
                                    @else
                                        <span class="px-2.5 py-1.5 rounded-lg bg-amber-50 text-amber-600 text-[9px] font-black uppercase tracking-wider">{{ strtoupper($tx->payment_status) }}</span>
                                    @endif
                                </td>
                                <td class="px-8 py-5">
                                    <div class="flex items-center gap-2">
                                        @if($tx->payment_status !== 'paid')
                                            <form method="POST" action="{{ route('organizer.transactions.mark-as-paid', $tx) }}" onsubmit="return confirm('PENTING: Pastikan uang pembayaran sudah diterima secara manual sebelum melakukan verifikasi ini. Lanjutkan?')">
                                                @csrf
                                                <button type="submit" title="Konfirmasi Pembayaran Manual" class="p-2.5 bg-orange-500 text-black rounded-xl hover:bg-orange-600 transition shadow-sm border border-orange-200">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                                </button>
                                            </form>
                                        @else
                                            <form method="POST" action="{{ route('organizer.transactions.resend-evoucher', $tx) }}" class="inline">
                                                @csrf
                                                <button type="submit" title="Kirim Ulang Email" class="p-2.5 bg-orange-500 text-black rounded-xl hover:bg-orange-600 transition shadow-sm">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                                                </button>
                                            </form>
                                            <a href="{{ route('organizer.transactions.print-evoucher', $tx) }}" target="_blank" title="Cetak E-Voucher" class="p-2.5 bg-orange-50 text-orange-600 rounded-xl hover:bg-orange-600 hover:text-white transition shadow-sm">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                                            </a>
                                        @endif
                                        
                                        @if(!in_array($tx->payment_status, ['failed', 'expired', 'refunded']))
                                            <form method="POST" action="{{ route('organizer.transactions.cancel', $tx) }}" onsubmit="return confirm('PENTING: Membatalkan transaksi ini akan menonaktifkan tiket/e-voucher terkait dan mengembalikan kuota tiket. Lanjutkan?')">
                                                @csrf
                                                <button type="submit" title="Cancel Transaksi & Kembalikan Kuota" class="p-2.5 bg-rose-50 text-rose-600 rounded-xl hover:bg-rose-600 hover:text-white transition border border-rose-100 shadow-sm">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-8 py-20 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center text-slate-300 mb-4">
                                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
                                        </div>
                                        <p class="text-sm font-bold text-slate-400">Tidak ada transaksi yang ditemukan.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-6">
            {{ $transactions->links() }}
        </div>
    </div>
</x-app-layout>
