<x-app-layout>
    <x-slot name="title">Manajemen Invoice</x-slot>

    <div class="space-y-6 animate-in fade-in slide-in-from-bottom-4 duration-500">

        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl font-black text-slate-800 font-outfit">Invoice Tenant</h2>
                <p class="text-sm text-slate-500 mt-1">Kelola tagihan yang diterbitkan untuk setiap Penyedia Event</p>
            </div>
            <a href="{{ route('superadmin.invoices.create') }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 bg-orange-500 text-white rounded-xl font-bold text-sm hover:bg-orange-600 transition shadow-lg shadow-orange-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Buat Invoice Baru
            </a>
        </div>

        {{-- Flash Message --}}
        @if(session('success'))
            <div class="flex items-center gap-3 p-4 bg-green-50 border border-green-200 rounded-2xl text-green-800 text-sm font-semibold">
                <svg class="w-5 h-5 text-green-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                {{ session('success') }}
            </div>
        @endif

        {{-- Filters --}}
        <form method="GET" class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4 flex flex-wrap gap-3 items-end">
            <div class="flex-1 min-w-[180px]">
                <label class="text-xs font-black text-slate-500 uppercase tracking-wider block mb-1.5">Tenant</label>
                <select name="tenant_id" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-orange-400 focus:border-transparent">
                    <option value="">Semua Tenant</option>
                    @foreach($tenants as $tenant)
                        <option value="{{ $tenant->id }}" {{ request('tenant_id') == $tenant->id ? 'selected' : '' }}>
                            {{ $tenant->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex-1 min-w-[150px]">
                <label class="text-xs font-black text-slate-500 uppercase tracking-wider block mb-1.5">Status</label>
                <select name="status" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-orange-400 focus:border-transparent">
                    <option value="">Semua Status</option>
                    <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="sent" {{ request('status') === 'sent' ? 'selected' : '' }}>Terkirim</option>
                    <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Lunas</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                </select>
            </div>
            <button type="submit" class="px-5 py-2 bg-slate-800 text-white rounded-xl text-sm font-bold hover:bg-slate-700 transition">
                Filter
            </button>
            @if(request()->hasAny(['tenant_id', 'status']))
                <a href="{{ route('superadmin.invoices.index') }}" class="px-4 py-2 text-slate-500 text-sm font-semibold hover:text-slate-800 transition">Reset</a>
            @endif
        </form>

        {{-- Stats Cards --}}
        @php
            $allInvoices = \App\Models\Invoice::get();
            $statDraft = $allInvoices->where('status','draft')->count();
            $statSent  = $allInvoices->where('status','sent')->count();
            $statPaid  = $allInvoices->where('status','paid')->count();
            $totalRevenue = $allInvoices->where('status','paid')->sum('total_amount');
        @endphp
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
                <p class="text-[10px] font-black uppercase text-slate-400 tracking-widest mb-1">Draft</p>
                <p class="text-3xl font-black text-slate-700 font-outfit">{{ $statDraft }}</p>
            </div>
            <div class="bg-amber-50 rounded-2xl border border-amber-100 shadow-sm p-5">
                <p class="text-[10px] font-black uppercase text-amber-500 tracking-widest mb-1">Terkirim</p>
                <p class="text-3xl font-black text-amber-700 font-outfit">{{ $statSent }}</p>
            </div>
            <div class="bg-green-50 rounded-2xl border border-green-100 shadow-sm p-5">
                <p class="text-[10px] font-black uppercase text-green-500 tracking-widest mb-1">Lunas</p>
                <p class="text-3xl font-black text-green-700 font-outfit">{{ $statPaid }}</p>
            </div>
            <div class="bg-gradient-to-br from-orange-500 to-amber-500 rounded-2xl shadow-md p-5">
                <p class="text-[10px] font-black uppercase text-orange-100 tracking-widest mb-1">Total Terkumpul</p>
                <p class="text-xl font-black text-white font-outfit">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</p>
            </div>
        </div>

        {{-- Table --}}
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
            @if($invoices->isEmpty())
                <div class="flex flex-col items-center justify-center py-20 text-center">
                    <div class="w-16 h-16 bg-slate-100 rounded-2xl flex items-center justify-center mb-4">
                        <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <p class="text-slate-500 font-semibold">Belum ada invoice diterbitkan</p>
                    <a href="{{ route('superadmin.invoices.create') }}" class="mt-4 text-orange-500 font-bold text-sm hover:underline">Buat invoice pertama →</a>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-slate-100 bg-slate-50">
                                <th class="text-left px-6 py-4 text-[10px] font-black uppercase text-slate-400 tracking-widest">No. Invoice</th>
                                <th class="text-left px-4 py-4 text-[10px] font-black uppercase text-slate-400 tracking-widest">Tenant</th>
                                <th class="text-left px-4 py-4 text-[10px] font-black uppercase text-slate-400 tracking-widest">Judul</th>
                                <th class="text-right px-4 py-4 text-[10px] font-black uppercase text-slate-400 tracking-widest">Total</th>
                                <th class="text-center px-4 py-4 text-[10px] font-black uppercase text-slate-400 tracking-widest">Jatuh Tempo</th>
                                <th class="text-center px-4 py-4 text-[10px] font-black uppercase text-slate-400 tracking-widest">Status</th>
                                <th class="text-center px-4 py-4 text-[10px] font-black uppercase text-slate-400 tracking-widest">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @foreach($invoices as $invoice)
                                <tr class="hover:bg-slate-50/50 transition group">
                                    <td class="px-6 py-4">
                                        <span class="font-bold text-slate-700 font-mono text-xs">{{ $invoice->invoice_number }}</span>
                                    </td>
                                    <td class="px-4 py-4">
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-8 h-8 rounded-lg bg-orange-100 flex items-center justify-center shrink-0">
                                                <span class="text-xs font-black text-orange-600">{{ substr($invoice->tenant->name, 0, 1) }}</span>
                                            </div>
                                            <span class="font-semibold text-slate-800 text-xs">{{ $invoice->tenant->name }}</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4">
                                        <span class="text-slate-600 text-xs">{{ Str::limit($invoice->title, 40) }}</span>
                                    </td>
                                    <td class="px-4 py-4 text-right">
                                        <span class="font-black text-slate-800">{{ $invoice->formatted_total }}</span>
                                    </td>
                                    <td class="px-4 py-4 text-center">
                                        <span class="text-xs {{ $invoice->is_overdue ? 'text-red-600 font-bold' : 'text-slate-500' }}">
                                            {{ $invoice->due_date->format('d M Y') }}
                                            @if($invoice->is_overdue)
                                                <span class="block text-[10px]">TERLAMBAT</span>
                                            @endif
                                        </span>
                                    </td>
                                    <td class="px-4 py-4 text-center">
                                        @php
                                            $colors = [
                                                'draft'     => 'bg-slate-100 text-slate-600',
                                                'sent'      => 'bg-amber-100 text-amber-700',
                                                'paid'      => 'bg-green-100 text-green-700',
                                                'cancelled' => 'bg-red-100 text-red-600',
                                            ];
                                        @endphp
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[10px] font-black uppercase tracking-wider {{ $colors[$invoice->status] ?? 'bg-slate-100 text-slate-600' }}">
                                            {{ $invoice->status_label }}
                                        </span>
                                        @if($invoice->payment_proof && $invoice->status === 'sent')
                                            <span class="block mt-1 text-[10px] text-blue-500 font-bold">Ada Bukti Bayar</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-4 text-center">
                                        <div class="flex items-center justify-center gap-1.5">
                                            <a href="{{ route('superadmin.invoices.show', $invoice) }}"
                                               class="p-1.5 text-slate-400 hover:text-orange-500 hover:bg-orange-50 rounded-lg transition" title="Lihat Detail">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                            </a>
                                            <a href="{{ route('superadmin.invoices.download-pdf', $invoice) }}"
                                               class="p-1.5 text-slate-400 hover:text-blue-500 hover:bg-blue-50 rounded-lg transition" title="Download PDF">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                                </svg>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="px-6 py-4 border-t border-slate-100">
                    {{ $invoices->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
