<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Voucher - {{ $ticket->event->name }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f3f4f6;
            margin: 0;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .ticket-card {
            background-color: white;
            border-radius: 24px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            width: 100%;
            max-width: 400px;
            overflow: hidden;
            position: relative;
        }
        .ticket-header {
            background-color: {{ $ticket->category->hex_color ?? '#6366F1' }};
            color: white;
            padding: 30px;
            text-align: center;
            position: relative;
        }
        .ticket-header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 800;
            letter-spacing: -0.5px;
        }
        .ticket-header p {
            margin: 5px 0 0;
            opacity: 0.9;
            font-size: 14px;
        }
        .ticket-body {
            padding: 30px;
            text-align: center;
        }
        .qr-container {
            background: #fff;
            padding: 20px;
            border-radius: 16px;
            display: inline-block;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        .ticket-info {
            text-align: left;
            margin-top: 20px;
            border-top: 2px dashed #e5e7eb;
            padding-top: 20px;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
        }
        .info-label {
            color: #6b7280;
            font-size: 12px;
            text-transform: uppercase;
            font-weight: 600;
        }
        .info-value {
            color: #111827;
            font-size: 14px;
            font-weight: 600;
        }
        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 9999px;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            margin-top: 10px;
        }
        .status-sold { background-color: #dcfce7; color: #166534; }
        .status-redeemed { background-color: #fee2e2; color: #991b1b; }
        
        /* Decorative punch holes */
        .punch-hole {
            position: absolute;
            width: 30px;
            height: 30px;
            background-color: #f3f4f6;
            border-radius: 50%;
            top: 50%;
            z-index: 10;
        }
        .punch-left { left: -15px; }
        .punch-right { right: -15px; }
    </style>
</head>
<body>
    <div class="ticket-card">
        <div class="ticket-header">
            <h1>{{ $ticket->category->name }}</h1>
            <p>{{ $ticket->event->name }}</p>
        </div>
        
        <div class="ticket-body">
            <div class="qr-container">
                {!! QrCode::size(200)->generate($ticket->ticket_code) !!}
            </div>
            
            <div class="ticket-code" style="font-family: monospace; font-size: 18px; font-weight: 800; color: #374151; letter-spacing: 2px;">
                {{ $ticket->ticket_code }}
            </div>

            <div class="status-badge status-{{ $ticket->status }}">
                {{ $ticket->status }}
            </div>

            <div class="ticket-info">
                <div class="info-row">
                    <div>
                        <div class="info-label">Visitor</div>
                        <div class="info-value">{{ $ticket->visitor_data['name'] ?? $ticket->transaction->customer_name }}</div>
                    </div>
                    <div style="text-align: right;">
                        <div class="info-label">Date</div>
                        <div class="info-value">{{ $ticket->event->event_start_date->format('d M Y') }}</div>
                    </div>
                </div>
                <div class="info-row">
                    <div>
                        <div class="info-label">Venue</div>
                        <div class="info-value">{{ $ticket->event->venue }}</div>
                    </div>
                    <div style="text-align: right;">
                        <div class="info-label">Time</div>
                        <div class="info-value">{{ $ticket->event->event_start_date->format('H:i') }}</div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Decorative elements -->
        <div class="punch-hole punch-left" style="top: 135px;"></div>
        <div class="punch-hole punch-right" style="top: 135px;"></div>
    </div>
</body>
</html>
