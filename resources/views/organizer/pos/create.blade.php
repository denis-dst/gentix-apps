<x-app-layout>
    <x-slot name="title">Jual Tiket: {{ $event->name }}</x-slot>

    @php
        $flowLabels = [
            'redeem' => 'Perlu Redeem',
            'evoucher' => 'E-Voucher',
            'print' => 'Cetak Ticket Langsung',
        ];
        $flow = $event->purchase_flow ?? 'redeem';
    @endphp

    <div class="max-w-5xl mx-auto space-y-6 animate-in fade-in slide-in-from-bottom-4 duration-700">
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
            <div>
                <p class="text-[10px] font-black text-orange-500 uppercase tracking-[0.25em] mb-2">{{ $flowLabels[$flow] ?? 'Perlu Redeem' }}</p>
                <h2 class="text-3xl font-black text-slate-800 font-outfit">{{ $event->name }}</h2>
                <p class="text-sm text-slate-500 font-medium mt-1">{{ $event->venue }} · {{ $event->city }}</p>
            </div>
            <a href="{{ route('organizer.pos.index') }}" class="px-5 py-2.5 bg-white border border-slate-200 text-slate-700 rounded-xl font-bold hover:bg-slate-50 transition shadow-sm text-center">
                Ganti Event
            </a>
        </div>

        @if(session('success'))
            <div class="bg-emerald-50 border border-emerald-100 text-emerald-700 px-4 py-3 rounded-2xl text-sm font-bold space-y-3">
                <div>{{ session('success') }}</div>
                @if(session('active_evoucher_url'))
                    <div class="flex flex-col md:flex-row gap-2 md:items-center">
                        <input type="text" readonly value="{{ session('active_evoucher_url') }}" class="flex-1 rounded-lg border-emerald-200 bg-white text-xs font-bold text-slate-600">
                        <a href="{{ session('active_evoucher_url') }}" target="_blank" class="px-4 py-2 bg-emerald-600 text-white rounded-lg text-xs font-black uppercase tracking-wider hover:bg-emerald-700 transition text-center">Buka QR</a>
                    </div>
                @endif
            </div>
        @endif

        @if(session('error'))
            <div class="bg-rose-50 border border-rose-100 text-rose-700 px-4 py-3 rounded-2xl text-sm font-bold">
                {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="p-4 bg-red-50 border-l-4 border-red-500 rounded-2xl">
                <h3 class="text-sm font-bold text-red-800">Terdapat kesalahan pengisian form:</h3>
                <ul class="mt-2 list-disc list-inside text-xs text-red-700 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <form action="{{ route('organizer.pos.store', $event) }}" method="POST" class="lg:col-span-2 bg-white rounded-2xl border border-slate-100 shadow-sm p-6 space-y-5">
                @csrf

                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Kategori Tiket</label>
                    <select name="ticket_category_id" class="w-full rounded-xl border-slate-200 focus:border-orange-500 focus:ring-orange-500 text-sm font-bold" required>
                        <option value="">Pilih kategori</option>
                        @foreach($event->ticketCategories as $category)
                            <option value="{{ $category->id }}" {{ old('ticket_category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }} · Rp {{ number_format($category->price, 0, ',', '.') }} · Sisa {{ max(0, $category->quota - $category->sold_count) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Jumlah</label>
                        <input type="number" name="quantity" value="{{ old('quantity', 1) }}" min="1" max="100" class="w-full rounded-xl border-slate-200 focus:border-orange-500 focus:ring-orange-500 font-bold" required>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Metode Bayar</label>
                        <select name="payment_method" class="w-full rounded-xl border-slate-200 focus:border-orange-500 focus:ring-orange-500 text-sm font-bold" required>
                            @foreach(['Tunai', 'QRIS', 'Debit', 'Transfer', 'EDC'] as $method)
                                <option value="{{ $method }}" {{ old('payment_method', 'Tunai') === $method ? 'selected' : '' }}>{{ $method }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Nama Pembeli</label>
                        <input type="text" name="customer_name" value="{{ old('customer_name') }}" class="w-full rounded-xl border-slate-200 focus:border-orange-500 focus:ring-orange-500" required>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">No HP</label>
                        <input type="text" name="customer_phone" value="{{ old('customer_phone') }}" class="w-full rounded-xl border-slate-200 focus:border-orange-500 focus:ring-orange-500" required>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Email</label>
                        <input type="email" name="customer_email" value="{{ old('customer_email') }}" class="w-full rounded-xl border-slate-200 focus:border-orange-500 focus:ring-orange-500" required>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">NIK (Opsional)</label>
                        <input type="text" name="customer_nik" value="{{ old('customer_nik') }}" class="w-full rounded-xl border-slate-200 focus:border-orange-500 focus:ring-orange-500">
                    </div>
                </div>

                <button type="submit" class="w-full py-4 bg-orange-600 text-white rounded-xl font-black hover:bg-orange-700 transition shadow-lg shadow-orange-200 uppercase tracking-wider">
                    Proses Transaksi
                </button>
            </form>

            <aside class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 space-y-4">
                <h3 class="text-sm font-black text-slate-800 uppercase tracking-wider">Ringkasan Flow</h3>
                <div class="p-4 rounded-2xl bg-orange-50 border border-orange-100">
                    <div class="text-sm font-black text-orange-700">{{ $flowLabels[$flow] ?? 'Perlu Redeem' }}</div>
                    <p class="text-xs text-orange-700/70 mt-1 leading-relaxed">
                        @if($flow === 'redeem')
                            Setelah dibeli, e-voucher perlu diredeem menjadi wristband sebelum dipakai di gate.
                        @elseif($flow === 'evoucher')
                            Tiket langsung memiliki QR yang bisa discan di gate seperti event gratis.
                        @else
                            Setelah transaksi, sistem membuka halaman cetak termal berisi QR scan gate.
                        @endif
                    </p>
                </div>
                @if($flow === 'print')
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100">
                        <div class="text-xs font-bold text-slate-400 uppercase tracking-wider">Ukuran Kertas</div>
                        <div class="text-xl font-black text-slate-800 mt-1">{{ $event->thermal_paper_width_mm ?? 80 }} x {{ $event->thermal_paper_height_mm ?? 160 }} mm</div>
                    </div>
                @endif
            </aside>
        </div>
    </div>
</x-app-layout>
