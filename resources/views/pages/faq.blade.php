<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pertanyaan Umum (FAQ) - Gentix Apps</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .font-outfit { font-family: 'Outfit', sans-serif; }
        .glass {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.07);
        }
    </style>
</head>
<body class="bg-[#0b0c10] text-[#e8e4df] antialiased flex flex-col min-h-screen">

    <!-- Header / Navbar -->
    <nav class="fixed w-full z-50 transition-all duration-300 py-4 glass border-b border-white/10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-12">
                <a href="/" class="flex items-center gap-3 group">
                    <div class="w-10 h-10 bg-gradient-to-br from-orange-500 to-amber-600 rounded-xl flex items-center justify-center shadow-lg shadow-orange-500/30 group-hover:scale-105 transition-transform">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                        </svg>
                    </div>
                    <span class="text-2xl font-bold tracking-tight font-outfit uppercase text-white">Gentix<span class="text-orange-400">Apps</span></span>
                </a>
                <div class="flex items-center gap-4">
                    <a href="/" class="text-sm font-medium text-stone-300 hover:text-white transition flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                        Kembali ke Beranda
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Banner -->
    <section class="pt-36 pb-16 relative overflow-hidden bg-[#0e0f15]">
        <div class="absolute inset-0 bg-orange-500/10 blur-[120px]"></div>
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
            <span class="px-4 py-1.5 rounded-full text-xs font-semibold uppercase tracking-wider bg-orange-500/10 text-orange-400 border border-orange-500/20 inline-block mb-4">Pusat Bantuan & Layanan</span>
            <h1 class="text-4xl md:text-5xl font-black font-outfit mb-4 text-white">Pertanyaan Umum (FAQ)</h1>
            <p class="text-stone-400 max-w-xl mx-auto text-base font-light mb-8">Temukan jawaban cepat mengenai pemesanan tiket, sistem e-voucher, metode pembayaran, hingga panduan masuk gate event di Gentix Apps.</p>
        </div>
    </section>

    <!-- Content & Interactive Accordion -->
    <main class="py-12 flex-1" x-data="{ activeTab: 'all', openFaq: null, search: '' }">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Category Tabs -->
            <div class="flex flex-wrap gap-2 justify-center mb-10">
                <button @click="activeTab = 'all'" :class="activeTab === 'all' ? 'bg-orange-500 text-black font-bold shadow-lg shadow-orange-500/20' : 'glass text-stone-300 hover:bg-white/10'" class="px-5 py-2.5 rounded-xl text-sm transition-all duration-200">Semua FAQ</button>
                <button @click="activeTab = 'pemesanan'" :class="activeTab === 'pemesanan' ? 'bg-orange-500 text-black font-bold shadow-lg shadow-orange-500/20' : 'glass text-stone-300 hover:bg-white/10'" class="px-5 py-2.5 rounded-xl text-sm transition-all duration-200">Pembelian & Pembayaran</button>
                <button @click="activeTab = 'evoucher'" :class="activeTab === 'evoucher' ? 'bg-orange-500 text-black font-bold shadow-lg shadow-orange-500/20' : 'glass text-stone-300 hover:bg-white/10'" class="px-5 py-2.5 rounded-xl text-sm transition-all duration-200">E-Voucher & Check-in</button>
                <button @click="activeTab = 'refund'" :class="activeTab === 'refund' ? 'bg-orange-500 text-black font-bold shadow-lg shadow-orange-500/20' : 'glass text-stone-300 hover:bg-white/10'" class="px-5 py-2.5 rounded-xl text-sm transition-all duration-200">Refund & Pembatalan</button>
            </div>

            <!-- FAQ List -->
            <div class="space-y-4">
                
                <!-- Item 1 -->
                <div x-show="activeTab === 'all' || activeTab === 'pemesanan'" class="glass rounded-2xl overflow-hidden transition-all duration-200">
                    <button @click="openFaq = (openFaq === 1 ? null : 1)" class="w-full p-6 text-left flex justify-between items-center gap-4 hover:bg-white/[0.02]">
                        <span class="font-bold text-white text-lg">Bagaimana cara membeli tiket di Gentix Apps?</span>
                        <div class="w-8 h-8 rounded-full bg-orange-500/10 text-orange-400 flex items-center justify-center shrink-0">
                            <svg x-show="openFaq !== 1" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            <svg x-show="openFaq === 1" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
                        </div>
                    </button>
                    <div x-show="openFaq === 1" x-collapse class="px-6 pb-6 text-stone-300 text-sm leading-relaxed border-t border-white/5 pt-4">
                        Untuk membeli tiket, ikuti langkah berikut:
                        <ol class="list-decimal list-inside space-y-1.5 mt-2 text-stone-400">
                            <li>Pilih event yang ingin Anda hadiri dari daftar event di beranda Gentix Apps.</li>
                            <li>Tentukan jenis kategori tiket dan jumlah tiket yang Anda inginkan.</li>
                            <li>Isi data pemesan (Nama Lengkap, Email, dan No. Telepon/WhatsApp).</li>
                            <li>Pilih metode pembayaran (Virtual Account, QRIS, E-Wallet, atau Kartu Kredit).</li>
                            <li>Lakukan pembayaran sesuai petunjuk sebelum batas waktu berakhir.</li>
                        </ol>
                    </div>
                </div>

                <!-- Item 2 -->
                <div x-show="activeTab === 'all' || activeTab === 'pemesanan'" class="glass rounded-2xl overflow-hidden transition-all duration-200">
                    <button @click="openFaq = (openFaq === 2 ? null : 2)" class="w-full p-6 text-left flex justify-between items-center gap-4 hover:bg-white/[0.02]">
                        <span class="font-bold text-white text-lg">Metode pembayaran apa saja yang tersedia?</span>
                        <div class="w-8 h-8 rounded-full bg-orange-500/10 text-orange-400 flex items-center justify-center shrink-0">
                            <svg x-show="openFaq !== 2" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            <svg x-show="openFaq === 2" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
                        </div>
                    </button>
                    <div x-show="openFaq === 2" x-collapse class="px-6 pb-6 text-stone-300 text-sm leading-relaxed border-t border-white/5 pt-4">
                        Gentix Apps bekerjasama dengan payment gateway resmi untuk menyediakan metode pembayaran aman:
                        <ul class="list-disc list-inside space-y-1 mt-2 text-stone-400">
                            <li><strong>QRIS:</strong> Scan menggunakan DANA, GoPay, OVO, ShopeePay, LinkAja, atau Mobile Banking.</li>
                            <li><strong>Virtual Account:</strong> BCA, Mandiri, BNI, BRI, Permata Bank.</li>
                            <li><strong>Kartu Kredit / Debit:</strong> Visa dan Mastercard dengan fitur 3D Secure.</li>
                        </ul>
                    </div>
                </div>

                <!-- Item 3 -->
                <div x-show="activeTab === 'all' || activeTab === 'evoucher'" class="glass rounded-2xl overflow-hidden transition-all duration-200">
                    <button @click="openFaq = (openFaq === 3 ? null : 3)" class="w-full p-6 text-left flex justify-between items-center gap-4 hover:bg-white/[0.02]">
                        <span class="font-bold text-white text-lg">Di mana saya bisa melihat & mengunduh E-Voucher tiket?</span>
                        <div class="w-8 h-8 rounded-full bg-orange-500/10 text-orange-400 flex items-center justify-center shrink-0">
                            <svg x-show="openFaq !== 3" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            <svg x-show="openFaq === 3" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
                        </div>
                    </button>
                    <div x-show="openFaq === 3" x-collapse class="px-6 pb-6 text-stone-300 text-sm leading-relaxed border-t border-white/5 pt-4">
                        Setelah pembayaran sukses terkonfirmasi, E-Voucher akan otomatis dikirimkan ke alamat email yang Anda daftarkan. Selain itu, Anda juga dapat mengunduhnya langsung dari halaman konfirmasi transaksi usai pembayaran.
                    </div>
                </div>

                <!-- Item 4 -->
                <div x-show="activeTab === 'all' || activeTab === 'evoucher'" class="glass rounded-2xl overflow-hidden transition-all duration-200">
                    <button @click="openFaq = (openFaq === 4 ? null : 4)" class="w-full p-6 text-left flex justify-between items-center gap-4 hover:bg-white/[0.02]">
                        <span class="font-bold text-white text-lg">Apakah E-Voucher perlu dicetak?</span>
                        <div class="w-8 h-8 rounded-full bg-orange-500/10 text-orange-400 flex items-center justify-center shrink-0">
                            <svg x-show="openFaq !== 4" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            <svg x-show="openFaq === 4" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
                        </div>
                    </button>
                    <div x-show="openFaq === 4" x-collapse class="px-6 pb-6 text-stone-300 text-sm leading-relaxed border-t border-white/5 pt-4">
                        Tidak perlu. Gentix Apps mendukung sistem ramah lingkungan (paperless). Cukup tunjukkan tampilan QR Code pada smartphone Anda kepada petugas gate/loket penukaran wristband di lokasi event.
                    </div>
                </div>

                <!-- Item 5 -->
                <div x-show="activeTab === 'all' || activeTab === 'refund'" class="glass rounded-2xl overflow-hidden transition-all duration-200">
                    <button @click="openFaq = (openFaq === 5 ? null : 5)" class="w-full p-6 text-left flex justify-between items-center gap-4 hover:bg-white/[0.02]">
                        <span class="font-bold text-white text-lg">Bagaimana kebijakan pengembalian dana (Refund)?</span>
                        <div class="w-8 h-8 rounded-full bg-orange-500/10 text-orange-400 flex items-center justify-center shrink-0">
                            <svg x-show="openFaq !== 5" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            <svg x-show="openFaq === 5" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
                        </div>
                    </button>
                    <div x-show="openFaq === 5" x-collapse class="px-6 pb-6 text-stone-300 text-sm leading-relaxed border-t border-white/5 pt-4">
                        Tiket yang sudah dibeli secara umum bersifat non-refundable (tidak dapat dikembalikan). Namun jika event resmi dibatalkan oleh pihak penyelenggara (Organizer), seluruh dana akan dikembalikan sesuai alur dan ketentuan resmi di halaman <a href="{{ route('refund') }}" class="text-orange-400 underline">Refund Policy</a>.
                    </div>
                </div>

            </div>

            <!-- Still have questions card -->
            <div class="mt-12 p-8 glass rounded-3xl border border-orange-500/20 bg-gradient-to-r from-orange-500/10 to-transparent flex flex-col md:flex-row items-center justify-between gap-6">
                <div>
                    <h3 class="text-xl font-bold text-white mb-2">Masih memiliki pertanyaan?</h3>
                    <p class="text-stone-400 text-sm">Tim dukungan Gentix Apps siap membantu menjawab pertanyaan Anda melalui email atau WhatsApp.</p>
                </div>
                <a href="{{ route('contact') }}" class="px-6 py-3 bg-orange-500 hover:bg-orange-600 text-black font-bold rounded-xl transition-all shadow-lg shadow-orange-500/20 whitespace-nowrap">Hubungi Kami</a>
            </div>

        </div>
    </main>

    <!-- Footer Component -->
    <x-public-footer />

</body>
</html>
