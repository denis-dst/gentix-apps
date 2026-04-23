@component('mail::message')
# E-Voucher: {{ $event->name }}

Hi {{ $ticket->visitor_data['name'] ?? 'Customer' }},

Thank you for your purchase! Below is your E-Voucher. Please keep this QR code secure. You will need to show this QR code at the redemption counter to receive your wristband.

<div style="background-color: {{ $category->hex_color ?? '#6366F1' }}; padding: 20px; border-radius: 12px; color: white; font-family: 'Inter', sans-serif;">
    <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid rgba(255,255,255,0.2); padding-bottom: 15px; margin-bottom: 15px;">
        <div>
            <h2 style="margin: 0; font-size: 24px; font-weight: 800;">{{ $category->name }}</h2>
            <p style="margin: 0; opacity: 0.8; font-size: 14px;">{{ $event->name }}</p>
        </div>
        <div style="text-align: right;">
            <p style="margin: 0; font-size: 12px; opacity: 0.8;">Ticket Code</p>
            <p style="margin: 0; font-weight: 700; font-family: monospace; letter-spacing: 1px;">{{ $ticket->ticket_code }}</p>
        </div>
    </div>

    <div style="text-align: center; background: white; padding: 20px; border-radius: 8px; margin: 20px 0;">
        <img src="data:image/png;base64, {!! base64_encode(QrCode::format('png')->size(200)->generate($ticket->ticket_code)) !!} " alt="QR Code" style="max-width: 200px;">
        <p style="color: #333; margin-top: 10px; font-size: 12px;">Scan this at Redemption Counter</p>
    </div>

    <div style="font-size: 14px; line-height: 1.6;">
        <div style="margin-bottom: 8px;">
            <strong>Venue:</strong> {{ $event->venue }}
        </div>
        <div style="margin-bottom: 8px;">
            <strong>Date:</strong> {{ $event->event_start_date->format('d M Y') }}
        </div>
        <div>
            <strong>Time:</strong> {{ $event->event_start_date->format('H:i') }}
        </div>
    </div>
</div>

@component('mail::button', ['url' => config('app.url') . '/tickets/' . $ticket->ticket_code])
Download PDF Voucher
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
