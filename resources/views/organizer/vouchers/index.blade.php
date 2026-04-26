<x-app-layout>
    <x-slot name="title">Manajemen Voucher</x-slot>
    <x-slot name="header">Kode Promo / Voucher</x-slot>

    <div class="space-y-6">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-xl font-bold text-slate-800">Daftar Kode Promo</h2>
                <p class="text-sm text-slate-500">Kelola potongan harga untuk menarik lebih banyak pembeli.</p>
            </div>
            <a href="{{ route('organizer.vouchers.create') }}" class="px-5 py-2.5 bg-purple-600 text-white rounded-xl font-bold hover:bg-purple-700 transition shadow-lg shadow-purple-200 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" /></svg>
                Buat Voucher
            </a>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/50">
                            <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider">Kode</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider">Potongan</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider">Event</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider">Penggunaan</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($vouchers as $voucher)
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="px-6 py-4">
                                    <span class="px-3 py-1 bg-purple-50 text-purple-700 font-mono font-bold rounded-lg border border-purple-100 uppercase tracking-wider">
                                        {{ $voucher->code }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-bold text-slate-900">
                                        {{ $voucher->type === 'percentage' ? $voucher->value . '%' : 'Rp ' . number_format($voucher->value, 0, ',', '.') }}
                                    </div>
                                    <div class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">{{ $voucher->type }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-slate-600">
                                        {{ $voucher->event->name ?? 'Semua Event' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-bold text-slate-900">{{ $voucher->used_count }} / {{ $voucher->max_usage ?? '∞' }}</div>
                                    <div class="w-24 h-1.5 bg-slate-100 rounded-full mt-1 overflow-hidden">
                                        <div class="h-full bg-purple-500 rounded-full" style="width: {{ $voucher->max_usage ? ($voucher->used_count / $voucher->max_usage) * 100 : 0 }}%"></div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-end gap-2">
                                        <form action="{{ route('organizer.vouchers.destroy', $voucher) }}" method="POST" onsubmit="return confirm('Hapus voucher ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 text-slate-400 hover:text-rose-600 bg-slate-50 rounded-lg border border-slate-100 transition">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-slate-400">
                                    Belum ada kode promo yang dibuat.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
