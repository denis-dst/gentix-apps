<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TenantMember extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'user_id',
        'membership_tier_id',
        'member_number',
        'full_name_ktp',
        'nik',
        'phone',
        'birth_date',
        'gender',
        'address',
        'avatar',
        'ktp_photo',
        'face_photo',
        'kyc_status',
        'kyc_reject_reason',
        'kyc_verified_at',
        'kyc_verified_by',
        'points_balance',
        'korwil_id',
        'joined_at',
        'expired_at',
        'status',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'kyc_verified_at' => 'datetime',
        'joined_at' => 'datetime',
        'expired_at' => 'datetime',
        'points_balance' => 'integer',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tier()
    {
        return $this->belongsTo(MembershipTier::class, 'membership_tier_id');
    }

    public function kycVerifier()
    {
        return $this->belongsTo(User::class, 'kyc_verified_by');
    }

    public function korwil()
    {
        return $this->belongsTo(Korwil::class, 'korwil_id');
    }

    public function seasonPasses()
    {
        return $this->hasMany(SeasonPass::class);
    }

    public function pointsLedger()
    {
        return $this->hasMany(FanPointsLedger::class);
    }

    public function matchPredictions()
    {
        return $this->hasMany(MatchPrediction::class);
    }

    public function isKycVerified(): bool
    {
        return $this->kyc_status === 'verified';
    }

    public function isMembershipActive(): bool
    {
        if ($this->status !== 'active') return false;
        if ($this->expired_at && $this->expired_at->isPast()) return false;
        return true;
    }
}
