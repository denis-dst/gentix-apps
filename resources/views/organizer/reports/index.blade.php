<x-app-layout>
    <x-slot name="title">Laporan Penjualan</x-slot>
    <x-slot name="header">Laporan Transaksi</x-slot>

    <div class="space-y-6">
        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">Total Pendapatan</p>
                <h4 class="text-xl font-black text-slate-900 font-outfit">Rp {{ number_format($transactions->where('payment_status', 'paid')->sum('total_amount'), 0, ',', '.') }}</h4>
            </div>
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">Total Tiket Terjual</p>
                <h4 class="text-xl font-black text-slate-900 font-outfit">{{ $transactions->where('payment_status', 'paid')->sum(function($t) { return $t->tickets->count(); }) }}</h4>
            </div>
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">Transaksi Berhasil</p>
                <h4 class="text-xl font-black text-emerald-600 font-outfit">{{ $transactions->where('payment_status', 'paid')->count() }}</h4>
            </div>
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">Transaksi Pending</p>
                <h4 class="text-xl font-black text-amber-500 font-outfit">{{ $transactions->where('payment_status', 'pending')->count() }}</h4>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="p-6 border-b border-slate-50 flex justify-between items-center bg-slate-50/30">
                <h3 class="font-bold text-slate-800">Data Transaksi</h3>
                <div class="flex gap-2">
                    <!-- Export Button can go here -->
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/50">
                            <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider">No. Invoice</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider">Pelanggan</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider">Event</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider">Total</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider">Waktu</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($transactions as $tx)
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="px-6 py-4 font-mono font-bold text-xs text-slate-600">
                                    {{ $tx->reference_no }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-bold text-slate-900 text-sm">{{ $tx->customer_name }}</div>
                                    <div class="text-[10px] text-slate-400 font-bold uppercase">{{ $tx->customer_email }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-slate-600">{{ $tx->event->name ?? '-' }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-black text-slate-900 text-sm">Rp {{ number_format($tx->total_amount, 0, ',', '.') }}</div>
                                    <div class="text-[10px] text-slate-400 font-bold uppercase">{{ $tx->tickets->count() }} Tiket</div>
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $txStatusClasses = [
                                            'paid' => 'bg-emerald-50 text-emerald-600 border-emerald-100',
                                            'pending' => 'bg-amber-50 text-amber-600 border-amber-100',
                                            'cancelled' => 'bg-rose-50 text-rose-600 border-rose-100',
                                        ];
                                    @endphp
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-black uppercase border {{ $txStatusClasses[$tx->payment_status] ?? 'bg-slate-50 text-slate-500 border-slate-100' }}">
                                        {{ $tx->payment_status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-xs text-slate-500 font-medium">
                                    {{ $tx->created_at->format('d/m/Y H:i') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                                    Belum ada transaksi terekam.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-6 bg-slate-50/30 border-t border-slate-50">
                {{ $transactions->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
