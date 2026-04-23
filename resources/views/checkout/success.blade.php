<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchase Successful - GenTix</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;800&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f8fafc; }
    </style>
</head>
<body class="bg-slate-50 min-h-screen flex items-center justify-center p-6">
    <div class="max-w-xl w-full bg-white rounded-[3rem] shadow-2xl shadow-purple-900/10 border border-slate-100 overflow-hidden animate-in fade-in zoom-in duration-500">
        <div class="p-12 text-center">
            <div class="w-20 h-20 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mx-auto mb-8">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" /></svg>
            </div>
            
            <h1 class="text-3xl font-black text-slate-900 mb-2 font-outfit">Payment Successful!</h1>
            <p class="text-slate-500 mb-8">Your tickets for <span class="font-bold text-slate-700">{{ $transaction->event->name }}</span> are ready.</p>

            <div class="bg-slate-50 rounded-3xl p-6 mb-8 text-left space-y-4">
                <div class="flex justify-between items-center">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">Reference No</span>
                    <span class="text-sm font-black text-slate-900">{{ $transaction->reference_no }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">Quantity</span>
                    <span class="text-sm font-bold text-slate-700">{{ $transaction->tickets->count() }} Tickets</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">Total Paid</span>
                    <span class="text-sm font-black text-purple-600">Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</span>
                </div>
            </div>

            <div class="space-y-4">
                <div class="p-4 bg-purple-50 rounded-2xl border border-purple-100 text-purple-700 text-sm">
                    <p class="font-medium">📧 E-vouchers have been sent to:</p>
                    <p class="font-bold">{{ $transaction->customer_email }}</p>
                </div>

                <div class="grid grid-cols-1 gap-3">
                    @foreach($transaction->tickets as $ticket)
                        <a href="{{ route('tickets.view', $ticket->ticket_code) }}" target="_blank" class="block p-4 bg-white border border-slate-200 rounded-2xl hover:border-purple-600 transition group">
                            <div class="flex justify-between items-center">
                                <span class="text-xs font-bold text-slate-400 group-hover:text-purple-600">Ticket #{{ $loop->iteration }}</span>
                                <span class="text-sm font-black text-slate-900 group-hover:text-purple-600">View E-Voucher &rarr;</span>
                            </div>
                        </a>
                    @endforeach
                </div>

                <a href="{{ url('/') }}" class="inline-block mt-8 text-slate-400 hover:text-slate-600 text-sm font-bold transition">
                    &larr; Back to Homepage
                </a>
            </div>
        </div>
    </div>
</body>
</html>
