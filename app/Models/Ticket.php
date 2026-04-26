<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ticket extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tenant_id', 'event_id', 'transaction_id', 'ticket_category_id', 
        'ticket_code', 'wristband_qr', 'status', 'redeemed_at', 
        'redeemed_by', 'visitor_data', 'redeem_photo'
    ];

    protected $casts = [
        'redeemed_at' => 'datetime',
        'visitor_data' => 'array',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function category()
    {
        return $this->belongsTo(TicketCategory::class, 'ticket_category_id');
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public function redeemer()
    {
        return $this->belongsTo(User::class, 'redeemed_by');
    }

    public function gateLogs()
    {
        return $this->hasMany(GateLog::class);
    }
}
