@php
    // Hanya tampilkan untuk role Penyedia Event yang punya tenant_id
    $showModal = false;
    $pendingInvoices = collect();
    
    if (auth()->check() && auth()->user()->hasRole('Penyedia Event') && auth()->user()->tenant_id) {
        $pendingInvoices = \App\Models\Invoice::where('tenant_id', auth()->user()->tenant_id)
            ->where('status', 'sent')
            ->whereNull('payment_confirmed_at')
            ->orderBy('due_date', 'asc')
            ->get();
        
        // Tampilkan modal jika ada tagihan pending & belum di-dismiss hari ini
        $dismissedKey = 'invoice_modal_dismissed_' . auth()->id();
        $dismissedAt  = session($dismissedKey);
        $showModal = $pendingInvoices->isNotEmpty() 
            && ($dismissedAt === null || !now()->isSameDay(\Carbon\Carbon::parse($dismissedAt)));
    }
@endphp

@if($showModal)
{{-- Backdrop --}}
<div id="invoice-notification-modal"
     class="fixed inset-0 z-[999999] flex items-center justify-center p-4"
     x-data="{ open: true }"
     x-show="open"
     x-cloak>
    
    {{-- Overlay --}}
    <div class="absolute inset-0 bg-slate-900/80 z-0"
         x-show="open"
         x-transition:enter="transition-opacity duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
    </div>

    {{-- Modal Card --}}
    <div class="relative z-10 w-full max-w-md"
         x-show="open"
         x-transition:enter="transition duration-300"
         x-transition:enter-start="opacity-0 scale-90 translate-y-4"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         x-transition:leave="transition duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95">

        <div class="bg-white rounded-3xl shadow-2xl overflow-hidden">
            {{-- Top gradient banner --}}
            <div class="relative bg-gradient-to-br from-orange-500 via-orange-600 to-amber-600 px-6 pt-8 pb-16">
                {{-- Decorative circles --}}
                <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -translate-y-8 translate-x-8"></div>
                <div class="absolute bottom-0 left-0 w-24 h-24 bg-black/10 rounded-full translate-y-8 -translate-x-8"></div>
                
                {{-- Pulsing bell icon --}}
                <div class="relative z-10 flex items-center gap-4 mb-4">
                    <div class="w-14 h-14 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center border border-white/30 shadow-lg">
                        <svg class="w-7 h-7 text-white animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                    </div>
                    <div class="text-white">
                        <p class="text-[10px] font-black uppercase tracking-[0.2em] text-orange-100 opacity-80">Notifikasi Tagihan</p>
                        <p class="text-xl font-black leading-tight">
                            {{ $pendingInvoices->count() > 1 ? $pendingInvoices->count() . ' Invoice Menunggu' : 'Invoice Baru!' }}
                        </p>
                    </div>
                </div>

                {{-- Total amount highlight --}}
                @php $grandTotal = $pendingInvoices->sum('total_amount'); @endphp
                <div class="relative z-10">
                    <p class="text-orange-100 text-xs font-semibold mb-0.5">Total Tagihan</p>
                    <p class="text-3xl font-black text-white">Rp {{ number_format($grandTotal, 0, ',', '.') }}</p>
                </div>
            </div>

            {{-- Content --}}
            <div class="-mt-8 relative z-10 px-6 pb-6">
                {{-- Invoice cards list --}}
                <div class="space-y-2 mb-5">
                    @foreach($pendingInvoices->take(3) as $inv)
                        <div class="bg-white rounded-2xl border border-slate-100 shadow-md p-4 flex items-center justify-between gap-3">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-9 h-9 rounded-xl {{ $inv->is_overdue ? 'bg-red-100' : 'bg-amber-50' }} flex items-center justify-center shrink-0">
                                    <svg class="w-4 h-4 {{ $inv->is_overdue ? 'text-red-500' : 'text-amber-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-xs font-black text-slate-700 truncate">{{ $inv->title }}</p>
                                    <p class="text-[11px] {{ $inv->is_overdue ? 'text-red-500 font-bold' : 'text-slate-400' }}">
                                        {{ $inv->is_overdue ? '⚠ Terlambat · ' : '' }}Jatuh tempo {{ $inv->due_date->format('d M Y') }}
                                    </p>
                                </div>
                            </div>
                            <div class="text-right shrink-0">
                                <p class="text-sm font-black text-slate-800">{{ $inv->formatted_total }}</p>
                                @if($inv->payment_proof)
                                    <p class="text-[10px] text-blue-500 font-bold">Menunggu konfirmasi</p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                    @if($pendingInvoices->count() > 3)
                        <p class="text-xs text-slate-400 text-center font-semibold">+{{ $pendingInvoices->count() - 3 }} invoice lainnya</p>
                    @endif
                </div>

                {{-- Action Buttons --}}
                <div class="space-y-2">
                    @if($pendingInvoices->count() === 1)
                        <a href="{{ route('organizer.invoices.show', $pendingInvoices->first()) }}"
                           class="flex items-center justify-center gap-2 w-full py-3.5 bg-gradient-to-r from-orange-500 to-amber-500 text-white rounded-2xl font-black text-sm hover:from-orange-600 hover:to-amber-600 transition shadow-lg shadow-orange-200">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            Lihat & Bayar Invoice
                        </a>
                    @else
                        <a href="{{ route('organizer.invoices.index') }}"
                           class="flex items-center justify-center gap-2 w-full py-3.5 bg-gradient-to-r from-orange-500 to-amber-500 text-white rounded-2xl font-black text-sm hover:from-orange-600 hover:to-amber-600 transition shadow-lg shadow-orange-200">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            Lihat Semua Invoice
                        </a>
                    @endif

                    {{-- Dismiss (session per hari) --}}
                    <form method="POST" action="{{ route('organizer.invoices.dismiss-modal') }}">
                        @csrf
                        <button type="submit"
                                class="w-full py-2.5 text-slate-400 text-sm font-semibold hover:text-slate-600 transition !bg-transparent !text-slate-400 hover:!bg-slate-50 rounded-2xl">
                            Ingatkan lagi besok
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
