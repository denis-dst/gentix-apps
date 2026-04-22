<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GateLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id', 'event_id', 'ticket_id', 'gate_name', 
        'type', 'scanned_at', 'device_id', 'scanned_by', 'meta'
    ];

    protected $casts = [
        'scanned_at' => 'datetime',
        'meta' => 'array',
    ];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function scanner()
    {
        return $this->belongsTo(User::class, 'scanned_by');
    }
}
