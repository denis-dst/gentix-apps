<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MatchPrediction extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'event_id',
        'tenant_member_id',
        'predicted_home_score',
        'predicted_away_score',
        'predicted_first_goal_scorer',
        'is_calculated',
        'is_correct',
        'points_rewarded',
    ];

    protected $casts = [
        'predicted_home_score' => 'integer',
        'predicted_away_score' => 'integer',
        'is_calculated' => 'boolean',
        'is_correct' => 'boolean',
        'points_rewarded' => 'integer',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function member()
    {
        return $this->belongsTo(TenantMember::class, 'tenant_member_id');
    }
}
