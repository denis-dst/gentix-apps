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
        'assigned_gate',
        'assigned_at',
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
    ];

    public function scopeMale($query)
    {
        return $query->where('gender', 'male');
    }

    public function scopeFemale($query)
    {
        return $query->where('gender', 'female');
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
