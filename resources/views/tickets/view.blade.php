<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Voucher - {{ $ticket->ticket_code }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;800&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f8fafc; }
        .font-outfit { font-family: 'Outfit', sans-serif; }
        @media print {
            .no-print { display: none !important; }
            body { background-color: white !important; margin: 0; padding: 0; }
            .a4-container { 
                width: 210mm; 
                height: 297mm; 
                margin: 0 auto; 
                padding: 10mm;
                box-shadow: none !important;
                border: none !important;
            }
        }
        .a4-container {
            width: 210mm;
            min-height: 297mm;
            margin: 2rem auto;
            background: white;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.08);
            border-radius: 8px;
            overflow: hidden;
            position: relative;
        }
    </style>
</head>
<body class="bg-slate-100 min-h-screen py-4 md:py-10">

    @php $isFree = $ticket->transaction->payment_method === 'free'; @endphp

    @if($isFree)
        <div class="no-print max-w-[210mm] mx-auto mb-6 px-4">
            <div class="bg-gradient-to-r from-amber-500/10 to-orange-500/10 border-2 border-orange-200 rounded-3xl p-6 shadow-md flex flex-col md:flex-row items-center justify-between gap-4">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 bg-orange-500 rounded-2xl flex items-center justify-center shrink-0 shadow-lg shadow-orange-200 text-white">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                    <div>
                        <h4 class="font-black text-slate-900 font-outfit text-base">⚠️ Simpan E-Voucher Ini!</h4>
                        <p class="text-xs text-slate-600 mt-1 font-medium leading-relaxed">Pendaftaran berhasil! Silakan <strong>simpan halaman ini sebagai PDF</strong> (klik tombol simpan di kanan atau tekan <kbd class="px-1.5 py-0.5 bg-slate-200 border border-slate-300 rounded text-[10px]">Ctrl + P</kbd>) atau lakukan <strong>Screen Capture (Tangkapan Layar)</strong> pada QR Code untuk ditunjukkan kepada petugas pada saat Checkin di Hari Pelaksanaan.</p>
                    </div>
                </div>
                <div class="shrink-0 flex gap-2 w-full md:w-auto">
                    <button onclick="window.print()" class="w-full md:w-auto px-5 py-3 bg-orange-500 hover:bg-orange-600 text-black font-black text-xs rounded-xl transition shadow-lg shadow-orange-200 flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        Simpan ke PDF
                    </button>
                </div>
            </div>
        </div>
    @endif

    <div class="a4-container shadow-2xl">
        <!-- Header -->
        <div class="{{ $isFree ? 'bg-gradient-to-r from-emerald-700 to-teal-600' : 'bg-[#1e3a8a]' }} text-white p-8 flex justify-between items-center relative overflow-hidden">
            <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full -mr-32 -mt-32"></div>
            <div class="flex items-center gap-4 relative z-10">
                <span class="text-4xl font-black tracking-tighter font-outfit">GenTix</span>
                <div class="h-8 w-px bg-white/20"></div>
                <div>
                    <span class="text-2xl font-bold opacity-90 tracking-wide">E-Voucher</span>
                    @if($isFree)
                        <div class="text-xs font-black uppercase tracking-widest opacity-70 mt-0.5">Peserta Gratis</div>
                    @endif
                </div>
            </div>
            <button onclick="window.print()" class="no-print bg-white {{ $isFree ? 'text-emerald-700' : 'text-[#1e3a8a]' }} px-6 py-2.5 rounded-xl font-bold hover:bg-slate-50 transition shadow-lg active:scale-95">
                Print E-Voucher
            </button>
        </div>

        <div class="p-10 space-y-10">
            <!-- Event Section -->
            <div class="flex gap-8 items-start">
                <div class="w-1/3 shrink-0">
                    <img src="{{ $ticket->event->background_image ? (str_starts_with($ticket->event->background_image, 'http') ? $ticket->event->background_image : asset('storage/' . $ticket->event->background_image)) : 'https://images.unsplash.com/photo-1492684223066-81342ee5ff30?auto=format&fit=crop&q=80' }}" 
                         class="w-full aspect-[4/3] object-cover rounded-2xl shadow-sm border border-slate-100" alt="Banner">
                </div>
                <div class="flex-1 pt-2">
                    @if($isFree)
                        <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-emerald-100 text-emerald-700 rounded-lg text-[10px] font-black uppercase tracking-widest mb-3">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                            Event Gratis — Registrasi Terverifikasi
                        </div>
                    @endif
                    <h1 class="text-3xl font-black text-slate-900 leading-tight font-outfit mb-4">
                        {{ $ticket->event->name }}
                    </h1>
                    <div class="space-y-3 text-base font-medium text-slate-600">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 {{ $isFree ? 'text-emerald-600' : 'text-blue-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            {{ $ticket->event->event_start_date->translatedFormat('l, d F Y • H:i') }} WIB
                        </div>
                        <div class="flex items-center gap-3 uppercase tracking-wide">
                            <svg class="w-5 h-5 {{ $isFree ? 'text-emerald-600' : 'text-blue-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            {{ $ticket->event->venue }}, {{ $ticket->event->city }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-3 gap-8 items-stretch">
                <!-- Info Section -->
                <div class="col-span-2 bg-slate-50/50 border border-slate-100 rounded-[2rem] p-8 space-y-6">
                    <h3 class="text-lg font-black text-slate-900 font-outfit border-b border-slate-200 pb-3">
                        {{ $isFree ? 'Informasi Peserta' : 'Informasi Pesanan' }}
                    </h3>
                    <div class="space-y-3">
                        <div class="flex justify-between text-sm">
                            <span class="text-slate-400 font-bold uppercase tracking-wider text-[10px]">No. Invoice</span>
                            <span class="text-slate-800 font-bold uppercase">{{ $ticket->transaction->reference_no }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-slate-400 font-bold uppercase tracking-wider text-[10px]">Tanggal Registrasi</span>
                            <span class="text-slate-800 font-bold">{{ $ticket->transaction->created_at->format('d F Y H:i') }} WIB</span>
                        </div>
                        @if(!$isFree)
                        <div class="flex justify-between text-sm">
                            <span class="text-slate-400 font-bold uppercase tracking-wider text-[10px]">Metode Pembayaran</span>
                            <span class="text-slate-800 font-bold uppercase">{{ str_replace('_', ' ', $ticket->transaction->payment_method) }}</span>
                        </div>
                        @else
                        <div class="flex justify-between text-sm">
                            <span class="text-slate-400 font-bold uppercase tracking-wider text-[10px]">Jenis Tiket</span>
                            <span class="text-emerald-700 font-black uppercase">✓ GRATIS</span>
                        </div>
                        @endif

                        <div class="pt-4 border-t border-slate-200 space-y-3">
                            <div class="flex justify-between text-sm">
                                <span class="text-slate-400 font-bold uppercase tracking-wider text-[10px]">Nama</span>
                                <span class="text-slate-800 font-bold uppercase">{{ $ticket->transaction->customer_name }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-slate-400 font-bold uppercase tracking-wider text-[10px]">NIK</span>
                                <span class="text-slate-800 font-bold">{{ $ticket->transaction->customer_nik }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-slate-400 font-bold uppercase tracking-wider text-[10px]">Email</span>
                                <span class="text-slate-800 font-bold">{{ $ticket->transaction->customer_email }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-slate-400 font-bold uppercase tracking-wider text-[10px]">No. WhatsApp</span>
                                <span class="text-slate-800 font-bold">{{ $ticket->transaction->customer_phone }}</span>
                            </div>

                            @if($isFree && $ticket->transaction->customer_gender)
                            <div class="flex justify-between text-sm">
                                <span class="text-slate-400 font-bold uppercase tracking-wider text-[10px]">Gender</span>
                                <span class="font-black uppercase {{ $ticket->transaction->customer_gender === 'ikhwan' ? 'text-blue-600' : 'text-pink-600' }}">
                                    {{ $ticket->transaction->customer_gender === 'ikhwan' ? '🧔 Ikhwan' : '🧕 Akhwat' }}
                                </span>
                            </div>
                            @endif

                            @if($isFree && $ticket->transaction->customer_umroh_answer)
                            <div class="flex justify-between text-sm items-start gap-4">
                                <span class="text-slate-400 font-bold uppercase tracking-wider text-[10px] shrink-0">Umroh Bersama Batik</span>
                                <span class="text-slate-800 font-bold text-right">{{ $ticket->transaction->customer_umroh_answer }}</span>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- QR Section -->
                <div class="col-span-1 border-2 {{ $isFree ? 'border-emerald-100' : 'border-slate-100' }} rounded-[2rem] p-8 flex flex-col items-center justify-center text-center space-y-6">
                    <div>
                        <h4 class="font-black text-xl text-slate-900 font-outfit uppercase">{{ $ticket->category->name }}</h4>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">
                            {{ $isFree ? 'E-Voucher Peserta' : 'Item 1 of ' . $ticket->transaction->quantity }}
                        </p>
                    </div>

                    <div class="bg-white p-3 border {{ $isFree ? 'border-emerald-100' : 'border-slate-100' }} rounded-2xl shadow-sm">
                        {!! QrCode::size(160)->generate($ticket->ticket_code) !!}
                    </div>

                    <div class="space-y-1">
                        <span class="text-sm font-black text-slate-900 tracking-[0.2em] font-outfit block">{{ $ticket->ticket_code }}</span>
                        @if($isFree)
                            <span class="text-[9px] text-emerald-600 font-black uppercase tracking-widest block">Scan untuk Check-in</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Check-in instruction for free events -->
            @if($isFree)
            <div class="bg-amber-50 border border-amber-200 rounded-2xl p-5 flex items-start gap-4">
                <div class="w-10 h-10 bg-amber-100 rounded-xl flex items-center justify-center shrink-0 text-amber-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <p class="text-sm font-black text-amber-800">Cara Check-in di Lokasi</p>
                    <p class="text-xs text-amber-700 mt-1">Tunjukkan QR Code ini kepada petugas saat tiba di lokasi acara untuk proses check-in. Tidak ada proses penukaran (redeem) — cukup scan langsung.</p>
                </div>
            </div>
            @endif

            <!-- T&C Section -->
            <div class="pt-10 border-t-2 border-dashed border-slate-100">
                <h3 class="text-lg font-black text-slate-900 font-outfit mb-4 uppercase tracking-tight">Syarat & Ketentuan</h3>
                <div class="text-[11px] text-slate-500 leading-relaxed prose prose-sm max-w-none">
                    @php
                        $tc = $ticket->event->terms_conditions ?: ($ticket->event->tenant->terms_conditions ?? '');
                    @endphp

                    @if($tc)
                        @if(strip_tags($tc) !== $tc)
                            <div class="prose-slate">
                                {!! $tc !!}
                            </div>
                        @else
                            <div class="grid grid-cols-1 gap-1">
                                @foreach(explode("\n", $tc) as $line)
                                    @if(trim($line))
                                        <div class="flex gap-2">
                                            <span class="shrink-0">{{ $loop->iteration }}.</span>
                                            <span>{{ trim($line) }}</span>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        @endif
                    @else
                        <div class="grid grid-cols-1 gap-1">
                            <div class="flex gap-2"><span>1.</span><span>Wajib mengisi data pembelian tiket dengan benar.</span></div>
                            <div class="flex gap-2"><span>2.</span><span>E-Voucher ini hanya berlaku untuk 1 (satu) orang peserta.</span></div>
                            <div class="flex gap-2"><span>3.</span><span>Check-in dilakukan dengan scan QR Code di pintu masuk.</span></div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="absolute bottom-10 left-0 right-0 text-center no-print">
            <p class="text-xs text-slate-300 font-medium tracking-widest uppercase">Generated by GenTix Ticketing System</p>
        </div>
    </div>

</body>
</html>
