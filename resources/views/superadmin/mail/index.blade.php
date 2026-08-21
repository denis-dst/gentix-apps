<x-app-layout>
    <x-slot name="title">Monitor Email Masuk & Keluar</x-slot>

    <div class="space-y-6">
        <!-- Top Banner / Header -->
        <div class="bg-gradient-to-r from-slate-900 via-slate-800 to-indigo-950 rounded-3xl p-8 text-white shadow-xl relative overflow-hidden">
            <div class="absolute -right-10 -bottom-10 w-64 h-64 bg-indigo-500/10 rounded-full blur-3xl pointer-events-none"></div>
            <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <span class="px-3 py-1 bg-indigo-500/20 text-indigo-300 border border-indigo-500/30 rounded-xl text-xs font-black uppercase tracking-widest">Webmail Monitor</span>
                        <span class="flex items-center gap-1.5 px-3 py-1 bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 rounded-xl text-xs font-bold">
                            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                            SSL Encrypted
                        </span>
                    </div>
                    <h1 class="text-3xl font-black font-outfit">Monitor Email Masuk & Keluar</h1>
                    <p class="text-slate-400 text-sm mt-1">Kelola, uji koneksi, baca kotak masuk (IMAP 993 / POP3 995), dan pantau email keluar (SMTP 465).</p>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <button onclick="switchTab('tester')" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-black rounded-2xl transition shadow-lg shadow-indigo-600/30 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        Tes Kirim Email
                    </button>
                    <button onclick="switchTab('inbox'); loadInbox();" class="px-5 py-2.5 bg-slate-800 hover:bg-slate-700 text-white text-xs font-black rounded-2xl border border-white/10 transition flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        Muat Kotak Masuk
                    </button>
                </div>
            </div>
        </div>

        <!-- Quick Status Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Outgoing SMTP Card -->
            <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm flex items-start gap-4">
                <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Server Keluar (Outgoing)</p>
                    <h3 class="text-base font-black text-slate-800 font-outfit mt-0.5 truncate">{{ $smtpConfig['host'] }}</h3>
                    <div class="mt-2 flex items-center gap-2 text-xs text-slate-500 font-mono">
                        <span class="px-2 py-0.5 bg-blue-50 text-blue-700 font-bold rounded-lg">Port {{ $smtpConfig['port'] }} (SSL)</span>
                        <span class="truncate">{{ $smtpConfig['username'] }}</span>
                    </div>
                </div>
            </div>

            <!-- Incoming IMAP Card -->
            <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm flex items-start gap-4">
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Server Masuk (Incoming)</p>
                    <h3 class="text-base font-black text-slate-800 font-outfit mt-0.5 truncate">{{ $incomingConfig['host'] }}</h3>
                    <div class="mt-2 flex items-center gap-2 text-xs text-slate-500 font-mono">
                        <span class="px-2 py-0.5 bg-emerald-50 text-emerald-700 font-bold rounded-lg">IMAP 993 / POP3 995</span>
                    </div>
                </div>
            </div>

            <!-- Global Setting Status Card -->
            <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm flex items-start gap-4">
                <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Status Notifikasi Global</p>
                    <h3 class="text-base font-black text-slate-800 font-outfit mt-0.5">E-Voucher Email Aktif</h3>
                    <div class="mt-2 flex items-center gap-2">
                        <span class="px-2.5 py-0.5 bg-emerald-100 text-emerald-800 text-[10px] font-black rounded-lg uppercase tracking-wider">Enabled</span>
                        <a href="{{ route('superadmin.settings.index') }}" class="text-[10px] font-bold text-orange-600 hover:underline">Ubah di Pengaturan &rarr;</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Navigation Tabs -->
        <div class="flex items-center gap-2 border-b border-slate-200 pb-2">
            <button onclick="switchTab('inbox')" id="tab-btn-inbox" class="tab-btn px-6 py-3 rounded-2xl text-xs font-black uppercase tracking-wider transition bg-orange-600 text-white shadow-lg shadow-orange-600/20">
                📥 Kotak Masuk (Inbox)
            </button>
            <button onclick="switchTab('outbox')" id="tab-btn-outbox" class="tab-btn px-6 py-3 rounded-2xl text-xs font-black uppercase tracking-wider transition bg-white text-slate-600 hover:bg-slate-100 border border-slate-200">
                📤 Kotak Keluar (Sent / E-Voucher)
            </button>
            <button onclick="switchTab('tester')" id="tab-btn-tester" class="tab-btn px-6 py-3 rounded-2xl text-xs font-black uppercase tracking-wider transition bg-white text-slate-600 hover:bg-slate-100 border border-slate-200">
                ⚡ Uji Coba & Diagnostik
            </button>
        </div>

        <!-- TAB 1: INBOX (KOTAK MASUK) -->
        <div id="tab-content-inbox" class="space-y-6">
            <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
                    <div>
                        <h2 class="text-xl font-black text-slate-800 font-outfit">Kotak Masuk (IMAP / POP3)</h2>
                        <p class="text-xs text-slate-400 font-medium">Membaca email yang masuk ke akun <strong>{{ $incomingConfig['username'] }}</strong> secara live dari server cPanel.</p>
                    </div>

                    <div class="flex items-center gap-3">
                        <div class="relative">
                            <input type="password" id="imap-pass-input" placeholder="Password email (cPanel)" class="text-xs rounded-2xl border-slate-200 pl-4 pr-10 py-2.5 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 w-56 font-mono">
                            <button type="button" onclick="togglePassVisibility('imap-pass-input')" class="absolute right-3 top-2.5 text-slate-400 hover:text-slate-600 text-xs">👁️</button>
                        </div>
                        <button onclick="loadInbox()" class="px-5 py-2.5 bg-orange-600 hover:bg-orange-700 text-white rounded-2xl text-xs font-black transition shadow-sm flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                            Refresh Inbox
                        </button>
                    </div>
                </div>

                <div id="inbox-loading" class="hidden py-12 text-center">
                    <div class="w-10 h-10 border-4 border-orange-500 border-t-transparent rounded-full animate-spin mx-auto mb-3"></div>
                    <p class="text-xs font-bold text-slate-600">Menghubungi server IMAP mail.gentix-apps.com:993...</p>
                </div>

                <div id="inbox-error" class="hidden p-6 bg-rose-50 border border-rose-200 rounded-2xl text-center space-y-2">
                    <p class="text-xs font-black text-rose-700 uppercase tracking-wider">Gagal Mengambil Email</p>
                    <p id="inbox-error-msg" class="text-xs text-rose-600 font-medium"></p>
                </div>

                <div id="inbox-container" class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-slate-100 text-[10px] font-black uppercase text-slate-400 tracking-wider">
                                <th class="pb-3 px-4">#</th>
                                <th class="pb-3 px-4">Pengirim</th>
                                <th class="pb-3 px-4">Subjek</th>
                                <th class="pb-3 px-4">Tanggal & Waktu</th>
                                <th class="pb-3 px-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="inbox-table-body" class="divide-y divide-slate-50 text-xs">
                            <tr>
                                <td colspan="5" class="py-10 text-center text-slate-400 font-medium">
                                    Silakan masukkan password email di atas lalu klik tombol <strong>Refresh Inbox</strong> untuk membaca kotak masuk.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- TAB 2: OUTBOX / RIWAYAT TRANSAKSI E-VOUCHER -->
        <div id="tab-content-outbox" class="hidden space-y-6">
            <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="text-xl font-black text-slate-800 font-outfit">Kotak Keluar & Riwayat E-Voucher</h2>
                        <p class="text-xs text-slate-400 font-medium">Daftar email E-Voucher yang telah diterbitkan dan dikirimkan otomatis ke pembeli tiket.</p>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-slate-100 text-[10px] font-black uppercase text-slate-400 tracking-wider">
                                <th class="pb-3 px-4">Invoice</th>
                                <th class="pb-3 px-4">Penerima</th>
                                <th class="pb-3 px-4">Event</th>
                                <th class="pb-3 px-4">Status Pembayaran</th>
                                <th class="pb-3 px-4">Waktu Kirim</th>
                                <th class="pb-3 px-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50 text-xs">
                            @forelse($sentTransactions as $tx)
                                <tr class="hover:bg-slate-50/60 transition">
                                    <td class="py-4 px-4 font-mono font-bold text-slate-800">{{ $tx->reference_no }}</td>
                                    <td class="py-4 px-4">
                                        <p class="font-bold text-slate-800">{{ $tx->customer_name }}</p>
                                        <p class="text-[11px] text-slate-400 font-mono">{{ $tx->customer_email }}</p>
                                    </td>
                                    <td class="py-4 px-4 font-bold text-slate-700">{{ $tx->event->name ?? '-' }}</td>
                                    <td class="py-4 px-4">
                                        <span class="px-2.5 py-1 rounded-xl text-[10px] font-black bg-emerald-100 text-emerald-700 uppercase tracking-wider">
                                            {{ strtoupper($tx->payment_status) }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-4 text-slate-500 font-mono text-[11px]">{{ $tx->paid_at ? $tx->paid_at->format('d M Y H:i') : $tx->updated_at->format('d M Y H:i') }}</td>
                                    <td class="py-4 px-4 text-right">
                                        <form action="{{ route('superadmin.transactions.resend-evoucher', $tx->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" onclick="return confirm('Kirim ulang email E-Voucher ke {{ $tx->customer_email }}?')" class="px-3 py-1.5 bg-slate-100 hover:bg-orange-50 hover:text-orange-600 text-slate-600 rounded-xl font-black text-[10px] uppercase tracking-wider transition">
                                                Kirim Ulang ✉️
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-8 text-center text-slate-400 font-medium">Belum ada riwayat email transaksi lunas.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- TAB 3: TESTER & DIAGNOSTIK -->
        <div id="tab-content-tester" class="hidden space-y-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Outgoing SMTP Live Tester -->
                <div class="bg-white rounded-3xl p-8 border border-slate-100 shadow-sm space-y-6">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold">📤</div>
                        <div>
                            <h3 class="text-lg font-black text-slate-800 font-outfit">Uji Coba Kirim Email (SMTP)</h3>
                            <p class="text-xs text-slate-400">Kirim email pengujian langsung ke alamat email tujuan.</p>
                        </div>
                    </div>

                    <form id="form-test-smtp" onsubmit="handleSendTestEmail(event)" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-xs font-black uppercase text-slate-500 tracking-wider mb-2">Email Penerima</label>
                            <input type="email" name="to_email" id="test-to-email" required placeholder="contoh@gmail.com" class="w-full text-xs rounded-2xl border-slate-200 px-4 py-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>

                        <div>
                            <label class="block text-xs font-black uppercase text-slate-500 tracking-wider mb-2">Subjek Email</label>
                            <input type="text" name="subject" value="Tes Koneksi SMTP GenTix Apps" class="w-full text-xs rounded-2xl border-slate-200 px-4 py-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>

                        <div>
                            <label class="block text-xs font-black uppercase text-slate-500 tracking-wider mb-2">Isi Pesan</label>
                            <textarea name="body" rows="3" class="w-full text-xs rounded-2xl border-slate-200 px-4 py-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">Halo, ini adalah pesan uji coba dari sistem GenTix Apps untuk memverifikasi fungsionalitas pengiriman email SMTP cPanel.</textarea>
                        </div>

                        <div id="smtp-test-result" class="hidden p-4 rounded-2xl text-xs font-bold"></div>

                        <button type="submit" id="smtp-test-btn" class="w-full py-3.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-2xl text-xs font-black uppercase tracking-wider transition shadow-lg shadow-indigo-600/20 active:scale-95 flex items-center justify-center gap-2">
                            <span>Kirim Email Pengujian</span>
                        </button>
                    </form>
                </div>

                <!-- Incoming IMAP/POP3 Connection Tester -->
                <div class="bg-white rounded-3xl p-8 border border-slate-100 shadow-sm space-y-6">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">📥</div>
                        <div>
                            <h3 class="text-lg font-black text-slate-800 font-outfit">Uji Autentikasi IMAP / POP3</h3>
                            <p class="text-xs text-slate-400">Uji koneksi socket dan kredensial login ke server mail masuk.</p>
                        </div>
                    </div>

                    <form id="form-test-incoming" onsubmit="handleTestIncoming(event)" class="space-y-4">
                        @csrf
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-black uppercase text-slate-500 tracking-wider mb-2">Protokol</label>
                                <select name="protocol" id="incoming-protocol" class="w-full text-xs rounded-2xl border-slate-200 px-4 py-3 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                                    <option value="imap">IMAP (Port 993 SSL)</option>
                                    <option value="pop3">POP3 (Port 995 SSL)</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-black uppercase text-slate-500 tracking-wider mb-2">Host Server</label>
                                <input type="text" name="host" value="mail.gentix-apps.com" class="w-full text-xs rounded-2xl border-slate-200 px-4 py-3 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 font-mono">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-black uppercase text-slate-500 tracking-wider mb-2">Username / Email</label>
                            <input type="text" name="username" value="no-reply@gentix-apps.com" class="w-full text-xs rounded-2xl border-slate-200 px-4 py-3 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 font-mono">
                        </div>

                        <div>
                            <label class="block text-xs font-black uppercase text-slate-500 tracking-wider mb-2">Password Akun Email</label>
                            <input type="password" name="password" id="incoming-test-pass" required placeholder="Masukkan password email akun cPanel" class="w-full text-xs rounded-2xl border-slate-200 px-4 py-3 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 font-mono">
                        </div>

                        <div id="incoming-test-result" class="hidden p-4 rounded-2xl text-xs font-bold"></div>

                        <button type="submit" id="incoming-test-btn" class="w-full py-3.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-2xl text-xs font-black uppercase tracking-wider transition shadow-lg shadow-emerald-600/20 active:scale-95 flex items-center justify-center gap-2">
                            <span>Uji Koneksi & Autentikasi</span>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Configuration Reference Box -->
            <div class="bg-slate-900 rounded-3xl p-8 text-white space-y-4">
                <div class="flex items-center gap-3">
                    <span class="text-xl">📋</span>
                    <h3 class="text-base font-black font-outfit uppercase tracking-wider text-slate-200">Referensi Konfigurasi Email Server</h3>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-xs text-slate-300">
                    <div class="bg-slate-800/80 rounded-2xl p-5 space-y-2 border border-white/5 font-mono">
                        <p class="text-orange-400 font-bold uppercase tracking-widest text-[10px]">Server Masuk (Incoming)</p>
                        <p><span class="text-slate-400">Server:</span> mail.gentix-apps.com</p>
                        <p><span class="text-slate-400">IMAP Port:</span> 993 (SSL)</p>
                        <p><span class="text-slate-400">POP3 Port:</span> 995 (SSL)</p>
                        <p><span class="text-slate-400">Username:</span> no-reply@gentix-apps.com</p>
                    </div>
                    <div class="bg-slate-800/80 rounded-2xl p-5 space-y-2 border border-white/5 font-mono">
                        <p class="text-cyan-400 font-bold uppercase tracking-widest text-[10px]">Server Keluar (Outgoing)</p>
                        <p><span class="text-slate-400">Server:</span> mail.gentix-apps.com</p>
                        <p><span class="text-slate-400">SMTP Port:</span> 465 (SSL / SMTPS)</p>
                        <p><span class="text-slate-400">Alternatif Port:</span> 587 (TLS / STARTTLS)</p>
                        <p><span class="text-slate-400">Username:</span> no-reply@gentix-apps.com</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal View Email Body -->
    <div id="emailDetailModal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm">
        <div class="bg-white rounded-[2.5rem] shadow-2xl max-w-2xl w-full max-h-[85vh] flex flex-col overflow-hidden animate-float-in">
            <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <span class="text-[10px] font-black uppercase tracking-wider text-orange-600">Detail Pesan Masuk</span>
                    <h3 id="modal-email-subject" class="text-lg font-black text-slate-800 truncate">Memuat...</h3>
                </div>
                <button onclick="closeEmailModal()" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center font-bold">&times;</button>
            </div>
            <div class="p-6 overflow-y-auto space-y-4 flex-1 text-xs">
                <div class="bg-slate-50 p-4 rounded-2xl space-y-1 font-mono text-slate-600">
                    <p><strong>Dari:</strong> <span id="modal-email-from">-</span></p>
                    <p><strong>Waktu:</strong> <span id="modal-email-date">-</span></p>
                </div>
                <div class="space-y-2">
                    <label class="font-black uppercase tracking-wider text-slate-400 text-[10px]">Isi Pesan:</label>
                    <div id="modal-email-body" class="p-5 rounded-2xl border border-slate-100 bg-white font-mono whitespace-pre-wrap leading-relaxed max-h-80 overflow-y-auto">
                        Memuat isi pesan...
                    </div>
                </div>
            </div>
            <div class="p-4 border-t border-slate-100 bg-slate-50 flex justify-end">
                <button onclick="closeEmailModal()" class="px-6 py-2.5 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-bold transition">Tutup</button>
            </div>
        </div>
    </div>

    <script>
        function switchTab(tab) {
            ['inbox', 'outbox', 'tester'].forEach(t => {
                const content = document.getElementById(`tab-content-${t}`);
                const btn = document.getElementById(`tab-btn-${t}`);
                if (t === tab) {
                    content.classList.remove('hidden');
                    btn.className = "tab-btn px-6 py-3 rounded-2xl text-xs font-black uppercase tracking-wider transition bg-orange-600 text-white shadow-lg shadow-orange-600/20";
                } else {
                    content.classList.add('hidden');
                    btn.className = "tab-btn px-6 py-3 rounded-2xl text-xs font-black uppercase tracking-wider transition bg-white text-slate-600 hover:bg-slate-100 border border-slate-200";
                }
            });
        }

        function togglePassVisibility(id) {
            const input = document.getElementById(id);
            input.type = input.type === 'password' ? 'text' : 'password';
        }

        function loadInbox() {
            const pass = document.getElementById('imap-pass-input').value;
            const loading = document.getElementById('inbox-loading');
            const errorDiv = document.getElementById('inbox-error');
            const errorMsg = document.getElementById('inbox-error-msg');
            const tableBody = document.getElementById('inbox-table-body');

            loading.classList.remove('hidden');
            errorDiv.classList.add('hidden');
            tableBody.innerHTML = '';

            fetch("{{ route('superadmin.mail.inbox') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ password: pass })
            })
            .then(res => res.json())
            .then(data => {
                loading.classList.add('hidden');
                if (!data.success) {
                    errorDiv.classList.remove('hidden');
                    errorMsg.innerText = data.message || 'Gagal memuat kotak masuk.';
                    tableBody.innerHTML = `<tr><td colspan="5" class="py-8 text-center text-rose-500 font-bold">${data.message}</td></tr>`;
                    return;
                }

                if (!data.messages || data.messages.length === 0) {
                    tableBody.innerHTML = `<tr><td colspan="5" class="py-8 text-center text-slate-400 font-medium">Kotak masuk kosong (0 pesan).</td></tr>`;
                    return;
                }

                let html = '';
                data.messages.forEach((msg, idx) => {
                    html += `
                        <tr class="hover:bg-slate-50 transition">
                            <td class="py-4 px-4 font-mono text-slate-400 font-bold">${msg.id}</td>
                            <td class="py-4 px-4 font-bold text-slate-800">${escapeHtml(msg.from)}</td>
                            <td class="py-4 px-4">
                                <span class="font-bold text-slate-900">${escapeHtml(msg.subject)}</span>
                            </td>
                            <td class="py-4 px-4 font-mono text-[11px] text-slate-500">${escapeHtml(msg.date)}</td>
                            <td class="py-4 px-4 text-right">
                                <button onclick="viewEmailDetail(${msg.id}, '${escapeHtml(msg.subject)}', '${escapeHtml(msg.from)}', '${escapeHtml(msg.date)}')" class="px-3 py-1.5 bg-orange-50 hover:bg-orange-600 hover:text-white text-orange-600 rounded-xl text-[10px] font-black uppercase tracking-wider transition">
                                    Buka Pesan 📖
                                </button>
                            </td>
                        </tr>
                    `;
                });
                tableBody.innerHTML = html;
            })
            .catch(err => {
                loading.classList.add('hidden');
                errorDiv.classList.remove('hidden');
                errorMsg.innerText = err.message || 'Gagal terhubung ke endpoint server.';
            });
        }

        function viewEmailDetail(id, subject, from, date) {
            const pass = document.getElementById('imap-pass-input').value;
            const modal = document.getElementById('emailDetailModal');
            document.getElementById('modal-email-subject').innerText = subject;
            document.getElementById('modal-email-from').innerText = from;
            document.getElementById('modal-email-date').innerText = date;
            document.getElementById('modal-email-body').innerText = 'Mengambil isi pesan dari server IMAP...';
            modal.classList.remove('hidden');

            fetch(`{{ url('/superadmin/mail/message') }}/${id}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ password: pass })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('modal-email-body').innerText = data.body || data.raw || '(Pesan kosong)';
                } else {
                    document.getElementById('modal-email-body').innerText = 'Gagal memuat pesan: ' + data.message;
                }
            })
            .catch(err => {
                document.getElementById('modal-email-body').innerText = 'Error: ' + err.message;
            });
        }

        function closeEmailModal() {
            document.getElementById('emailDetailModal').classList.add('hidden');
        }

        function handleSendTestEmail(e) {
            e.preventDefault();
            const btn = document.getElementById('smtp-test-btn');
            const resDiv = document.getElementById('smtp-test-result');
            const form = e.target;
            const formData = new FormData(form);

            btn.disabled = true;
            btn.innerHTML = '<span class="animate-spin">⏳</span> Mengirim...';
            resDiv.className = "hidden";

            fetch("{{ route('superadmin.mail.test-smtp') }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                btn.disabled = false;
                btn.innerHTML = 'Kirim Email Pengujian';
                resDiv.classList.remove('hidden');
                if (data.success) {
                    resDiv.className = "p-4 rounded-2xl text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200";
                    resDiv.innerText = "✅ " + data.message;
                } else {
                    resDiv.className = "p-4 rounded-2xl text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200";
                    resDiv.innerText = "❌ " + data.message;
                }
            })
            .catch(err => {
                btn.disabled = false;
                btn.innerHTML = 'Kirim Email Pengujian';
                resDiv.classList.remove('hidden');
                resDiv.className = "p-4 rounded-2xl text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200";
                resDiv.innerText = "❌ Gagal: " + err.message;
            });
        }

        function handleTestIncoming(e) {
            e.preventDefault();
            const btn = document.getElementById('incoming-test-btn');
            const resDiv = document.getElementById('incoming-test-result');
            const form = e.target;
            const formData = new FormData(form);

            btn.disabled = true;
            btn.innerHTML = '<span class="animate-spin">⏳</span> Menguji...';
            resDiv.className = "hidden";

            fetch("{{ route('superadmin.mail.test-incoming') }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                btn.disabled = false;
                btn.innerHTML = 'Uji Koneksi & Autentikasi';
                resDiv.classList.remove('hidden');
                if (data.success) {
                    resDiv.className = "p-4 rounded-2xl text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200";
                    resDiv.innerText = "✅ " + data.message + (data.server_banner ? " (" + data.server_banner + ")" : "");
                } else {
                    resDiv.className = "p-4 rounded-2xl text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200";
                    resDiv.innerText = "❌ " + data.message;
                }
            })
            .catch(err => {
                btn.disabled = false;
                btn.innerHTML = 'Uji Koneksi & Autentikasi';
                resDiv.classList.remove('hidden');
                resDiv.className = "p-4 rounded-2xl text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200";
                resDiv.innerText = "❌ Gagal: " + err.message;
            });
        }

        function escapeHtml(text) {
            if (!text) return '';
            const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
            return text.replace(/[&<>"']/g, function(m) { return map[m]; });
        }
    </script>
</x-app-layout>
