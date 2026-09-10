<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SeasonPassClaim extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'season_pass_id',
        'event_id',
        'ticket_id',
        'claimed_at',
        'claim_ip',
        'status',
    ];

    protected $casts = [
        'claimed_at' => 'datetime',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function seasonPass()
    {
        return $this->belongsTo(SeasonPass::class);
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }
}
