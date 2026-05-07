<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Voucher - {{ $transaction->reference_no }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        @page {
            size: 210mm 330mm; /* F4 size approximately */
            margin: 0;
        }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f1f5f9;
        }
        .font-outfit { font-family: 'Outfit', sans-serif; }
        @media print {
            body { background-color: white; }
            .no-print { display: none; }
            .print-container {
                width: 210mm;
                height: 330mm;
                padding: 15mm;
                margin: 0 auto;
                background: white;
            }
        }
        .evoucher-card {
            background: white;
            width: 210mm;
            min-height: 330mm;
            margin: 20px auto;
            padding: 15mm;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
        }
        @media print {
            .evoucher-card {
                margin: 0;
                box-shadow: none;
                width: 100%;
            }
        }
    </style>
</head>
<body class="p-4 md:p-8">
    <div class="no-print max-w-[210mm] mx-auto mb-6 flex justify-between items-center bg-white p-4 rounded-2xl shadow-sm border border-slate-200">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-orange-500 rounded-xl flex items-center justify-center text-white font-black">GT</div>
            <div>
                <h1 class="text-sm font-black text-slate-800">Cetak E-Voucher</h1>
                <p class="text-[10px] font-bold text-slate-400 uppercase">F4 Size Default</p>
            </div>
        </div>
        <div class="flex gap-2">
            <button onclick="window.print()" class="px-5 py-2 bg-orange-600 text-white rounded-xl text-xs font-black uppercase tracking-wider hover:bg-orange-700 transition flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                Print EVoucher
            </button>
        </div>
    </div>

    <div class="evoucher-card">
        <!-- Header -->
        <div class="flex justify-between items-center border-b-2 border-slate-100 pb-6 mb-8">
            <div class="flex items-center gap-4">
                @if(isset($global_settings['app_logo']) && $global_settings['app_logo'])
                    <img src="{{ asset('storage/' . $global_settings['app_logo']) }}" class="h-10 w-auto">
                @else
                    <div class="w-12 h-12 bg-orange-500 rounded-2xl flex items-center justify-center text-white text-2xl font-black shadow-lg shadow-orange-200">GT</div>
                @endif
                <div>
                    <h2 class="text-2xl font-black text-slate-800 font-outfit uppercase tracking-tighter">Gen<span class="text-orange-500">Tix</span></h2>
                    <p class="text-xs font-black text-slate-400 uppercase tracking-[0.2em]">E-Voucher</p>
                </div>
            </div>
            <div class="text-right">
                <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest">No. Invoice</div>
                <div class="text-sm font-black text-slate-700 font-mono">{{ $transaction->reference_no }}</div>
            </div>
        </div>

        <!-- Event Detail -->
        <div class="grid grid-cols-12 gap-8 mb-10">
            <div class="col-span-4">
                @if($transaction->event->banner_image)
                    <img src="{{ asset('storage/' . $transaction->event->banner_image) }}" class="w-full aspect-[4/5] object-cover rounded-[2rem] shadow-md">
                @else
                    <div class="w-full aspect-[4/5] bg-slate-100 rounded-[2rem] flex items-center justify-center text-slate-300">
                        <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                    </div>
                @endif
            </div>
            <div class="col-span-8 flex flex-col justify-center">
                <p class="text-[10px] font-black text-orange-500 uppercase tracking-[0.3em] mb-2">Event Detail</p>
                <h3 class="text-3xl font-black text-slate-800 font-outfit leading-tight mb-4">{{ $transaction->event->name }}</h3>
                <div class="space-y-3">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-slate-50 rounded-lg flex items-center justify-center text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                        </div>
                        <span class="text-sm font-bold text-slate-600">{{ $transaction->event->event_start_date->format('l, d F Y') }}</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-slate-50 rounded-lg flex items-center justify-center text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        </div>
                        <span class="text-sm font-bold text-slate-600">{{ $transaction->event->event_start_date->format('H:i') }} WIB - Selesai</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-slate-50 rounded-lg flex items-center justify-center text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                        </div>
                        <span class="text-sm font-bold text-slate-600 uppercase">{{ $transaction->event->venue }}, {{ $transaction->event->city }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Booking Info & QR -->
        @foreach($transaction->tickets as $index => $ticket)
        <div class="grid grid-cols-12 gap-8 bg-slate-50 rounded-[2.5rem] p-8 border border-slate-100 mb-6 {{ $loop->index > 0 ? 'mt-10' : '' }}">
            <div class="col-span-8">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] mb-4">Informasi Pemesanan</p>
                <div class="grid grid-cols-2 gap-y-6">
                    <div>
                        <div class="text-[9px] font-black text-slate-400 uppercase mb-1">Nama Pembeli</div>
                        <div class="text-sm font-black text-slate-800">{{ $transaction->customer_name }}</div>
                    </div>
                    <div>
                        <div class="text-[9px] font-black text-slate-400 uppercase mb-1">Kategori Tiket</div>
                        <div class="text-sm font-black text-orange-600 uppercase">{{ $ticket->category->name }}</div>
                    </div>
                    <div>
                        <div class="text-[9px] font-black text-slate-400 uppercase mb-1">NIK</div>
                        <div class="text-sm font-black text-slate-800">{{ $transaction->customer_nik ?? '-' }}</div>
                    </div>
                    <div>
                        <div class="text-[9px] font-black text-slate-400 uppercase mb-1">Email</div>
                        <div class="text-sm font-black text-slate-800">{{ $transaction->customer_email }}</div>
                    </div>
                    <div>
                        <div class="text-[9px] font-black text-slate-400 uppercase mb-1">Tanggal Transaksi</div>
                        <div class="text-sm font-black text-slate-800">{{ $transaction->paid_at ? $transaction->paid_at->format('d M Y H:i') : $transaction->created_at->format('d M Y H:i') }}</div>
                    </div>
                    <div>
                        <div class="text-[9px] font-black text-slate-400 uppercase mb-1">Metode Pembayaran</div>
                        <div class="text-sm font-black text-slate-800 uppercase">{{ $transaction->payment_method }}</div>
                    </div>
                </div>
            </div>
            <div class="col-span-4 flex flex-col items-center justify-center bg-white rounded-[2rem] p-6 shadow-sm border border-slate-100">
                <div class="text-[10px] font-black text-slate-800 uppercase mb-3">{{ $ticket->category->name }}</div>
                <div class="mb-3">
                    {!! QrCode::size(140)->generate($ticket->ticket_code) !!}
                </div>
                <div class="text-[11px] font-black text-slate-400 font-mono tracking-widest">{{ $ticket->ticket_code }}</div>
                <div class="text-[9px] font-bold text-slate-300 mt-1 uppercase">Ticket {{ $index + 1 }} of {{ $transaction->tickets->count() }}</div>
            </div>
        </div>
        @endforeach

        <!-- Syarat & Ketentuan -->
        <div class="mt-10 border-t border-slate-100 pt-8">
            <h4 class="text-sm font-black text-slate-800 uppercase tracking-widest mb-4">Syarat & Ketentuan</h4>
            <div class="text-[11px] text-slate-500 font-medium leading-relaxed prose max-w-none">
                {!! nl2br(e($transaction->event->terms_conditions)) !!}
            </div>
        </div>

        <!-- Sponsor Logo -->
        @if(isset($transaction->event->meta['sponsors']) && count($transaction->event->meta['sponsors']) > 0)
        <div class="mt-12 pt-8 border-t border-slate-100 text-center">
            <p class="text-[9px] font-black text-slate-400 uppercase tracking-[0.3em] mb-6">Supported By</p>
            <div class="flex flex-wrap justify-center items-center gap-8 opacity-60 grayscale hover:grayscale-0 transition">
                @foreach($transaction->event->meta['sponsors'] as $sponsor)
                    <img src="{{ asset('storage/' . $sponsor) }}" class="h-8 w-auto">
                @endforeach
            </div>
        </div>
        @endif

        <!-- Footer Logo -->
        <div class="mt-auto pt-12 text-center">
            <p class="text-[9px] font-bold text-slate-300 uppercase tracking-widest">Generated by GenTix Platform</p>
        </div>
    </div>
</body>
</html>
