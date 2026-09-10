<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FanQuizParticipant extends Model
{
    use HasFactory;

    protected $fillable = [
        'fan_quiz_id',
        'tenant_member_id',
        'score',
        'is_passed',
        'points_earned',
        'completed_at',
    ];

    protected $casts = [
        'score' => 'integer',
        'is_passed' => 'boolean',
        'points_earned' => 'integer',
        'completed_at' => 'datetime',
    ];

    public function quiz()
    {
        return $this->belongsTo(FanQuiz::class, 'fan_quiz_id');
    }

    public function member()
    {
        return $this->belongsTo(TenantMember::class, 'tenant_member_id');
    }
}
