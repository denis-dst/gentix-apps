<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FanQuiz extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'title',
        'description',
        'starts_at',
        'expires_at',
        'points_reward',
        'questions',
        'is_active',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'points_reward' => 'integer',
        'questions' => 'array',
        'is_active' => 'boolean',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function participants()
    {
        return $this->hasMany(FanQuizParticipant::class);
    }
}
