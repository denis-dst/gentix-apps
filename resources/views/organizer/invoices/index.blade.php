<x-app-layout>
    <x-slot name="title">Invoice & Tagihan</x-slot>

    <div class="space-y-6 animate-in fade-in slide-in-from-bottom-4 duration-500">

        {{-- Header --}}
        <div class="relative overflow-hidden rounded-3xl p-6 text-white shadow-xl" style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);">
            <div class="relative z-10">
                <p class="text-[10px] font-black uppercase tracking-[0.3em] text-orange-400 mb-1">Keuangan</p>
                <h2 class="text-2xl font-black font-outfit text-white">Invoice & Tagihan</h2>
                <p class="text-sm text-slate-300 mt-1">Daftar tagihan dari pengelola platform</p>
            </div>
            <div class="absolute -bottom-8 -right-8 w-40 h-40 bg-white/5 rounded-full"></div>
        </div>

        @if(session('success'))
            <div class="flex items-center gap-3 p-4 bg-green-50 border border-green-200 rounded-2xl text-green-800 text-sm font-semibold">
                <svg class="w-5 h-5 text-green-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                {{ session('success') }}
            </div>
        @endif

        {{-- Invoice List --}}
        @if($invoices->isEmpty())
            <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-16 text-center">
                <div class="w-16 h-16 bg-slate-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <p class="text-slate-500 font-semibold">Belum ada invoice</p>
                <p class="text-sm text-slate-400 mt-1">Invoice dari admin akan muncul di sini</p>
            </div>
        @else
            <div class="space-y-3">
                @foreach($invoices as $invoice)
                    @php
                        $statusColors = [
                            'sent'      => 'border-amber-200 bg-amber-50/30',
                            'paid'      => 'border-green-200 bg-green-50/30',
                            'cancelled' => 'border-red-200 bg-red-50/30',
                        ];
                        $badgeColors = [
                            'sent'      => 'bg-amber-100 text-amber-700',
                            'paid'      => 'bg-green-100 text-green-700',
                            'cancelled' => 'bg-red-100 text-red-600',
                        ];
                    @endphp
                    <div class="bg-white rounded-2xl border {{ $statusColors[$invoice->status] ?? 'border-slate-100' }} shadow-sm p-5 hover:shadow-md transition-all">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div class="flex items-start gap-4">
                                <div class="w-11 h-11 rounded-xl
                                    {{ $invoice->status === 'paid' ? 'bg-green-100' : ($invoice->is_overdue ? 'bg-red-100' : 'bg-amber-100') }}
                                    flex items-center justify-center shrink-0">
                                    @if($invoice->status === 'paid')
                                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    @elseif($invoice->is_overdue)
                                        <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    @else
                                        <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                    @endif
                                </div>
                                <div>
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="font-mono text-xs font-black text-slate-600">{{ $invoice->invoice_number }}</span>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-[10px] font-black uppercase tracking-wider {{ $badgeColors[$invoice->status] ?? 'bg-slate-100 text-slate-600' }}">
                                            {{ $invoice->status_label }}
                                        </span>
                                        @if($invoice->is_overdue)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-[10px] font-black uppercase tracking-wider bg-red-100 text-red-600">
                                                Terlambat
                                            </span>
                                        @endif
                                    </div>
                                    <p class="font-semibold text-slate-800 mt-0.5">{{ $invoice->title }}</p>
                                    <p class="text-xs text-slate-400 mt-0.5">
                                        Diterbitkan {{ $invoice->issued_date->format('d M Y') }} · Jatuh tempo {{ $invoice->due_date->format('d M Y') }}
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3 sm:flex-col sm:items-end">
                                <span class="text-xl font-black text-slate-800">{{ $invoice->formatted_total }}</span>
                                <a href="{{ route('organizer.invoices.show', $invoice) }}"
                                   class="px-4 py-2 {{ $invoice->status === 'sent' ? 'bg-orange-500 text-white shadow-md shadow-orange-200' : 'bg-slate-100 text-slate-700' }} rounded-xl text-xs font-bold hover:opacity-90 transition whitespace-nowrap">
                                    {{ $invoice->status === 'sent' ? 'Bayar Sekarang' : 'Lihat Detail' }}
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div>{{ $invoices->links() }}</div>
        @endif
    </div>
</x-app-layout>
