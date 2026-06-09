<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Voucher Tidak Berlaku - {{ $ticket->ticket_code }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;800&family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f8fafc; }
        .font-outfit { font-family: 'Outfit', sans-serif; }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-6">
    <main class="max-w-lg w-full bg-white border border-rose-100 rounded-3xl shadow-xl overflow-hidden">
        <div class="bg-rose-600 text-white px-8 py-7">
            <p class="text-[10px] font-black uppercase tracking-[0.35em] text-rose-100">E-Voucher</p>
            <h1 class="text-3xl font-black font-outfit mt-2">Tidak Berlaku</h1>
        </div>

        <div class="p-8 space-y-6">
            <div>
                <p class="text-sm font-semibold text-slate-500">Tiket ini sudah dibatalkan dan tidak dapat digunakan untuk redeem/check-in.</p>
                <p class="text-xs font-bold text-slate-400 mt-3">Invoice: {{ $ticket->transaction->reference_no ?? '-' }}</p>
            </div>

            <div class="rounded-2xl border border-slate-100 bg-slate-50 p-5 space-y-3">
                <div class="flex justify-between gap-4 text-xs">
                    <span class="font-bold text-slate-400 uppercase">Kode Tiket</span>
                    <span class="font-black text-slate-700 font-mono text-right">{{ $ticket->ticket_code }}</span>
                </div>
                <div class="flex justify-between gap-4 text-xs">
                    <span class="font-bold text-slate-400 uppercase">Nama</span>
                    <span class="font-black text-slate-700 text-right">{{ $ticket->visitor_data['name'] ?? $ticket->transaction->customer_name ?? '-' }}</span>
                </div>
                <div class="flex justify-between gap-4 text-xs">
                    <span class="font-bold text-slate-400 uppercase">Kategori</span>
                    <span class="font-black text-slate-700 text-right">{{ $ticket->category->name ?? '-' }}</span>
                </div>
            </div>

            <div class="rounded-2xl bg-rose-50 border border-rose-100 px-5 py-4 text-xs font-bold text-rose-700">
                QR e-voucher disembunyikan karena status tiket sudah batal.
            </div>
        </div>
    </main>
</body>
</html>
