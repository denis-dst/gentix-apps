<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembelian Berhasil - GenTix</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;800&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f1f5f9; }
    </style>
</head>
<body class="bg-slate-50 min-h-screen flex items-center justify-center p-6">
    <div class="max-w-xl w-full bg-white rounded-[3rem] shadow-2xl shadow-blue-900/10 border border-slate-100 overflow-hidden animate-in fade-in zoom-in duration-500">
        <div class="p-12 text-center">
            <div class="w-20 h-20 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center mx-auto mb-8 animate-bounce">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" /></svg>
            </div>
            
            <h1 class="text-3xl font-black text-slate-900 mb-2 font-outfit">Terimakasih!</h1>
            <p class="text-slate-500 mb-8 font-medium">Pembayaran Anda telah terkonfirmasi. Siapkan diri Anda untuk <span class="font-bold text-blue-600">{{ $transaction->event->name }}</span>.</p>

            <div class="bg-slate-50 rounded-3xl p-6 mb-8 text-left space-y-4">
                <div class="flex justify-between items-center">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">No. Referensi</span>
                    <span class="text-sm font-black text-slate-900">{{ $transaction->reference_no }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">Jumlah</span>
                    <span class="text-sm font-bold text-slate-700">{{ $transaction->quantity }} Tiket</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">Metode</span>
                    <span class="text-sm font-bold text-slate-700 uppercase">{{ str_replace('_', ' ', $transaction->payment_method) }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">Total Bayar</span>
                    <span class="text-sm font-black text-blue-600 font-outfit">Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</span>
                </div>
            </div>

            <div class="space-y-4">
                <div class="p-4 bg-blue-50 rounded-2xl border border-blue-100 text-blue-700 text-sm">
                    <p class="font-medium">📧 E-voucher telah dikirim ke:</p>
                    <p class="font-bold">{{ $transaction->customer_email }}</p>
                </div>

                <div class="pt-4">
                    <h3 class="text-xs font-black text-slate-400 uppercase tracking-[0.2em] mb-4 text-left">Akses E-Voucher</h3>
                    <div class="grid grid-cols-1 gap-4">
                        @forelse($transaction->tickets as $ticket)
                            <a href="{{ route('tickets.view', $ticket->ticket_code) }}" target="_blank" 
                               class="flex items-center justify-between p-6 bg-white border-2 border-slate-100 rounded-[2rem] hover:border-blue-600 hover:bg-blue-50/30 transition-all group shadow-sm">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 bg-blue-600 text-white rounded-2xl flex items-center justify-center shadow-lg shadow-blue-600/20 group-hover:scale-110 transition">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                    </div>
                                    <div class="text-left">
                                        <p class="text-[10px] font-black text-slate-400 uppercase">Tiket #{{ $loop->iteration }}</p>
                                        <p class="text-sm font-black text-slate-900 font-outfit">{{ $transaction->event->name }}</p>
                                    </div>
                                </div>
                                <span class="text-blue-600 font-black text-xs uppercase tracking-widest group-hover:translate-x-1 transition">Buka &rarr;</span>
                            </a>
                        @empty
                            <div class="p-8 bg-slate-50 rounded-[2rem] border-2 border-dashed border-slate-200 text-center space-y-4">
                                <div class="w-12 h-12 bg-slate-200 rounded-full flex items-center justify-center mx-auto animate-pulse">
                                    <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                </div>
                                <div class="space-y-1">
                                    <p class="text-sm font-bold text-slate-600">Sedang Memproses Tiket...</p>
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
                    Kembali ke Beranda
                </a>
            </div>
        </div>
    </div>
</body>
</html>

