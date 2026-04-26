<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tenant_id', 'name', 'slug', 'description', 'terms_conditions', 'venue', 'city', 
        'background_image', 'banner_image', 'event_start_date', 
        'event_end_date', 'gate_open_at', 'gate_close_at', 'status', 'meta', 'security_code'
    ];

    protected $casts = [
        'event_start_date' => 'datetime',
        'event_end_date' => 'datetime',
        'gate_open_at' => 'datetime',
        'gate_close_at' => 'datetime',
        'meta' => 'array',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function ticketCategories()
    {
        return $this->hasMany(TicketCategory::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    // Current occupancy calculation
    public function getCurrentOccupancyAttribute()
    {
        $in = GateLog::where('event_id', $this->id)->where('type', 'IN')->count();
        $out = GateLog::where('event_id', $this->id)->where('type', 'OUT')->count();
        return $in - $out;
    }
}
