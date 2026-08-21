<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ranger extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'whatsapp',
        'bank_name',
        'account_number',
        'gender',
        'is_offday',
        'is_spv',
        'assigned_gate',
        'assigned_at',
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
        'is_offday' => 'boolean',
        'is_spv' => 'boolean',
    ];

    public function scopeMale($query)
    {
        return $query->where('gender', 'male');
    }

    public function scopeFemale($query)
    {
        return $query->where('gender', 'female');
    }

    public function scopeAvailableForPlotting($query)
    {
        return $query->where('is_offday', false)->where('is_spv', false);
    }

    public function scopeSpv($query)
    {
        return $query->where('is_spv', true);
    }

    public function scopeOffday($query)
    {
        return $query->where('is_offday', true);
    }

    public function scopeAssigned($query)
    {
        return $query->whereNotNull('assigned_gate');
    }

    public function scopeUnassigned($query)
    {
        return $query->whereNull('assigned_gate');
    }
}
