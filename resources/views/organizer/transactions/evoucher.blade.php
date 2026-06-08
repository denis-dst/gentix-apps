<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Voucher - {{ $transaction->reference_no }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link
        href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap"
        rel="stylesheet">
    <style>
        @page {
            size: 210mm 330mm;
            /* F4 size approximately */
            margin: 0;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f1f5f9;
        }

        .font-outfit {
            font-family: 'Outfit', sans-serif;
        }

        @media print {
            body {
                background-color: white;
            }

            .no-print {
                display: none;
            }

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
            position: relative;
        }

        .terms-content ul {
            list-style-type: disc !important;
            padding-left: 1.5rem !important;
            margin-top: 0.5rem;
            margin-bottom: 0.5rem;
        }

        .terms-content ol {
            list-style-type: decimal !important;
            padding-left: 1.5rem !important;
            margin-top: 0.5rem;
            margin-bottom: 0.5rem;
        }

        .terms-content li {
            margin-bottom: 0.25rem;
        }

        .ticket-page {
            page-break-after: always;
            break-after: page;
        }

        .ticket-page:last-child {
            page-break-after: auto;
            break-after: auto;
        }

        @media print {
            body {
                background-color: white;
                padding: 0 !important;
                margin: 0 !important;
            }

            .evoucher-card {
                margin: 0;
                box-shadow: none;
                width: 100%;
                padding: 10mm;
            }

            .no-print {
                display: none !important;
            }
        }
    </style>
</head>

<body class="p-4 md:p-8">
    <div
        class="no-print max-w-[210mm] mx-auto mb-6 flex justify-between items-center bg-white p-4 rounded-2xl shadow-sm border border-slate-200">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-orange-500 rounded-xl flex items-center justify-center text-white font-black">GT
            </div>
            <div>
                <h1 class="text-sm font-black text-slate-800">Cetak E-Voucher</h1>
                <p class="text-[10px] font-bold text-slate-400 uppercase">F4 Size Default</p>
            </div>
        </div>
        <div class="flex gap-2">
            <button onclick="window.print()"
                class="px-5 py-2 bg-orange-600 text-white rounded-xl text-xs font-black uppercase tracking-wider hover:bg-orange-700 transition flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                Print EVoucher
            </button>
        </div>
    </div>

    <div class="evoucher-card">
        @foreach($transaction->tickets as $index => $ticket)
            <div class="ticket-page mb-10">
                <!-- Header (Repeated on each page for clarity) -->
                <div class="flex justify-between items-center border-b-2 border-slate-100 pb-6 mb-8">
                    <div class="flex items-center gap-4">
                        @if(isset($global_settings['app_logo']) && $global_settings['app_logo'])
                            <img src="{{ asset('storage/' . $global_settings['app_logo']) }}" class="h-10 w-auto">
                        @else
                            <div
                                class="w-12 h-12 bg-orange-500 rounded-2xl flex items-center justify-center text-white text-2xl font-black shadow-lg shadow-orange-200">
                                GT</div>
                        @endif
                        <div>
                            <h2 class="text-2xl font-black text-slate-800 font-outfit uppercase tracking-tighter">Gen<span
                                    class="text-orange-500">Tix</span></h2>
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
                    @if($transaction->event->background_image)
                        <div class="col-span-4">
                            <img src="{{ str_starts_with($transaction->event->background_image, 'http') ? $transaction->event->background_image : asset('storage/' . $transaction->event->background_image) }}"
                                class="w-full aspect-[4/5] object-cover rounded-[2rem] shadow-md">
                        </div>
                    @endif
                    <div
                        class="{{ $transaction->event->background_image ? 'col-span-8' : 'col-span-12' }} flex flex-col justify-center">
                        <p class="text-[10px] font-black text-orange-500 uppercase tracking-[0.3em] mb-2">Event Detail</p>
                        <h3 class="text-3xl font-black text-slate-800 font-outfit leading-tight mb-4">
                            {{ $transaction->event->name }}</h3>
                        <div class="space-y-3">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 bg-slate-50 rounded-lg flex items-center justify-center text-slate-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <span
                                    class="text-sm font-bold text-slate-600">{{ $transaction->event->event_start_date->format('l, d F Y') }}</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 bg-slate-50 rounded-lg flex items-center justify-center text-slate-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <span
                                    class="text-sm font-bold text-slate-600">{{ $transaction->event->event_start_date->format('H:i') }}
                                    WIB - Selesai</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 bg-slate-50 rounded-lg flex items-center justify-center text-slate-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </div>
                                <span class="text-sm font-bold text-slate-600 uppercase">{{ $transaction->event->venue }},
                                    {{ $transaction->event->city }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                @if($transaction->event->evoucher_info)
                    <div
                        class="bg-gradient-to-r from-orange-500 to-amber-500 text-white rounded-2xl p-5 mb-8 shadow-sm flex items-center gap-4">
                        <div
                            class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center shrink-0 text-white shadow-inner">
                            @if(Str::contains(strtolower($transaction->event->evoucher_info), ['whatsapp', 'wa.me', 'chat.whatsapp']))
                                <svg class="w-5 h-5 text-white fill-current" viewBox="0 0 24 24">
                                    <path
                                        d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z" />
                                </svg>
                            @else
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            @endif
                        </div>
                        <div>
                            <p class="text-xs font-black uppercase tracking-wider text-white">Pengumuman Penting / Notice</p>
                            <p class="text-[11px] text-orange-50 font-medium leading-relaxed mt-0.5">
                                {!! preg_replace('/(https?:\/\/[^\s]+)/', '<a href="$1" target="_blank" class="underline text-yellow-200 hover:text-white font-black">$1</a>', nl2br(e($transaction->event->evoucher_info))) !!}
                            </p>
                        </div>
                    </div>
                @endif

                <!-- Booking Info & QR Section -->
                <h4 class="text-lg font-bold text-slate-800 mb-4">Informasi Pesanan</h4>
                <div class="grid grid-cols-12 gap-0 border border-slate-200 rounded-lg overflow-hidden mb-8">
                    <!-- Left Side: Buyer Data -->
                    <div class="col-span-8 bg-slate-50/50 p-6 border-r border-slate-200">
                        <div class="space-y-3">
                            <div class="flex justify-between items-center text-xs">
                                <span class="text-slate-400 font-medium">No. Invoice</span>
                                <span class="text-slate-700 font-bold font-mono">{{ $transaction->reference_no }}</span>
                            </div>
                            <div class="flex justify-between items-center text-xs">
                                <span class="text-slate-400 font-medium">Tanggal Transaksi</span>
                                <span
                                    class="text-slate-700 font-bold">{{ $transaction->paid_at ? $transaction->paid_at->format('d F Y H:i') : $transaction->created_at->format('d F Y H:i') }}
                                    WIB</span>
                            </div>
                            <div class="flex justify-between items-center text-xs pb-3 border-b border-slate-200">
                                <span class="text-slate-400 font-medium">Metode Pembayaran</span>
                                <span class="text-slate-700 font-bold uppercase">{{ $transaction->payment_method }}</span>
                            </div>

                            <div class="flex justify-between items-center text-xs pt-2">
                                <span class="text-slate-400 font-medium">Nama</span>
                                <span class="text-slate-700 font-bold">{{ $transaction->customer_name }}</span>
                            </div>
                            <div class="flex justify-between items-center text-xs">
                                <span class="text-slate-400 font-medium">NIK</span>
                                <span class="text-slate-700 font-bold">{{ $transaction->customer_nik ?? '-' }}</span>
                            </div>
                            <div class="flex justify-between items-center text-xs">
                                <span class="text-slate-400 font-medium">Email</span>
                                <span class="text-slate-700 font-bold">{{ $transaction->customer_email }}</span>
                            </div>
                            <div class="flex justify-between items-center text-xs">
                                <span class="text-slate-400 font-medium">No. Telepon</span>
                                <span class="text-slate-700 font-bold">{{ $transaction->customer_phone ?? '-' }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Right Side: QR Code -->
                    <div class="col-span-4 p-6 flex flex-col items-center justify-center bg-white">
                        <div class="text-center mb-3">
                            <div class="text-xs font-black text-slate-800 uppercase leading-tight">
                                {{ $ticket->category->name }}</div>
                            <div class="text-[9px] text-slate-400 font-bold uppercase">Item {{ $index + 1 }} of
                                {{ $transaction->tickets->count() }}</div>
                        </div>

                        <div class="mb-4">
                            {!! QrCode::size(120)->generate($ticket->ticket_code) !!}
                        </div>

                        <div class="text-[10px] font-black text-slate-400 font-mono tracking-widest">
                            {{ $ticket->ticket_code }}</div>
                    </div>
                </div>

                <!-- Syarat & Ketentuan -->
                <div class="mt-10 border-t border-slate-100 pt-8">
                    <h4 class="text-sm font-black text-slate-800 uppercase tracking-widest mb-4">Syarat & Ketentuan</h4>
                    <div class="text-[11px] text-slate-500 font-medium leading-relaxed prose max-w-none terms-content">
                        {!! $transaction->event->terms_conditions !!}
                    </div>
                </div>

                <!-- Sponsor Logo -->
                @if(isset($transaction->event->meta['sponsors']) && count($transaction->event->meta['sponsors']) > 0)
                    <div class="mt-12 pt-8 border-t border-slate-100 text-center">
                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-[0.3em] mb-6">Supported By</p>
                        <div
                            class="flex flex-wrap justify-center items-center gap-8 opacity-60 grayscale hover:grayscale-0 transition">
                            @foreach($transaction->event->meta['sponsors'] as $sponsor)
                                <img src="{{ asset('storage/' . $sponsor) }}" class="h-8 w-auto">
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        @endforeach

        <!-- Footer Logo -->
        <div class="pt-8 text-center border-t border-slate-50">
            <p class="text-[9px] font-bold text-slate-300 uppercase tracking-widest">Generated by GenTix Apps Platform
            </p>
        </div>
    </div>
</body>

</html>