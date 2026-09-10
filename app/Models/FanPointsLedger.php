<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FanPointsLedger extends Model
{
    use HasFactory;

    protected $table = 'fan_points_ledger';

    protected $fillable = [
        'tenant_id',
        'tenant_member_id',
        'type',
        'source',
        'points',
        'balance_after',
        'reference_type',
        'reference_id',
        'description',
    ];

    protected $casts = [
        'points' => 'integer',
        'balance_after' => 'integer',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function member()
    {
        return $this->belongsTo(TenantMember::class, 'tenant_member_id');
    }
}
