<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SeasonPass extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'user_id',
        'tenant_member_id',
        'invoice_id',
        'name',
        'season_name',
        'pass_type',
        'ticket_category_id',
        'seat_number',
        'pass_code',
        'price_paid',
        'total_matches',
        'claimed_matches',
        'claim_window_days_before',
        'status',
        'valid_until',
    ];

    protected $casts = [
        'price_paid' => 'decimal:2',
        'total_matches' => 'integer',
        'claimed_matches' => 'integer',
        'claim_window_days_before' => 'integer',
        'valid_until' => 'datetime',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function member()
    {
        return $this->belongsTo(TenantMember::class, 'tenant_member_id');
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function category()
    {
        return $this->belongsTo(TicketCategory::class, 'ticket_category_id');
    }

    public function claims()
    {
        return $this->hasMany(SeasonPassClaim::class);
    }

    public function hasClaimedForEvent(int $eventId): bool
    {
        return $this->claims()->where('event_id', $eventId)->where('status', 'claimed')->exists();
    }
}
