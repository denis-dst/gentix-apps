<x-app-layout>
    <x-slot name="title">Edit Invoice</x-slot>

    <div class="max-w-4xl mx-auto space-y-6 animate-in fade-in slide-in-from-bottom-4 duration-500">

        {{-- Header --}}
        <div class="flex items-center gap-4">
            <a href="{{ route('superadmin.invoices.show', $invoice) }}"
               class="p-2 text-slate-400 hover:text-slate-700 hover:bg-slate-100 rounded-xl transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div>
                <h2 class="text-2xl font-black text-slate-800 font-outfit">Edit Invoice</h2>
                <p class="text-sm text-slate-500 font-mono">{{ $invoice->invoice_number }}</p>
            </div>
        </div>

        <form id="invoice-form" method="POST" action="{{ route('superadmin.invoices.update', $invoice) }}">
            @csrf
            @method('PUT')

            <div class="space-y-5">
                {{-- Section: Info Umum --}}
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 space-y-5">
                    <h3 class="text-sm font-black text-slate-700 uppercase tracking-wider flex items-center gap-2">
                        <span class="w-6 h-6 bg-orange-100 text-orange-600 rounded-lg flex items-center justify-center text-xs">1</span>
                        Informasi Invoice
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold text-slate-600 mb-1.5 uppercase tracking-wider">Tenant / Penyedia Event <span class="text-red-500">*</span></label>
                            <select name="tenant_id" required
                                    class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:border-transparent bg-white">
                                <option value="">Pilih Tenant</option>
                                @foreach($tenants as $tenant)
                                    <option value="{{ $tenant->id }}" {{ old('tenant_id', $invoice->tenant_id) == $tenant->id ? 'selected' : '' }}>
                                        {{ $tenant->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold text-slate-600 mb-1.5 uppercase tracking-wider">Judul Invoice <span class="text-red-500">*</span></label>
                            <input type="text" name="title" value="{{ old('title', $invoice->title) }}" required
                                   class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:border-transparent">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-600 mb-1.5 uppercase tracking-wider">Tanggal Diterbitkan <span class="text-red-500">*</span></label>
                            <input type="date" name="issued_date" value="{{ old('issued_date', $invoice->issued_date->format('Y-m-d')) }}" required
                                   class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:border-transparent">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-600 mb-1.5 uppercase tracking-wider">Tanggal Jatuh Tempo <span class="text-red-500">*</span></label>
                            <input type="date" name="due_date" value="{{ old('due_date', $invoice->due_date->format('Y-m-d')) }}" required
                                   class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:border-transparent">
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold text-slate-600 mb-1.5 uppercase tracking-wider">Deskripsi (Opsional)</label>
                            <textarea name="description" rows="3"
                                      class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:border-transparent resize-none">{{ old('description', $invoice->description) }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- Section: Item Tagihan --}}
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 space-y-4">
                    <h3 class="text-sm font-black text-slate-700 uppercase tracking-wider flex items-center gap-2">
                        <span class="w-6 h-6 bg-orange-100 text-orange-600 rounded-lg flex items-center justify-center text-xs">2</span>
                        Item Tagihan
                    </h3>

                    <div id="items-container" class="space-y-3"></div>

                    <button type="button" id="add-item-btn"
                            class="flex items-center gap-2 text-sm text-orange-500 font-bold hover:text-orange-700 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Tambah Item
                    </button>

                    <div class="border-t border-slate-100 pt-4 space-y-2">
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-slate-500 font-semibold">Subtotal</span>
                            <span id="display-subtotal" class="font-bold text-slate-700">Rp 0</span>
                        </div>
                        <div class="flex justify-between items-center text-sm">
                            <div class="flex items-center gap-2">
                                <span class="text-slate-500 font-semibold">Pajak (%)</span>
                                <input type="number" name="tax_percent" id="tax-percent" value="{{ old('tax_percent', $invoice->tax_percent) }}"
                                       min="0" max="100" step="0.01"
                                       class="w-20 border border-slate-200 rounded-lg px-2 py-1 text-sm text-center focus:ring-2 focus:ring-orange-400 focus:border-transparent">
                            </div>
                            <span id="display-tax" class="font-bold text-slate-700">Rp 0</span>
                        </div>
                        <div class="flex justify-between items-center pt-2 border-t border-slate-200">
                            <span class="text-base font-black text-slate-800">TOTAL</span>
                            <span id="display-total" class="text-xl font-black text-orange-600">Rp 0</span>
                        </div>
                    </div>
                </div>

                {{-- Section: Catatan --}}
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
                    <h3 class="text-sm font-black text-slate-700 uppercase tracking-wider flex items-center gap-2 mb-4">
                        <span class="w-6 h-6 bg-orange-100 text-orange-600 rounded-lg flex items-center justify-center text-xs">3</span>
                        Catatan & Instruksi Pembayaran (Opsional)
                    </h3>
                    <input id="notes_input" type="hidden" name="notes" value="{{ old('notes', $invoice->notes) }}">
                    <trix-editor input="notes_input"
                        class="trix-content bg-white rounded-xl border border-slate-200 focus-within:border-orange-400 transition-all text-slate-600 leading-relaxed px-4 py-3"
                        placeholder="Instruksi pembayaran, nomor rekening, atau catatan lain..."
                        style="min-height:160px;"></trix-editor>
                </div>

                <div class="flex flex-wrap gap-3 justify-end">
                    <a href="{{ route('superadmin.invoices.show', $invoice) }}"
                       class="px-5 py-2.5 border border-slate-200 text-slate-600 rounded-xl font-bold text-sm hover:bg-slate-50 transition">
                        Batal
                    </a>
                    <button type="submit" name="action" value="draft"
                            class="!bg-slate-700 !text-white px-5 py-2.5 rounded-xl font-bold text-sm hover:!bg-slate-900 transition">
                        Simpan Draft
                    </button>
                    <button type="submit" name="action" value="send"
                            class="!bg-orange-500 !text-white px-6 py-2.5 rounded-xl font-bold text-sm hover:!bg-orange-600 transition shadow-lg shadow-orange-200">
                        Simpan & Terbitkan
                    </button>
                </div>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
        let itemCount = 0;
        const existingItems = @json($invoice->items);

        function formatRupiah(num) {
            return 'Rp ' + Math.round(num).toLocaleString('id-ID');
        }

        function recalculate() {
            let subtotal = 0;
            document.querySelectorAll('.item-row').forEach(row => {
                const qty   = parseFloat(row.querySelector('.item-qty').value)   || 0;
                const price = parseFloat(row.querySelector('.item-price').value) || 0;
                const sub   = qty * price;
                subtotal += sub;
                row.querySelector('.item-subtotal-display').textContent = formatRupiah(sub);
            });

            const taxPct = parseFloat(document.getElementById('tax-percent').value) || 0;
            const taxAmt = subtotal * (taxPct / 100);
            const total  = subtotal + taxAmt;

            document.getElementById('display-subtotal').textContent = formatRupiah(subtotal);
            document.getElementById('display-tax').textContent = formatRupiah(taxAmt);
            document.getElementById('display-total').textContent = formatRupiah(total);
        }

        function addItem(desc = '', qty = 1, price = 0) {
            const container = document.getElementById('items-container');
            const idx = itemCount++;
            const row = document.createElement('div');
            row.className = 'item-row grid grid-cols-12 gap-2 items-start bg-slate-50 rounded-xl p-3';
            row.innerHTML = `
                <div class="col-span-12 md:col-span-5">
                    <label class="text-[10px] font-black uppercase text-slate-400 tracking-wider block mb-1">Deskripsi Item</label>
                    <input type="text" name="items[${idx}][description]" value="${desc}" placeholder="Nama layanan / tagihan" required
                           class="w-full border border-slate-200 bg-white rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-orange-400 focus:border-transparent">
                </div>
                <div class="col-span-4 md:col-span-2">
                    <label class="text-[10px] font-black uppercase text-slate-400 tracking-wider block mb-1">Qty</label>
                    <input type="number" name="items[${idx}][quantity]" value="${qty}" min="0.01" step="0.01" required
                           class="item-qty w-full border border-slate-200 bg-white rounded-lg px-3 py-2 text-sm text-center focus:ring-2 focus:ring-orange-400 focus:border-transparent">
                </div>
                <div class="col-span-8 md:col-span-3">
                    <label class="text-[10px] font-black uppercase text-slate-400 tracking-wider block mb-1">Harga Satuan (Rp)</label>
                    <input type="number" name="items[${idx}][unit_price]" value="${price}" min="0" required
                           class="item-price w-full border border-slate-200 bg-white rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-orange-400 focus:border-transparent">
                </div>
                <div class="col-span-8 md:col-span-1 text-right">
                    <label class="text-[10px] font-black uppercase text-slate-400 tracking-wider block mb-1">Subtotal</label>
                    <span class="item-subtotal-display text-sm font-bold text-slate-700">Rp 0</span>
                </div>
                <div class="col-span-4 md:col-span-1 flex items-end justify-end pb-0.5">
                    <button type="button" class="remove-item p-2 text-slate-300 hover:text-red-500 hover:bg-red-50 rounded-lg transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>
                </div>
            `;
            container.appendChild(row);
            row.querySelector('.item-qty').addEventListener('input', recalculate);
            row.querySelector('.item-price').addEventListener('input', recalculate);
            row.querySelector('.remove-item').addEventListener('click', () => { row.remove(); recalculate(); });
            recalculate();
        }

        document.getElementById('add-item-btn').addEventListener('click', () => addItem());
        document.getElementById('tax-percent').addEventListener('input', recalculate);

        // Sync Trix editor value to hidden input before submit
        document.getElementById('invoice-form').addEventListener('submit', function () {
            const trix = document.querySelector('trix-editor[input="notes_input"]');
            if (trix) {
                document.getElementById('notes_input').value = trix.value || '';
            }
        });

        // Load existing items
        existingItems.forEach(item => addItem(item.description, item.quantity, item.unit_price));
    </script>
    @endpush
</x-app-layout>
