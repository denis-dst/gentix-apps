<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Korwil extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'coordinator_user_id',
        'name',
        'code',
        'region',
        'phone',
        'address',
        'is_verified',
    ];

    protected $casts = [
        'is_verified' => 'boolean',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function coordinator()
    {
        return $this->belongsTo(User::class, 'coordinator_user_id');
    }

    public function members()
    {
        return $this->hasMany(TenantMember::class);
    }

    public function allocations()
    {
        return $this->hasMany(KorwilAllocation::class);
    }
}
