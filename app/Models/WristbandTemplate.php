<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class WristbandTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'event_id',
        'ticket_category_id',
        'name',
        'mode',
        'background_image',
        'columns_config',
        'layout_settings',
    ];

    protected $casts = [
        'columns_config' => 'array',
        'layout_settings' => 'array',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function category()
    {
        return $this->belongsTo(TicketCategory::class, 'ticket_category_id');
    }

    public function isCustomMode(): bool
    {
        return $this->mode === 'custom';
    }

    /**
     * Standard default columns and their initial visual positions on a 215mm x 22mm wristband.
     */
    public static function getDefaultColumns(): array
    {
        return [
            'qr_code' => [
                'key' => 'qr_code',
                'label' => 'QR Code Scanner',
                'enabled' => true,
                'x' => 24.0, // mm from left
                'y' => 2.0,  // mm from top
                'qr_size' => 14.0, // mm width/height
                'color' => '#000000',
            ],
            'wristband_code' => [
                'key' => 'wristband_code',
                'label' => 'Kode Tiket / Gelang',
                'enabled' => true,
                'x' => 24.0,
                'y' => 17.0,
                'font_size' => '4pt',
                'font_weight' => '900',
                'color' => '#111827',
                'align' => 'center',
            ],
            'category_name' => [
                'key' => 'category_name',
                'label' => 'Nama Kategori Tiket',
                'enabled' => true,
                'x' => 45.0,
                'y' => 5.0,
                'font_size' => '11pt',
                'font_weight' => '900',
                'color' => '#111827',
                'align' => 'left',
            ],
            'event_name' => [
                'key' => 'event_name',
                'label' => 'Nama Event',
                'enabled' => true,
                'x' => 80.0,
                'y' => 3.0,
                'font_size' => '7.5pt',
                'font_weight' => '900',
                'color' => '#111827',
                'align' => 'left',
            ],
            'event_date' => [
                'key' => 'event_date',
                'label' => 'Tanggal & Jam Acara',
                'enabled' => true,
                'x' => 80.0,
                'y' => 11.0,
                'font_size' => '4.2pt',
                'font_weight' => '700',
                'color' => '#374151',
                'align' => 'left',
            ],
            'venue' => [
                'key' => 'venue',
                'label' => 'Lokasi / Venue Acara',
                'enabled' => true,
                'x' => 80.0,
                'y' => 15.5,
                'font_size' => '3.8pt',
                'font_weight' => '500',
                'color' => '#4b5563',
                'align' => 'left',
            ],
            'seat_number' => [
                'key' => 'seat_number',
                'label' => 'Nomor Urut / Kursi (#0001)',
                'enabled' => false,
                'x' => 150.0,
                'y' => 5.0,
                'font_size' => '8pt',
                'font_weight' => '900',
                'color' => '#111827',
                'align' => 'left',
            ],
            'price' => [
                'key' => 'price',
                'label' => 'Harga Tiket (IDR)',
                'enabled' => false,
                'x' => 150.0,
                'y' => 12.0,
                'font_size' => '5pt',
                'font_weight' => '700',
                'color' => '#111827',
                'align' => 'left',
            ],
            'custom_text' => [
                'key' => 'custom_text',
                'label' => 'Teks Kustom / Syarat Singkat',
                'enabled' => false,
                'x' => 140.0,
                'y' => 8.0,
                'font_size' => '4pt',
                'font_weight' => '700',
                'color' => '#4b5563',
                'align' => 'left',
                'custom_value' => 'NON-REFUNDABLE • WAJIB IDENTITAS RESMI',
            ],
        ];
    }

    /**
     * Get merged configuration ensuring all supported fields have complete attributes.
     */
    public function getMergedColumnsConfig(): array
    {
        $defaults = self::getDefaultColumns();
        $configured = $this->columns_config ?? [];

        foreach ($defaults as $key => $defaultData) {
            if (isset($configured[$key]) && is_array($configured[$key])) {
                $defaults[$key] = array_merge($defaultData, $configured[$key]);
            }
        }

        return $defaults;
    }

    /**
     * Resolve the background image URL.
     */
    public function getBackgroundImageUrl(): ?string
    {
        if (!$this->background_image) {
            return null;
        }

        if (str_starts_with($this->background_image, 'http://') || str_starts_with($this->background_image, 'https://') || str_starts_with($this->background_image, '/')) {
            return $this->background_image;
        }

        return Storage::url($this->background_image);
    }
}
