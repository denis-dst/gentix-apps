<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'invoice_number',
        'tenant_id',
        'issued_by',
        'title',
        'description',
        'issued_date',
        'due_date',
        'subtotal',
        'tax_percent',
        'tax_amount',
        'total_amount',
        'status',
        'payment_proof',
        'payment_proof_uploaded_at',
        'payment_confirmed_at',
        'notes',
    ];

    protected $casts = [
        'issued_date'               => 'date',
        'due_date'                  => 'date',
        'payment_proof_uploaded_at' => 'datetime',
        'payment_confirmed_at'      => 'datetime',
        'subtotal'                  => 'decimal:2',
        'tax_percent'               => 'decimal:2',
        'tax_amount'                => 'decimal:2',
        'total_amount'              => 'decimal:2',
    ];

    // ─── Relationships ────────────────────────────────────────────

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function issuer()
    {
        return $this->belongsTo(User::class, 'issued_by');
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    // ─── Accessors ────────────────────────────────────────────────

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'draft'     => 'Draft',
            'sent'      => 'Terkirim',
            'paid'      => 'Lunas',
            'cancelled' => 'Dibatalkan',
            default     => ucfirst($this->status),
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'draft'     => 'gray',
            'sent'      => 'amber',
            'paid'      => 'green',
            'cancelled' => 'red',
            default     => 'gray',
        };
    }

    public function getFormattedTotalAttribute(): string
    {
        return 'Rp ' . number_format($this->total_amount, 0, ',', '.');
    }

    public function getFormattedSubtotalAttribute(): string
    {
        return 'Rp ' . number_format($this->subtotal, 0, ',', '.');
    }

    public function getFormattedTaxAmountAttribute(): string
    {
        return 'Rp ' . number_format($this->tax_amount, 0, ',', '.');
    }

    public function getIsOverdueAttribute(): bool
    {
        return in_array($this->status, ['sent']) && $this->due_date->isPast();
    }

    // ─── Scopes ───────────────────────────────────────────────────

    public function scopePending($query)
    {
        return $query->where('status', 'sent');
    }

    // ─── Static Helpers ───────────────────────────────────────────

    public static function generateInvoiceNumber(): string
    {
        $year  = now()->format('Y');
        $month = now()->format('m');
        $last  = self::whereYear('created_at', $year)->count() + 1;
        return 'INV-' . $year . $month . '-' . str_pad($last, 4, '0', STR_PAD_LEFT);
    }
}
