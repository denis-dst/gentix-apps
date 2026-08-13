<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RangerGateQuota extends Model
{
    use HasFactory;

    protected $fillable = [
        'gate_name',
        'male_quota',
        'female_quota',
    ];
}
