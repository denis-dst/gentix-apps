<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MembershipTier extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'name',
        'slug',
        'description',
        'price',
        'validity_days',
        'early_access_hours',
        'ticket_discount_percent',
        'benefits',
        'badge_color',
        'card_template_url',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'ticket_discount_percent' => 'decimal:2',
        'benefits' => 'array',
        'is_active' => 'boolean',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function members()
    {
        return $this->hasMany(TenantMember::class);
    }
}
