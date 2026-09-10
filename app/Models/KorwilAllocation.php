<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KorwilAllocation extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'korwil_id',
        'event_id',
        'ticket_category_id',
        'allocated_quota',
        'used_quota',
        'status',
    ];

    protected $casts = [
        'allocated_quota' => 'integer',
        'used_quota' => 'integer',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function korwil()
    {
        return $this->belongsTo(Korwil::class);
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function category()
    {
        return $this->belongsTo(TicketCategory::class, 'ticket_category_id');
    }

    public function memberConsents()
    {
        return $this->hasMany(KorwilMemberConsent::class);
    }
}
