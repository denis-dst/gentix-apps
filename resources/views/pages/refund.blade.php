<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Refund Policy (Kebijakan Pengembalian Dana) - Gentix Apps</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .font-outfit { font-family: 'Outfit', sans-serif; }
        .glass {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.07);
        }
        .policy-prose h2 { font-family: 'Outfit', sans-serif; font-size: 1.5rem; font-weight: 700; color: #ffffff; margin-top: 2rem; margin-bottom: 1rem; border-bottom: 1px solid rgba(255,255,255,0.08); padding-bottom: 0.5rem; }
        .policy-prose h3 { font-size: 1.2rem; font-weight: 600; color: #f97316; margin-top: 1.5rem; margin-bottom: 0.75rem; }
        .policy-prose p { color: #d6d3d1; font-size: 0.95rem; line-height: 1.7; margin-bottom: 1rem; }
        .policy-prose ul { list-style-type: disc; margin-left: 1.5rem; margin-bottom: 1rem; color: #a8a29e; }
        .policy-prose li { margin-bottom: 0.5rem; font-size: 0.95rem; }
        .policy-prose strong { color: #ffffff; }
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
            <span class="px-4 py-1.5 rounded-full text-xs font-semibold uppercase tracking-wider bg-orange-500/10 text-orange-400 border border-orange-500/20 inline-block mb-4">Jaminan Transaksi Aman</span>
            <h1 class="text-4xl md:text-5xl font-black font-outfit mb-4 text-white">Refund Policy</h1>
            <p class="text-stone-400 max-w-xl mx-auto text-base font-light">Panduan dan kebijakan resmi mengenai pengembalian dana (refund) untuk tiket event di Gentix Apps.</p>
        </div>
    </section>

    <!-- Policy Content -->
    <main class="py-12 flex-1">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="glass p-8 md:p-12 rounded-[2rem] policy-prose">

                <p>Gentix Apps selalu berupaya menjamin kepuasan dan kepastian bagi seluruh pengguna platform kami. Kebijakan Pengembalian Dana (Refund Policy) ini mengatur syarat dan tata cara pengembalian dana transaksi tiket.</p>

                <h2>1. Prinsip Utama Penjualan Tiket</h2>
                <p>Setiap transaksi tiket yang telah berhasil dikonfirmasi secara umum bersifat <strong>Final, Terkunci, dan Non-Refundable</strong> (tidak dapat dikembalikan atau ditukar dengan uang tunai), kecuali apabila terjadi kondisi tertentu yang secara resmi disetujui oleh Penyelenggara Event atau akibat gangguan teknis sistem pembayaran.</p>

                <h2>2. Kondisi yang Memenuhi Syarat Refund</h2>
                <ul>
                    <li><strong>Pembatalan Resmi Event oleh Organizer:</strong> Apabila event dibatalkan sepenuhnya oleh Penyelenggara tanpa ada tanggal pengganti, seluruh pemegang tiket yang sah berhak mendapatkan pengembalian dana sesuai prosedur resmi.</li>
                    <li><strong>Pembayaran Ganda (Overpayment / Double Charge):</strong> Apabila terjadi gangguan sistem pembayaran sehingga rekening/kartu Anda terpotong lebih dari satu kali untuk nomor referensi transaksi yang sama, pemotongan dana ganda tersebut akan dikembalikan 100%.</li>
                </ul>

                <h2>3. Kondisi yang TIDAK Memenuhi Syarat Refund</h2>
                <ul>
                    <li>Pembeli berhalangan hadir atau berubah pikiran setelah tiket berhasil dibeli.</li>
                    <li>Kesalahan pembeli saat memilih tanggal event, sesi acara, atau jenis kategori tiket.</li>
                    <li>Tiket telah dipindai (scanned) di lokasi gate/loket event.</li>
                    <li>E-Voucher hilang atau bocor akibat kelalaian pembeli membagikan kode QR ke pihak lain.</li>
                </ul>

                <h2>4. Prosedur dan Alur Pengajuan Refund</h2>
                <ol class="list-decimal list-inside space-y-2 text-stone-300 mb-6">
                    <li>Kirimkan email ke <a href="mailto:virtusunity@gmail.com" class="text-orange-400 underline">virtusunity@gmail.com</a> atau WhatsApp ke <a href="https://wa.me/6283878537818" class="text-orange-400 underline">083878537818</a>.</li>
                    <li>Cantumkan Subjek: <strong>[Pengajuan Refund] - Kode Transaksi / Nama Pembeli</strong>.</li>
                    <li>Sertakan Lampiran: Bukti Pembayaran, Foto KTP Pembeli, dan Nomor Rekening Bank Pengembalian.</li>
                    <li>Tim Gentix Apps akan melakukan verifikasi data transaksi dalam waktu 2 x 24 jam kerja.</li>
                    <li>Proses transfer refund yang telah disetujui akan dilaksanakan dalam kurun waktu 7–14 hari kerja tergantung metode pembayaran awal.</li>
                </ol>

                <h2>5. Layanan Bantuan Refund</h2>
                <div class="p-6 bg-white/5 border border-white/10 rounded-2xl mt-4">
                    <p class="text-white font-bold mb-1">Customer Support Refund - Gentix Apps</p>
                    <p class="text-stone-400 text-sm">Email: <a href="mailto:virtusunity@gmail.com" class="text-orange-400 hover:underline">virtusunity@gmail.com</a></p>
                    <p class="text-stone-400 text-sm">WhatsApp Support: <a href="https://wa.me/6283878537818" class="text-orange-400 hover:underline">083878537818</a></p>
                    <p class="text-stone-400 text-sm">Alamat Usaha: DUSUN MANDAH INDUK 00/001 MANDAH, NATAR, LAMPUNG SELATAN, LAMPUNG 35362</p>
                </div>

            </div>
        </div>
    </main>

    <!-- Footer Component -->
    <x-public-footer />

</body>
</html>
