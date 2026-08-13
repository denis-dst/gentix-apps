<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kontak Usaha - Gentix Apps</title>
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
            <span class="px-4 py-1.5 rounded-full text-xs font-semibold uppercase tracking-wider bg-orange-500/10 text-orange-400 border border-orange-500/20 inline-block mb-4">Layanan Pelanggan & Info Usaha</span>
            <h1 class="text-4xl md:text-5xl font-black font-outfit mb-4 text-white">Hubungi Kami</h1>
            <p class="text-stone-400 max-w-xl mx-auto text-base font-light">Kami senang dapat membantu Anda. Silakan hubungi tim Gentix Apps melalui kontak resmi di bawah ini.</p>
        </div>
    </section>

    <!-- Main Content Grid -->
    <main class="py-12 flex-1">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Contact Info Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
                
                <!-- Email Card -->
                <div class="glass p-8 rounded-3xl border border-white/10 relative overflow-hidden hover:border-orange-500/30 transition-all duration-300 group">
                    <div class="w-14 h-14 rounded-2xl bg-orange-500/10 text-orange-400 flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    </div>
                    <h3 class="text-xs uppercase tracking-wider text-stone-500 font-bold mb-1">Email Support / Bisnis</h3>
                    <p class="text-xl font-bold text-white mb-2 font-outfit">virtusunity@gmail.com</p>
                    <p class="text-stone-400 text-xs mb-4">Kirimkan email untuk dukungan tiket, kemitraan event, atau pertanyaan umum.</p>
                    <a href="mailto:virtusunity@gmail.com" class="inline-flex items-center gap-2 text-xs font-bold text-orange-400 hover:text-orange-300">
                        Kirim Email &rarr;
                    </a>
                </div>

                <!-- Phone / WA Card -->
                <div class="glass p-8 rounded-3xl border border-white/10 relative overflow-hidden hover:border-orange-500/30 transition-all duration-300 group">
                    <div class="w-14 h-14 rounded-2xl bg-orange-500/10 text-orange-400 flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                    </div>
                    <h3 class="text-xs uppercase tracking-wider text-stone-500 font-bold mb-1">Telepon / WhatsApp</h3>
                    <p class="text-xl font-bold text-white mb-2 font-outfit">083878537818</p>
                    <p class="text-stone-400 text-xs mb-4">Layanan respon cepat melalui pesan WhatsApp atau panggilan telepon.</p>
                    <a href="https://wa.me/6283878537818" target="_blank" class="inline-flex items-center gap-2 text-xs font-bold text-orange-400 hover:text-orange-300">
                        Chat WhatsApp &rarr;
                    </a>
                </div>

                <!-- Address Card -->
                <div class="glass p-8 rounded-3xl border border-white/10 relative overflow-hidden hover:border-orange-500/30 transition-all duration-300 group">
                    <div class="w-14 h-14 rounded-2xl bg-orange-500/10 text-orange-400 flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                    <h3 class="text-xs uppercase tracking-wider text-stone-500 font-bold mb-1">Alamat Bisnis / Usaha</h3>
                    <p class="text-base font-bold text-white mb-2 font-outfit">Gentix Apps</p>
                    <p class="text-stone-300 text-xs leading-relaxed mb-4">DUSUN MANDAH INDUK 00/001 MANDAH, NATAR, LAMPUNG SELATAN, LAMPUNG 35362</p>
                    <span class="inline-flex items-center gap-1 text-xs text-stone-500">
                        <svg class="w-3.5 h-3.5 text-orange-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/></path></svg>
                        Lampung Selatan, Indonesia
                    </span>
                </div>

            </div>

            <!-- Form & Map Section -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-start" x-data="{ sent: false, name: '', email: '', subject: '', message: '' }">
                
                <!-- Contact Form -->
                <div class="glass p-8 md:p-10 rounded-[2.5rem]">
                    <h3 class="text-2xl font-bold text-white font-outfit mb-2">Kirim Pesan Langsung</h3>
                    <p class="text-stone-400 text-sm mb-6">Isi formulir di bawah ini dan tim Gentix Apps akan merespons pesan Anda secepatnya.</p>
                    
                    <div x-show="sent" class="p-6 bg-emerald-500/10 border border-emerald-500/20 rounded-2xl mb-6 text-emerald-400 text-sm flex items-center gap-3">
                        <svg class="w-6 h-6 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Terima kasih! Pesan Anda telah terkirim ke tim Gentix Apps. Kami akan segera menghubungi Anda melalui email.</span>
                    </div>

                    <form @submit.prevent="sent = true" x-show="!sent" class="space-y-4">
                        <div>
                            <label class="block text-xs uppercase font-bold text-stone-400 mb-2">Nama Lengkap</label>
                            <input type="text" x-model="name" required placeholder="Masukkan nama Anda" class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white placeholder-stone-600 focus:outline-none focus:border-orange-500 transition text-sm">
                        </div>

                        <div>
                            <label class="block text-xs uppercase font-bold text-stone-400 mb-2">Alamat Email</label>
                            <input type="email" x-model="email" required placeholder="contoh@domain.com" class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white placeholder-stone-600 focus:outline-none focus:border-orange-500 transition text-sm">
                        </div>

                        <div>
                            <label class="block text-xs uppercase font-bold text-stone-400 mb-2">Subjek / Perihal</label>
                            <input type="text" x-model="subject" required placeholder="misal: Pertanyaan Tiket Event" class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white placeholder-stone-600 focus:outline-none focus:border-orange-500 transition text-sm">
                        </div>

                        <div>
                            <label class="block text-xs uppercase font-bold text-stone-400 mb-2">Pesan Anda</label>
                            <textarea x-model="message" rows="4" required placeholder="Tuliskan pesan atau bantuan yang Anda butuhkan secara jelas..." class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white placeholder-stone-600 focus:outline-none focus:border-orange-500 transition text-sm"></textarea>
                        </div>

                        <button type="submit" class="w-full py-4 bg-orange-500 hover:bg-orange-600 text-black font-extrabold uppercase tracking-wider rounded-xl transition-all shadow-lg shadow-orange-500/20 text-sm">
                            Kirim Pesan Now
                        </button>
                    </form>
                </div>

                <!-- Business Profile & Map Details -->
                <div class="space-y-6">
                    <div class="glass p-8 md:p-10 rounded-[2.5rem]">
                        <h3 class="text-xl font-bold text-white font-outfit mb-4">Profil Usaha Resmi</h3>
                        <dl class="space-y-4 text-sm">
                            <div class="border-b border-white/5 pb-3">
                                <dt class="text-xs text-stone-500 uppercase font-semibold">Nama Bisnis / Perusahaan</dt>
                                <dd class="text-stone-200 font-bold text-base">Gentix Apps</dd>
                            </div>
                            <div class="border-b border-white/5 pb-3">
                                <dt class="text-xs text-stone-500 uppercase font-semibold">Alamat Lengkap</dt>
                                <dd class="text-stone-300 leading-relaxed">DUSUN MANDAH INDUK 00/001 MANDAH, NATAR, LAMPUNG SELATAN, LAMPUNG 35362</dd>
                            </div>
                            <div class="border-b border-white/5 pb-3">
                                <dt class="text-xs text-stone-500 uppercase font-semibold">Kontak Utama</dt>
                                <dd class="text-stone-300">Email: <a href="mailto:virtusunity@gmail.com" class="text-orange-400 hover:underline">virtusunity@gmail.com</a> | WA: <a href="https://wa.me/6283878537818" class="text-orange-400 hover:underline">083878537818</a></dd>
                            </div>
                            <div>
                                <dt class="text-xs text-stone-500 uppercase font-semibold">Jam Operasional Layanan Support</dt>
                                <dd class="text-stone-300">Senin &ndash; Minggu: 08.00 &ndash; 22.00 WIB</dd>
                            </div>
                        </dl>
                    </div>

                    <!-- Location Visual Card / Embed -->
                    <div class="glass p-6 rounded-[2rem] border border-white/10 bg-gradient-to-br from-white/5 to-transparent">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="w-8 h-8 rounded-lg bg-orange-500/20 text-orange-400 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                            </div>
                            <h4 class="font-bold text-white">Lokasi Usaha (Lampung Selatan)</h4>
                        </div>
                        <p class="text-xs text-stone-400 leading-relaxed mb-4">Alamat usaha tercantum dan resmi terdaftar di Mandah, Natar, Kabupaten Lampung Selatan, Provinsi Lampung 35362.</p>
                        <a href="https://maps.google.com/?q=Mandah+Natar+Lampung+Selatan" target="_blank" class="w-full py-2.5 glass border border-white/10 hover:border-orange-500/30 text-stone-200 hover:text-orange-400 text-xs font-bold rounded-xl flex items-center justify-center gap-2 transition">
                            Buka di Google Maps &rarr;
                        </a>
                    </div>
                </div>

            </div>

        </div>
    </main>

    <!-- Footer Component -->
    <x-public-footer />

</body>
</html>
