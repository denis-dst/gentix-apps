<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TicketCategory extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'event_id', 'tenant_id', 'name', 'description', 'price', 
        'quota', 'sold_count', 'hex_color', 'category_image', 
        'is_active', 'sort_order'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }
}
