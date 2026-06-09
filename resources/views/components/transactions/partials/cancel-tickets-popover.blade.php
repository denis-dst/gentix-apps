@props([
    'action',
    'cancelableTickets',
    'heading' => 'Batalkan Tiket Peserta',
    'referenceNo' => '-',
    'summaryClass' => '',
    'summaryTitle' => 'Cancel Transaksi (Satuan / Semua)',
    'summaryText' => null,
    'confirmMessage' => 'Apakah Anda yakin ingin membatalkan tiket yang dipilih? Kuota akan otomatis dikembalikan ke stok.',
])

@once
    <style>
        .cancel-ticket-summary::-webkit-details-marker { display: none; }
        .cancel-ticket-summary::marker { content: ''; }
    </style>
@endonce

<details class="inline-block align-middle">
    <summary
        title="{{ $summaryTitle }}"
        class="cancel-ticket-summary {{ $summaryClass }} {{ $summaryText ? 'inline-flex items-center justify-center' : 'inline-flex items-center justify-center w-9 h-9' }} cursor-pointer select-none leading-none"
        style="list-style: none;"
    >
        @if($summaryText)
            {{ $summaryText }}
        @else
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
        @endif
    </summary>

    <div class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm"></div>
        <div class="relative bg-white rounded-3xl shadow-2xl border border-slate-100 max-w-xl w-full mx-4 overflow-hidden">
            <form method="POST" action="{{ $action }}" onsubmit="return confirm('{{ $confirmMessage }}')">
                @csrf
                <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                    <div>
                        <h3 class="text-lg font-black text-slate-800 font-outfit">{{ $heading }}</h3>
                        <p class="text-xs text-slate-500 font-medium mt-0.5">Invoice: {{ $referenceNo }}</p>
                    </div>
                </div>

                <div class="p-6 space-y-4 max-h-[350px] overflow-y-auto custom-scrollbar">
                    <p class="text-xs font-semibold text-slate-500">Pilih tiket peserta yang ingin dibatalkan. Kuota tiket akan dikembalikan secara otomatis.</p>

                    <div class="divide-y divide-slate-100 border border-slate-100 rounded-2xl overflow-hidden bg-white">
                        @forelse($cancelableTickets as $ticket)
                            <label class="flex items-start gap-3 p-3.5 hover:bg-slate-50/50 transition cursor-pointer select-none">
                                <input type="checkbox" name="ticket_ids[]" value="{{ $ticket['id'] }}" class="mt-0.5 rounded border-slate-300 text-orange-600 focus:ring-orange-500 h-4 w-4">
                                <span class="flex-1">
                                    <span class="flex items-center justify-between gap-3">
                                        <span class="text-xs font-mono font-black text-slate-700">{{ $ticket['code'] ?? '-' }}</span>
                                        <span class="px-2 py-0.5 rounded bg-orange-50 text-orange-600 font-black text-[9px] uppercase tracking-wider">{{ $ticket['category'] ?? '-' }}</span>
                                    </span>
                                    <span class="block text-xs font-black text-slate-800 uppercase mt-1">{{ $ticket['name'] ?? '-' }}</span>
                                </span>
                            </label>
                        @empty
                            <div class="p-4 text-center text-xs font-bold text-slate-400">Tidak ada tiket aktif yang dapat dibatalkan.</div>
                        @endforelse
                    </div>
                </div>

                <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50 flex items-center justify-end gap-3">
                    <button type="button" onclick="this.closest('details').removeAttribute('open')" class="px-4 py-2.5 bg-slate-200 hover:bg-slate-300 text-slate-700 rounded-xl text-xs font-black uppercase tracking-wider transition">Batal</button>
                    <button type="submit" class="px-5 py-2.5 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-black uppercase tracking-wider transition shadow-lg shadow-rose-200">Konfirmasi Pembatalan</button>
                </div>
            </form>
        </div>
    </div>
</details>
