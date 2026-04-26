<x-app-layout>
    <x-slot name="title">Buat Voucher</x-slot>
    <x-slot name="header">Buat Voucher Baru</x-slot>

    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="p-8 border-b border-slate-50 bg-slate-50/50 text-center">
                <h3 class="text-xl font-bold text-slate-800">Detail Voucher</h3>
                <p class="text-sm text-slate-500">Tentukan kode unik dan besaran potongan harga.</p>
            </div>
            
            <form action="{{ route('organizer.vouchers.store') }}" method="POST" class="p-8 space-y-6">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Kode Voucher</label>
                        <input type="text" name="code" placeholder="CONTOH: PROMOHEMAT" class="w-full rounded-xl border-slate-200 focus:border-purple-500 focus:ring-purple-500 transition font-mono font-bold uppercase" required>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Tipe Potongan</label>
                        <select name="type" class="w-full rounded-xl border-slate-200 focus:border-purple-500 focus:ring-purple-500 transition">
                            <option value="percentage">Persentase (%)</option>
                            <option value="fixed">Nominal Tetap (Rp)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Nilai Potongan</label>
                        <input type="number" name="value" placeholder="Misal: 10 atau 50000" class="w-full rounded-xl border-slate-200 focus:border-purple-500 focus:ring-purple-500 transition" required>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Berlaku Untuk Event</label>
                        <select name="event_id" class="w-full rounded-xl border-slate-200 focus:border-purple-500 focus:ring-purple-500 transition">
                            <option value="">Semua Event</option>
                            @foreach($events as $event)
                                <option value="{{ $event->id }}">{{ $event->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Maksimal Penggunaan</label>
                        <input type="number" name="max_usage" placeholder="Kosongkan jika tak terbatas" class="w-full rounded-xl border-slate-200 focus:border-purple-500 focus:ring-purple-500 transition">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Status</label>
                        <select name="is_active" class="w-full rounded-xl border-slate-200 focus:border-purple-500 focus:ring-purple-500 transition">
                            <option value="1">Aktif</option>
                            <option value="0">Non-Aktif</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Tanggal Mulai</label>
                        <input type="datetime-local" name="start_at" class="w-full rounded-xl border-slate-200 focus:border-purple-500 focus:ring-purple-500 transition">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Tanggal Berakhir</label>
                        <input type="datetime-local" name="expires_at" class="w-full rounded-xl border-slate-200 focus:border-purple-500 focus:ring-purple-500 transition">
                    </div>
                </div>

                <div class="flex gap-4 pt-4">
                    <a href="{{ route('organizer.vouchers.index') }}" class="flex-1 py-3 px-6 bg-white border border-slate-200 text-slate-600 rounded-xl font-bold hover:bg-slate-50 transition text-center">
                        Batal
                    </a>
                    <button type="submit" class="flex-1 py-3 px-6 bg-purple-600 text-white rounded-xl font-bold hover:bg-purple-700 transition shadow-lg shadow-purple-200">
                        Simpan Voucher
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
