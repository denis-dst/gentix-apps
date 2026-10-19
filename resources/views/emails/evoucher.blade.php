<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Penerbitan E-Voucher</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; line-height: 1.6; color: #334155; margin: 0; padding: 0; background-color: #f8fafc; }
        .container { max-width: 600px; margin: 20px auto; background: white; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); }
        .header { padding: 32px; background: #ffffff; border-bottom: 1px border #f1f5f9; }
        .content { padding: 32px; }
        .event-card { background: #f1f5f9; padding: 24px; border-radius: 12px; margin: 24px 0; }
        .event-title { font-size: 20px; font-weight: 800; color: #1e293b; margin-bottom: 8px; }
        .event-detail { font-size: 14px; color: #64748b; margin-bottom: 4px; }
        .status-table { width: 100%; border-collapse: collapse; margin: 24px 0; font-size: 14px; }
        .status-table td { padding: 12px 0; border-bottom: 1px solid #f1f5f9; }
        .status-label { color: #64748b; font-weight: 500; }
        .status-value { text-align: right; color: #1e293b; font-weight: 700; }
        .badge { padding: 4px 12px; border-radius: 9999px; font-size: 11px; font-weight: 800; text-transform: uppercase; }
        .badge-success { background: #dcfce7; color: #166534; }
        .section-title { font-size: 16px; font-weight: 800; color: #1e293b; margin-top: 32px; margin-bottom: 16px; text-transform: uppercase; letter-spacing: 0.05em; }
        .button { display: block; width: 100%; background: #ea580c; color: #ffffff !important; text-align: center; padding: 16px; border-radius: 12px; text-decoration: none; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em; margin-top: 32px; box-sizing: border-box; }
        .footer { padding: 24px; text-align: center; font-size: 12px; color: #94a3b8; }
        .price-summary { background: #fafafa; padding: 20px; border-radius: 12px; margin-top: 24px; }
        .price-row { display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 14px; }
        .price-total { border-top: 2px solid #f1f5f9; padding-top: 12px; margin-top: 12px; font-weight: 800; font-size: 18px; color: #1e293b; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2 style="margin:0; font-weight: 800; color: #1e293b;">Penerbitan E-Voucher</h2>
        </div>
        @php
            // Gunakan link yang sama dengan popup halaman sukses (tickets.view),
            // yaitu halaman E-Voucher publik untuk visitor. Ambil tiket pertama.
            $evoucherTicket = $transaction->tickets->first();
            $evoucherUrl = $evoucherTicket
                ? route('tickets.view', $evoucherTicket->ticket_code)
                : url('/');
            $ticketCount = $transaction->tickets->count();
        @endphp
        <div class="content">
            <p>Halo <strong>{{ $transaction->customer_name }}</strong>,</p>
            <p>Terima kasih sudah memesan tiket <strong>{{ $transaction->event->name }}</strong>.</p>
            @if($ticketCount > 1)
                <p>Proses pemesanan dan pembayaran kamu telah kami terima! Sebanyak <strong>{{ $ticketCount }} tiket</strong> untuk event ini sudah kami terbitkan. Kamu dapat membuka masing-masing link e-voucher pada daftar tiket di bawah, atau <a href="{{ $evoucherUrl }}" style="color: #ea580c; font-weight: bold;">klik di sini untuk melihat semua tiket</a>.</p>
            @else
                <p>Proses pemesanan dan pembayaran kamu telah kami terima! Tiket untuk event ini sudah kami terbitkan, ya. Klik tombol di bawah atau <a href="{{ $evoucherUrl }}" style="color: #ea580c; font-weight: bold;">klik di sini</a> untuk melihat E-Voucher.</p>
            @endif

            <div class="event-card">
                <div class="event-title">{{ $transaction->event->name }}</div>
                <div class="event-detail">{{ $transaction->event->event_start_date->format('l, d F Y') }} | Kick-off {{ $transaction->event->event_start_date->format('H:i') }} WIB</div>
                <div class="event-detail">{{ $transaction->event->venue }}, {{ $transaction->event->city }}</div>
            </div>

            <table class="status-table">
                <tr>
                    <td class="status-label">Status Pesanan</td>
                    <td class="status-value"><span class="badge badge-success">{{ $transaction->payment_status }}</span></td>
                </tr>
                <tr>
                    <td class="status-label">No. Invoice</td>
                    <td class="status-value" style="font-family: monospace;">{{ $transaction->reference_no }}</td>
                </tr>
                <tr>
                    <td class="status-label">Tanggal Transaksi</td>
                    <td class="status-value">{{ $transaction->paid_at ? $transaction->paid_at->format('d F Y H:i') : $transaction->created_at->format('d F Y H:i') }} WIB</td>
                </tr>
                <tr>
                    <td class="status-label">Metode Pembayaran</td>
                    <td class="status-value uppercase">{{ $transaction->payment_method }}</td>
                </tr>
            </table>

            <div class="section-title">
                Daftar Tiket &amp; Link E-Voucher @if($ticketCount > 1)({{ $ticketCount }} Tiket)@endif
            </div>

            @foreach($transaction->tickets as $index => $ticket)
                @php
                    $individualUrl = route('tickets.view', $ticket->ticket_code);
                @endphp
                <div style="background: #f8fafc; padding: 16px; border-radius: 10px; margin-bottom: 12px; border: 1px solid #e2e8f0;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <tr>
                            <td style="vertical-align: top;">
                                <div style="font-weight: 800; font-size: 13px; color: #1e293b; text-transform: uppercase;">
                                    Tiket #{{ $index + 1 }} &bull; {{ $ticket->category->name ?? 'Tiket' }}
                                </div>
                                <div style="font-size: 13px; color: #334155; font-weight: 700; margin-top: 4px;">
                                    {{ $ticket->visitor_data['name'] ?? $transaction->customer_name }}
                                </div>
                                <div style="font-size: 12px; color: #64748b; font-family: monospace; margin-top: 2px;">
                                    Kode: <strong style="color: #0f172a;">{{ $ticket->ticket_code }}</strong>
                                </div>
                            </td>
                            <td style="vertical-align: middle; text-align: right; width: 130px;">
                                <a href="{{ $individualUrl }}" style="display: inline-block; background: #0f172a; color: #ffffff !important; padding: 9px 14px; border-radius: 8px; text-decoration: none; font-size: 11px; font-weight: 800; text-align: center; text-transform: uppercase; letter-spacing: 0.03em;">
                                    Buka Tiket &rarr;
                                </a>
                            </td>
                        </tr>
                    </table>
                    <div style="margin-top: 10px; padding-top: 8px; border-top: 1px dashed #cbd5e1; font-size: 11px; color: #64748b; word-break: break-all;">
                        Link: <a href="{{ $individualUrl }}" style="color: #ea580c; text-decoration: underline;">{{ $individualUrl }}</a>
                    </div>
                </div>
            @endforeach

            <div class="section-title">Ringkasan Pemesanan</div>
            <div class="price-summary">
                <div style="display: table; width: 100%;">
                    <div style="display: table-row;">
                        <div style="display: table-cell; padding: 4px 0; font-size: 14px; color: #64748b;">Total Harga ({{ $transaction->quantity }} Tiket)</div>
                        <div style="display: table-cell; padding: 4px 0; text-align: right; font-weight: 700;">Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</div>
                    </div>
                    <div style="display: table-row;">
                        <div style="display: table-cell; padding: 4px 0; font-size: 14px; color: #64748b;">Biaya Transaksi</div>
                        <div style="display: table-cell; padding: 4px 0; text-align: right; font-weight: 700;">Rp 0</div>
                    </div>
                    <div style="display: table-row;">
                        <div style="display: table-cell; padding: 12px 0 0 0; font-size: 16px; font-weight: 800; color: #1e293b; border-top: 2px solid #f1f5f9;">Total Tagihan</div>
                        <div style="display: table-cell; padding: 12px 0 0 0; text-align: right; font-weight: 800; font-size: 20px; color: #ea580c; border-top: 2px solid #f1f5f9;">Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</div>
                    </div>
                </div>
            </div>

            @if($ticketCount > 1)
                <a href="{{ $evoucherUrl }}" class="button">Lihat Semua E-Voucher ({{ $ticketCount }} Tiket)</a>
            @else
                <a href="{{ $evoucherUrl }}" class="button">Lihat E-Voucher</a>
            @endif
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} GenTix Platform. Semua Hak Dilindungi.<br>
            Email ini dikirim otomatis, mohon tidak membalas email ini.
        </div>
    </div>
</body>
</html>
