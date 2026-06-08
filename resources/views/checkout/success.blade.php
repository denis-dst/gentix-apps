<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $transaction->payment_method === 'free' ? 'Registrasi Berhasil' : 'Pembelian Berhasil' }} - GenTix</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;800&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f1f5f9; }
        @keyframes floatIn {
            from { opacity: 0; transform: translateY(20px) scale(0.95); }
            to   { opacity: 1; transform: translateY(0) scale(1); }
        }
        .animate-float-in { animation: floatIn 0.5s ease-out forwards; }
    </style>
</head>
<body class="bg-slate-50 min-h-screen flex items-center justify-center p-6">
    @php $isFree = $transaction->payment_method === 'free'; @endphp

    <div class="max-w-xl w-full bg-white rounded-[3rem] shadow-2xl shadow-blue-900/10 border border-slate-100 overflow-hidden animate-float-in">
        <!-- Top Accent Bar -->
        <div class="h-2 {{ $isFree ? 'bg-gradient-to-r from-emerald-400 to-teal-500' : 'bg-gradient-to-r from-blue-500 to-indigo-500' }}"></div>

        <div class="p-10 text-center">
            <!-- Icon -->
            <div class="w-20 h-20 {{ $isFree ? 'bg-emerald-100 text-emerald-600' : 'bg-blue-100 text-blue-600' }} rounded-full flex items-center justify-center mx-auto mb-6 animate-bounce">
                @if($isFree)
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                @else
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                @endif
            </div>
            
            <h1 class="text-3xl font-black text-slate-900 mb-2 font-outfit">
                {{ $isFree ? 'Registrasi Berhasil! 🎉' : 'Terimakasih!' }}
            </h1>
            <p class="text-slate-500 mb-8 font-medium">
                @if($isFree)
                    Pendaftaran Anda telah berhasil. E-Voucher sudah siap untuk 
                    <span class="font-bold text-emerald-600">{{ $transaction->event->name }}</span>.
                @else
                    Pembayaran Anda telah terkonfirmasi. Siapkan diri Anda untuk 
                    <span class="font-bold text-blue-600">{{ $transaction->event->name }}</span>.
                @endif
            </p>

            <!-- Info Card -->
            <div class="bg-slate-50 rounded-3xl p-6 mb-6 text-left space-y-3 border border-slate-100">
                <div class="flex justify-between items-center">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">No. Referensi</span>
                    <span class="text-sm font-black text-slate-900 font-mono">{{ $transaction->reference_no }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">Nama</span>
                    <span class="text-sm font-bold text-slate-700">{{ $transaction->customer_name }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">Jumlah</span>
                    <span class="text-sm font-bold text-slate-700">{{ $transaction->quantity }} {{ $isFree ? 'Peserta' : 'Tiket' }}</span>
                </div>
                @if($isFree)
                    <div class="flex justify-between items-center">
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">Gender</span>
                        <span class="text-sm font-bold {{ $transaction->customer_gender === 'ikhwan' ? 'text-blue-600' : 'text-pink-600' }} capitalize">
                            {{ $transaction->customer_gender === 'ikhwan' ? '🧔 Ikhwan' : '🧕 Akhwat' }}
                        </span>
                    </div>
                    <div class="pt-2 border-t border-slate-200 flex justify-between items-center">
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">Biaya</span>
                        <span class="text-sm font-black text-emerald-600">GRATIS</span>
                    </div>
                @else
                    <div class="flex justify-between items-center">
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">Metode</span>
                        <span class="text-sm font-bold text-slate-700 uppercase">{{ str_replace('_', ' ', $transaction->payment_method) }}</span>
                    </div>
                    <div class="flex justify-between items-center pt-2 border-t border-slate-200">
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">Total Bayar</span>
                        <span class="text-sm font-black text-blue-600 font-outfit">Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</span>
                    </div>
                @endif
            </div>

            @if($transaction->event->evoucher_info)
                <div class="bg-gradient-to-r from-orange-500 to-amber-500 text-white rounded-3xl p-6 mb-6 shadow-sm text-left flex items-start gap-4">
                    <div class="w-10 h-10 bg-white/20 rounded-2xl flex items-center justify-center shrink-0 text-white shadow-inner">
                        @if(Str::contains(strtolower($transaction->event->evoucher_info), ['whatsapp', 'wa.me', 'chat.whatsapp']))
                            <svg class="w-5 h-5 text-white fill-current" viewBox="0 0 24 24">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z" />
                            </svg>
                        @else
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        @endif
                    </div>
                    <div>
                        <p class="text-xs font-black uppercase tracking-wider text-white">Informasi Penting Peserta</p>
                        <p class="text-[11px] text-orange-50 font-medium leading-relaxed mt-1">
                            {!! preg_replace('/(https?:\/\/[^\s]+)/', '<a href="$1" target="_blank" class="underline text-yellow-200 hover:text-white font-black">$1</a>', nl2br(e($transaction->event->evoucher_info))) !!}
                        </p>
                    </div>
                </div>
            @endif

            <!-- E-Voucher Access -->
            <div>
                <h3 class="text-xs font-black text-slate-400 uppercase tracking-[0.2em] mb-4 text-left">
                    {{ $isFree ? '🎟️ E-Voucher Anda' : 'Akses E-Voucher' }}
                </h3>
                
                <p class="text-[11px] text-amber-600 font-bold text-left mb-4 bg-amber-50 border border-amber-200 rounded-2xl p-4 flex items-start gap-2.5">
                    <svg class="w-4 h-4 text-amber-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <span><strong>PETUNJUK PENTING:</strong> Silakan klik / buka salah satu E-Voucher di bawah ini, kemudian simpan baik-baik (lakukan <strong>Cetak / Screenshot / Simpan sebagai PDF</strong>) untuk ditunjukkan kepada petugas saat masuk ke lokasi acara.</span>
                </p>
                <div class="grid grid-cols-1 gap-4">
                    @forelse($transaction->tickets as $ticket)
                        <a href="{{ route('tickets.view', $ticket->ticket_code) }}" target="_blank" 
                           class="flex items-center justify-between p-6 bg-white border-2 {{ $isFree ? 'border-emerald-100 hover:border-emerald-500 hover:bg-emerald-50/30' : 'border-slate-100 hover:border-blue-600 hover:bg-blue-50/30' }} rounded-[2rem] transition-all group shadow-sm">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 {{ $isFree ? 'bg-emerald-600 shadow-emerald-600/20' : 'bg-blue-600 shadow-blue-600/20' }} text-white rounded-2xl flex items-center justify-center shadow-lg group-hover:scale-110 transition">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                </div>
                                <div class="text-left">
                                    <p class="text-[10px] font-black text-slate-400 uppercase">{{ $isFree ? 'E-Voucher Peserta' : 'Tiket' }} #{{ $loop->iteration }}</p>
                                    <p class="text-sm font-black text-slate-900 font-outfit">{{ $transaction->event->name }}</p>
                                    <p class="text-[10px] text-slate-400 font-mono mt-0.5">{{ $ticket->ticket_code }}</p>
                                </div>
                            </div>
                            <span class="{{ $isFree ? 'text-emerald-600' : 'text-blue-600' }} font-black text-xs uppercase tracking-widest group-hover:translate-x-1 transition">Buka &rarr;</span>
                        </a>
                    @empty
                        <div class="p-8 bg-slate-50 rounded-[2rem] border-2 border-dashed border-slate-200 text-center space-y-4">
                            <div class="w-12 h-12 bg-slate-200 rounded-full flex items-center justify-center mx-auto animate-pulse">
                                <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <div class="space-y-1">
                                <p class="text-sm font-bold text-slate-600">Sedang Memproses...</p>
                                <p class="text-[10px] text-slate-400 uppercase tracking-widest">Mohon tunggu sebentar atau muat ulang halaman</p>
                            </div>
                            <button onclick="window.location.reload()" class="px-4 py-2 bg-white border border-slate-200 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-50 transition shadow-sm">
                                Muat Ulang
                            </button>
                        </div>
                    @endforelse
                </div>
            </div>

            <a href="{{ url('/') }}" class="inline-block mt-8 py-3 px-8 bg-slate-900 text-white rounded-2xl font-bold text-sm hover:bg-slate-800 transition transform active:scale-95 shadow-xl shadow-slate-900/20">
                ← Kembali ke Beranda
            </a>
        </div>
    </div>
</body>
</html>
