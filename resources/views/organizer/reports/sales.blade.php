@extends('layouts.app')
@section('title', 'Laporan Penjualan Tiket')
@section('content')
<div class="space-y-6 animate-in fade-in slide-in-from-bottom-4 duration-700">
    <h2 class="text-2xl font-black text-slate-800 font-outfit mb-4">Laporan Penjualan Tiket</h2>
    <form method="GET" action="" class="flex flex-col md:flex-row gap-3 bg-white p-3 rounded-2xl border border-slate-100 shadow-sm mb-4">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari email atau nama pembeli..." class="rounded-xl border-slate-200 text-sm font-bold text-slate-600 focus:border-orange-500 focus:ring-orange-500 w-full md:w-64" />
        <select name="ticket_category_id" class="rounded-xl border-slate-200 text-sm font-bold text-slate-600 focus:border-orange-500 focus:ring-orange-500 w-full md:w-64">
            <option value="">Semua Kategori Tiket</option>
            @foreach($ticketCategories as $cat)
                <option value="{{ $cat->id }}" {{ request('ticket_category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
            @endforeach
        </select>
        <button type="submit" class="px-5 py-2.5 bg-orange-600 text-black rounded-xl text-xs font-black uppercase tracking-wider hover:bg-orange-700 transition shadow-lg shadow-orange-200">Cari</button>
    </form>
    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/70">
                        <th class="px-6 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest">Invoice</th>
                        <th class="px-6 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest">Customer</th>
                        <th class="px-6 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest">Kategori Tiket</th>
                        <th class="px-6 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Tiket</th>
                        <th class="px-6 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Total</th>
                        <th class="px-6 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest">Status</th>
                        <th class="px-6 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($transactions as $tx)
                        <tr class="hover:bg-slate-50/40 transition">
                            <td class="px-6 py-4 text-xs font-black text-slate-600 font-mono">{{ $tx->reference_no }}</td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-black text-slate-800">{{ $tx->customer_name }}</div>
                                <div class="text-[10px] font-bold text-slate-400 uppercase">{{ $tx->customer_email }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm font-bold text-slate-600">{{ $tx->tickets->pluck('category.name')->implode(', ') }}</td>
                            <td class="px-6 py-4 text-right text-sm font-black text-slate-700">{{ $tx->tickets->count() }}</td>
                            <td class="px-6 py-4 text-right text-sm font-black text-green-600">Rp {{ number_format($tx->total_amount, 0, ',', '.') }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 rounded-lg text-[9px] font-black uppercase tracking-wider {{ $tx->payment_status === 'paid' ? 'bg-emerald-50 text-emerald-600' : 'bg-amber-50 text-amber-600' }}">{{ $tx->payment_status }}</span>
                            </td>
                            <td class="px-6 py-4 flex gap-2">
                                <form method="POST" action="{{ route('organizer.transactions.resend-evoucher', $tx) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="px-3 py-1 bg-orange-600 hover:bg-orange-700 text-black rounded text-xs font-bold shadow-sm transition">Resend E-Voucher</button>
                                </form>
                                <a href="{{ route('organizer.transactions.print-evoucher', $tx) }}" target="_blank" class="px-3 py-1 bg-orange-500 hover:bg-orange-600 text-white rounded text-xs font-bold">Cetak E-Voucher</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-10 text-center text-sm font-bold text-slate-400">Belum ada transaksi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
