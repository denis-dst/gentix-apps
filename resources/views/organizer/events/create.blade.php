<x-app-layout>
    <x-slot name="title">Buat Event Baru</x-slot>
    <x-slot name="header">Buat Event</x-slot>

    <div class="max-w-3xl mx-auto">
        <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 overflow-hidden">
            <div class="p-8 border-b border-slate-50 bg-slate-50/50 flex items-center gap-6">
                <div class="w-16 h-16 bg-purple-600 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-purple-200">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" /></svg>
                </div>
                <div>
                    <h3 class="text-2xl font-black text-slate-800 font-outfit">Detail Event Baru</h3>
                    <p class="text-sm text-slate-500 font-medium">Lengkapi informasi dasar event Anda.</p>
                </div>
            </div>

            <form action="{{ route('organizer.events.store') }}" method="POST" class="p-8 space-y-8">
                @csrf
                
                <div class="space-y-6">
                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-[0.2em] mb-2">Nama Event</label>
                        <input type="text" name="name" placeholder="Contoh: Konser Musik Bhayangkara" class="w-full rounded-2xl border-slate-200 focus:border-purple-500 focus:ring-purple-500 transition py-4 px-6 text-lg font-bold" required>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-bold text-slate-400 uppercase tracking-[0.2em] mb-2">Venue / Lokasi</label>
                            <input type="text" name="venue" placeholder="Contoh: Stadion Utama" class="w-full rounded-2xl border-slate-200 focus:border-purple-500 focus:ring-purple-500 transition py-3 px-5" required>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-400 uppercase tracking-[0.2em] mb-2">Kota</label>
                            <input type="text" name="city" placeholder="Contoh: Bandar Lampung" class="w-full rounded-2xl border-slate-200 focus:border-purple-500 focus:ring-purple-500 transition py-3 px-5" required>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-bold text-slate-400 uppercase tracking-[0.2em] mb-2">Waktu Mulai</label>
                            <input type="datetime-local" name="event_start_date" class="w-full rounded-2xl border-slate-200 focus:border-purple-500 focus:ring-purple-500 transition py-3 px-5" required>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-400 uppercase tracking-[0.2em] mb-2">Waktu Selesai</label>
                            <input type="datetime-local" name="event_end_date" class="w-full rounded-2xl border-slate-200 focus:border-purple-500 focus:ring-purple-500 transition py-3 px-5" required>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-[0.2em] mb-2">Security Code (6 Digit PIN)</label>
                        <div class="flex gap-2">
                            <input type="text" name="security_code" id="security_code" placeholder="Akan di-generate otomatis jika kosong" class="flex-1 rounded-2xl border-slate-200 focus:border-purple-500 focus:ring-purple-500 transition py-3 px-5 font-mono font-bold text-center tracking-[0.3em]" maxlength="6">
                            <button type="button" onclick="generatePIN()" class="px-4 bg-slate-100 text-slate-600 rounded-2xl hover:bg-slate-200 transition text-xs font-black uppercase">Generate</button>
                        </div>
                        <p class="text-[10px] text-slate-400 mt-2 italic px-1">Kode ini diperlukan kru untuk masuk ke mode scan. Biarkan kosong untuk generate otomatis.</p>
                    </div>

                    <script>
                        function generatePIN() {
                            const pin = Math.floor(100000 + Math.random() * 900000);
                            document.getElementById('security_code').value = pin;
                        }
                    </script>
                </div>

                <div class="flex gap-4 pt-4 border-t border-slate-50 mt-8">
                    <a href="{{ route('organizer.events.index') }}" class="flex-1 py-4 px-6 bg-white border border-slate-200 text-slate-600 rounded-2xl font-bold hover:bg-slate-50 transition text-center shadow-sm">
                        Batal
                    </a>
                    <button type="submit" class="flex-1 py-4 px-6 bg-purple-600 text-white rounded-2xl font-bold hover:bg-purple-700 transition shadow-lg shadow-purple-200 flex items-center justify-center gap-2">
                        Lanjut ke Pengaturan Tiket
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
