<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Wristbands - {{ $category->name }}</title>
    <style>
        @page {
            size: 215mm 330mm; /* F4 Size */
            margin: 0;
        }
        body {
            margin: 0;
            padding: 0;
            font-family: 'Arial', sans-serif;
            background: #f0f0f0;
        }
        .page {
            width: 215mm;
            height: 330mm;
            background: white;
            margin: 0 auto;
            page-break-after: always;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
        }
        .wristband {
            width: 215mm;
            height: 22mm; /* 330mm / 15 = 22mm */
            border-bottom: 1px dashed #ccc;
            display: flex;
            align-items: center;
            overflow: hidden;
            position: relative;
            box-sizing: border-box;
        }
        .qr-section {
            width: 30mm;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            border-right: 1px solid #eee;
            background: #fff;
        }
        .qr-section svg {
            width: 16mm;
            height: 16mm;
        }
        .qr-section .code {
            font-size: 6pt;
            font-weight: bold;
            margin-top: 1px;
        }
        .category-section {
            width: 15mm;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            text-transform: uppercase;
        }
        .category-name {
            transform: rotate(-90deg);
            white-space: nowrap;
            font-size: 9pt;
            letter-spacing: 1px;
        }
        .info-section {
            flex-grow: 1;
            padding: 0 5mm;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .event-name {
            font-size: 11pt;
            font-weight: 800;
            margin: 0;
            color: #333;
            text-transform: uppercase;
        }
        .event-details {
            font-size: 7pt;
            color: #666;
            margin-top: 1px;
        }
        .gate-section {
            width: 20mm;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            border-left: 1px dashed #eee;
        }
        .gate-label {
            font-size: 6pt;
            color: #999;
        }
        .gate-value {
            font-size: 14pt;
            font-weight: bold;
            color: #333;
        }
        .logo-section {
            width: 40mm;
            height: 100%;
            padding: 0 2mm;
            display: flex;
            align-items: center;
            justify-content: flex-end;
            opacity: 0.8;
        }
        .logo-section img {
            max-height: 12mm;
            max-width: 100%;
        }

        @media print {
            body { background: none; }
            .page { margin: 0; border: none; }
            .no-print { display: none; }
        }

        /* Preview controls */
        .controls {
            position: fixed;
            top: 20px;
            right: 20px;
            background: white;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            z-index: 1000;
        }
        .btn {
            background: #6366F1;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="controls no-print">
        <button class="btn" onclick="window.print()">Print Wristbands</button>
        <p style="font-size: 8pt; color: #666; margin-top: 10px;">
            Set printer to F4 size,<br>
            Margins: None,<br>
            Scale: 100%
        </p>
    </div>

    @foreach($tickets->chunk(15) as $chunk)
    <div class="page">
        @foreach($chunk as $ticket)
        <div class="wristband">
            <div class="qr-section">
                {!! QrCode::size(60)->generate($ticket->ticket_code) !!}
                <div class="code">{{ $ticket->ticket_code }}</div>
            </div>
            
            <div class="category-section" style="background: {{ $category->hex_color ?? '#333' }}">
                <div class="category-name">{{ $category->name }}</div>
            </div>

            <div class="info-section">
                <div class="event-name">{{ $event->name }}</div>
                <div class="event-details">
                    {{ $event->event_start_date->format('l, d M Y') }} | {{ $event->venue }}
                </div>
            </div>

            <div class="gate-section">
                <div class="gate-label">GATE</div>
                <div class="gate-value">{{ substr($category->name, 0, 1) }}</div>
            </div>

            <div class="logo-section">
                @if($event->banner_image)
                    <img src="{{ Storage::url($event->banner_image) }}" alt="Logo">
                @else
                    <span style="font-size: 8pt; color: #ccc;">GenTix</span>
                @endif
            </div>
        </div>
        @endforeach

        {{-- Fill empty slots to keep F4 layout if chunk < 15 --}}
        @for($i = count($chunk); $i < 15; $i++)
        <div class="wristband" style="border-bottom: 1px dashed #eee; opacity: 0.1;">
            <div style="padding-left: 10mm; font-size: 8pt;">Empty Wristband Slot</div>
        </div>
        @endfor
    </div>
    @endforeach

</body>
</html>
