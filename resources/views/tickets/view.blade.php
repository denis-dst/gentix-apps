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

    <div class="a4-container shadow-2xl">
        <!-- Header -->
        <div class="bg-[#1e3a8a] text-white p-8 flex justify-between items-center relative overflow-hidden">
            <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full -mr-32 -mt-32"></div>
            <div class="flex items-center gap-4 relative z-10">
                <span class="text-4xl font-black tracking-tighter font-outfit">GenTix</span>
                <div class="h-8 w-px bg-white/20"></div>
                <span class="text-2xl font-bold opacity-90 tracking-wide">E-Voucher</span>
            </div>
            <button onclick="window.print()" class="no-print bg-white text-[#1e3a8a] px-6 py-2.5 rounded-xl font-bold hover:bg-slate-50 transition shadow-lg active:scale-95">
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
                    <h1 class="text-3xl font-black text-slate-900 leading-tight font-outfit mb-4">
                        {{ $ticket->event->name }}
                    </h1>
                    <div class="space-y-3 text-base font-medium text-slate-600">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            {{ $ticket->event->event_start_date->translatedFormat('l, d F Y • H:i') }} WIB
                        </div>
                        <div class="flex items-center gap-3 uppercase tracking-wide">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                            {{ $ticket->event->venue }}, {{ $ticket->event->city }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-3 gap-8 items-stretch">
                <!-- Info Section -->
                <div class="col-span-2 bg-slate-50/50 border border-slate-100 rounded-[2rem] p-8 space-y-6">
                    <h3 class="text-lg font-black text-slate-900 font-outfit border-b border-slate-200 pb-3">Informasi Pesanan</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between text-sm">
                            <span class="text-slate-400 font-bold uppercase tracking-wider text-[10px]">No. Invoice</span>
                            <span class="text-slate-800 font-bold uppercase">{{ $ticket->transaction->reference_no }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-slate-400 font-bold uppercase tracking-wider text-[10px]">Tanggal Transaksi</span>
                            <span class="text-slate-800 font-bold">{{ $ticket->transaction->created_at->format('d F Y H:i') }} WIB</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-slate-400 font-bold uppercase tracking-wider text-[10px]">Metode Pembayaran</span>
                            <span class="text-slate-800 font-bold uppercase">{{ str_replace('_', ' ', $ticket->transaction->payment_method) }}</span>
                        </div>
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
                                <span class="text-slate-400 font-bold uppercase tracking-wider text-[10px]">No. Telepon</span>
                                <span class="text-slate-800 font-bold">{{ $ticket->transaction->customer_phone }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- QR Section -->
                <div class="col-span-1 border-2 border-slate-100 rounded-[2rem] p-8 flex flex-col items-center justify-center text-center space-y-6">
                    <div>
                        <h4 class="font-black text-xl text-slate-900 font-outfit uppercase">{{ $ticket->category->name }}</h4>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">Item 1 of {{ $ticket->transaction->quantity }}</p>
                    </div>

                    <div class="bg-white p-3 border border-slate-100 rounded-2xl shadow-sm">
                        {!! QrCode::size(160)->generate($ticket->ticket_code) !!}
                    </div>

                    <div class="space-y-1">
                        <span class="text-sm font-black text-slate-900 tracking-[0.2em] font-outfit block">{{ $ticket->ticket_code }}</span>
                    </div>
                </div>
            </div>

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
                            <div class="flex gap-2"><span>1.</span><span>Maksimal pembelian per user/NIK hanya 2 tiket.</span></div>
                            <div class="flex gap-2"><span>2.</span><span>Wajib mengisi data pembelian tiket dengan benar.</span></div>
                            <div class="flex gap-2"><span>3.</span><span>NIK asal Provinsi Jawa Barat tidak bisa melakukan pembelian.</span></div>
                            <div class="flex gap-2"><span>4.</span><span>E-tiket ini hanya berlaku 1x penukaran.</span></div>
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
