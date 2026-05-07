<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Wristbands - {{ $category->name }}</title>
    <style>
        @page {
            size: 215mm 330mm;
            margin: 0;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: Arial, Helvetica, sans-serif;
            background: #f3f4f6;
        }

        .page {
            width: 215mm;
            height: 330mm;
            background: #fff;
            margin: 0 auto;
            page-break-after: always;
            display: flex;
            flex-direction: column;
        }

        .wristband {
            width: 215mm;
            height: 22mm;
            border-bottom: 1px dashed #cfcfcf;
            display: flex;
            align-items: stretch;
            overflow: hidden;
            background: #fff;
        }

        .blank-space {
            width: 22mm;
            min-width: 22mm;
            background: #fff;
            border-right: 1px solid #e5e7eb;
        }

        .league-logo {
            width: 15mm;
            min-width: 15mm;
            height: 100%;
            padding: 1mm;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #0f5cc7;
            color: #fff;
            text-align: center;
            font-weight: 900;
            font-size: 4pt;
            line-height: 1.05;
            text-transform: uppercase;
            overflow: hidden;
        }

        .league-logo img,
        .club-logo img,
        .sponsor-item img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
            display: block;
        }

        .ticket-band {
            flex: 1;
            min-width: 0;
            display: grid;
            grid-template-columns: 20mm 25mm 15mm minmax(39mm, 1fr) 15mm 36mm;
            align-items: stretch;
            background:
                linear-gradient(90deg, var(--band-dark) 0%, var(--band-primary) 26%, var(--band-light) 52%, var(--band-dark) 100%),
                repeating-linear-gradient(-12deg, rgba(255,255,255,0.08), rgba(255,255,255,0.08) 1mm, transparent 1mm, transparent 6mm);
            color: var(--band-text);
            position: relative;
        }

        .ticket-band::before,
        .ticket-band::after {
            content: '';
            position: absolute;
            left: 0;
            right: 0;
            height: 1.2mm;
            background: rgba(0,0,0,0.8);
            background: var(--band-edge);
            z-index: 0;
        }

        .ticket-band::before { top: 0; }
        .ticket-band::after { bottom: 0; }

        .ticket-band > * {
            position: relative;
            z-index: 1;
            height: 100%;
            min-height: 0;
            overflow: hidden;
        }

        .qr-section {
            padding: 1mm 1.6mm;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            background: #fff;
            color: #111827;
            border-right: 1px solid rgba(255,255,255,0.2);
        }

        .qr-section svg {
            width: 10.5mm;
            height: 10.5mm;
            flex: 0 0 auto;
        }

        .qr-section .code {
            max-width: 100%;
            margin-top: 0.5mm;
            font-size: 3.4pt;
            font-weight: 900;
            line-height: 1;
            text-align: center;
            word-break: break-all;
        }

        .category-section {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0 2mm;
            color: var(--band-text);
            text-align: center;
            text-transform: uppercase;
            font-weight: 900;
            font-size: 13pt;
            line-height: 1;
            letter-spacing: 0;
            text-shadow: var(--band-text-shadow);
            overflow-wrap: anywhere;
        }

        .club-logo {
            padding: 2.5mm 1.6mm;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .club-placeholder {
            width: 12mm;
            height: 12mm;
            border: 1px solid rgba(255,255,255,0.55);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 7pt;
            font-weight: 900;
            color: var(--band-text);
        }

        .event-section {
            padding: 1.3mm 1.4mm;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            background: var(--band-panel);
            clip-path: polygon(7% 0, 93% 0, 100% 100%, 0 100%);
        }

        .league-title {
            margin-bottom: 0.5mm;
            font-size: 4.2pt;
            font-weight: 900;
            line-height: 1;
            letter-spacing: 0;
            text-transform: uppercase;
            color: var(--band-text);
            overflow-wrap: anywhere;
        }

        .event-name {
            max-width: 100%;
            font-size: 8.4pt;
            font-weight: 900;
            line-height: 0.95;
            text-transform: uppercase;
            color: var(--band-accent-text);
            overflow-wrap: anywhere;
        }

        .event-details {
            margin-top: 0.7mm;
            max-width: 100%;
            font-size: 3.8pt;
            font-weight: 800;
            line-height: 1.05;
            text-transform: uppercase;
            color: var(--band-text);
            overflow-wrap: anywhere;
        }

        .sponsor-section {
            padding: 2.2mm 2mm;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            grid-auto-rows: 3.1mm;
            gap: 0.7mm 1.2mm;
            align-content: center;
        }

        .sponsor-item {
            min-width: 0;
            min-height: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--band-text);
            font-size: 3pt;
            font-weight: 900;
            line-height: 1;
            text-align: center;
            text-transform: uppercase;
        }

        .right-blank {
            width: 28mm;
            min-width: 28mm;
            background: #fff;
            border-left: 1px solid #e5e7eb;
        }

        .empty-slot {
            height: 22mm;
            border-bottom: 1px dashed #eee;
            display: flex;
            align-items: center;
            padding-left: 10mm;
            font-size: 8pt;
            color: #cbd5e1;
        }

        @media print {
            body { background: none; }
            .page { margin: 0; border: none; }
            .no-print { display: none; }
        }

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
            background: #6366f1;
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

    @php
        $tenant = $event->tenant;
        $eventMeta = $event->meta ?? [];
        $tenantMeta = $tenant?->meta ?? [];

        $assetUrl = function ($path) {
            if (!$path) return null;
            if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://') || str_starts_with($path, '/')) {
                return $path;
            }
            return Storage::url($path);
        };

        $normalizeHex = function ($hex, $fallback = '#d71920') {
            $hex = trim((string) $hex);
            $hex = ltrim($hex, '#');

            if (strlen($hex) === 3) {
                $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
            }

            return preg_match('/^[0-9a-fA-F]{6}$/', $hex) ? '#' . strtoupper($hex) : $fallback;
        };

        $hexToRgb = function ($hex) {
            $hex = ltrim($hex, '#');
            return [
                hexdec(substr($hex, 0, 2)),
                hexdec(substr($hex, 2, 2)),
                hexdec(substr($hex, 4, 2)),
            ];
        };

        $mixHex = function ($hex, array $target, $weight) use ($hexToRgb) {
            [$r, $g, $b] = $hexToRgb($hex);
            $weight = max(0, min(1, $weight));

            return sprintf(
                '#%02X%02X%02X',
                round($r + (($target[0] - $r) * $weight)),
                round($g + (($target[1] - $g) * $weight)),
                round($b + (($target[2] - $b) * $weight))
            );
        };

        $contrastPalette = function ($hex) use ($hexToRgb, $mixHex) {
            [$r, $g, $b] = $hexToRgb($hex);
            $luminance = ((0.299 * $r) + (0.587 * $g) + (0.114 * $b)) / 255;
            $isLight = $luminance > 0.58;

            return [
                'text' => $isLight ? '#111827' : '#FFFFFF',
                'accentText' => $isLight ? '#0F172A' : '#FFFFFF',
                'shadow' => $isLight ? 'none' : '1px 1px 0 rgba(0,0,0,0.35)',
                'dark' => $mixHex($hex, [0, 0, 0], $isLight ? 0.22 : 0.55),
                'light' => $mixHex($hex, [255, 255, 255], $isLight ? 0.18 : 0.26),
                'edge' => $isLight ? 'rgba(15, 23, 42, 0.28)' : 'rgba(0, 0, 0, 0.72)',
                'panel' => $isLight ? 'rgba(255, 255, 255, 0.62)' : 'rgba(0, 0, 0, 0.68)',
            ];
        };

        $leagueName = $eventMeta['wristband_league_name'] ?? $tenantMeta['wristband_league_name'] ?? 'BRI Super League 2025-26';
        $leagueLogo = $assetUrl($eventMeta['wristband_league_logo'] ?? $tenantMeta['wristband_league_logo'] ?? \App\Models\Setting::get('wristband_league_logo'));
        $homeLogo = $assetUrl($eventMeta['wristband_home_club_logo'] ?? $tenant?->logo ?? null);
        $awayLogo = $assetUrl($eventMeta['wristband_away_club_logo'] ?? null);
        $sponsorLogos = $eventMeta['wristband_sponsor_logos'] ?? $tenantMeta['wristband_sponsor_logos'] ?? [];
        $sponsorNames = $eventMeta['wristband_sponsor_names'] ?? $tenantMeta['wristband_sponsor_names'] ?? ['GenTix', 'BRI', 'Super Soccer', 'Adidas', 'Coca Cola', 'Vidio', 'DRX', 'AFG'];
    @endphp

    @foreach($tickets->chunk(15) as $chunk)
    <div class="page">
        @foreach($chunk as $ticket)
        @php
            $ticketCategoryName = $ticket->category?->name ?? $category->name;
            $homeInitial = collect(explode(' ', $event->name))->filter()->map(fn($word) => strtoupper(substr($word, 0, 1)))->take(3)->join('');
            $bandColor = $normalizeHex($ticket->category?->hex_color ?? $category->hex_color ?? '#d71920');
            $bandPalette = $contrastPalette($bandColor);
        @endphp
        <div class="wristband">
            <div class="blank-space"></div>

            <div class="league-logo">
                @if($leagueLogo)
                    <img src="{{ $leagueLogo }}" alt="League Logo">
                @else
                    <div>SUPER<br>LEAGUE</div>
                @endif
            </div>

            <div class="ticket-band" style="--band-primary: {{ $bandColor }}; --band-dark: {{ $bandPalette['dark'] }}; --band-light: {{ $bandPalette['light'] }}; --band-edge: {{ $bandPalette['edge'] }}; --band-panel: {{ $bandPalette['panel'] }}; --band-text: {{ $bandPalette['text'] }}; --band-accent-text: {{ $bandPalette['accentText'] }}; --band-text-shadow: {{ $bandPalette['shadow'] }};">
                <div class="qr-section">
                    {!! QrCode::size(62)->margin(0)->generate($ticket->ticket_code) !!}
                    <div class="code">{{ $ticket->ticket_code }}</div>
                </div>

                <div class="category-section">{{ $ticketCategoryName }}</div>

                <div class="club-logo">
                    @if($homeLogo)
                        <img src="{{ $homeLogo }}" alt="Home Club Logo">
                    @else
                        <div class="club-placeholder">{{ $homeInitial ?: 'HOME' }}</div>
                    @endif
                </div>

                <div class="event-section">
                    <div class="league-title">{{ $leagueName }}</div>
                    <div class="event-name">{{ $event->name }}</div>
                    <div class="event-details">
                        {{ $event->event_start_date->format('l, d M Y') }}
                        @if($event->gate_open_at)
                            - KICK OFF {{ $event->gate_open_at->format('H.i') }} WIB
                        @endif
                        <br>{{ $event->venue }}{{ $event->city ? ', ' . $event->city : '' }}
                    </div>
                </div>

                <div class="club-logo">
                    @if($awayLogo)
                        <img src="{{ $awayLogo }}" alt="Away Club Logo">
                    @else
                        <div class="club-placeholder">AWAY</div>
                    @endif
                </div>

                <div class="sponsor-section">
                    @forelse($sponsorLogos as $sponsorLogo)
                        @php $sponsorLogoUrl = $assetUrl($sponsorLogo); @endphp
                        @if($sponsorLogoUrl)
                            <div class="sponsor-item"><img src="{{ $sponsorLogoUrl }}" alt="Sponsor Logo"></div>
                        @endif
                    @empty
                        @foreach($sponsorNames as $sponsorName)
                            <div class="sponsor-item">{{ $sponsorName }}</div>
                        @endforeach
                    @endforelse
                </div>
            </div>

            <div class="right-blank"></div>
        </div>
        @endforeach

        @for($i = count($chunk); $i < 15; $i++)
            <div class="empty-slot">Empty Wristband Slot</div>
        @endfor
    </div>
    @endforeach
</body>
</html>
