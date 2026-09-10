<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KorwilMemberConsent extends Model
{
    use HasFactory;

    protected $fillable = [
        'korwil_allocation_id',
        'tenant_member_id',
        'nik',
        'full_name',
        'phone',
        'ticket_id',
        'consent_status',
        'responded_at',
    ];

    protected $casts = [
        'responded_at' => 'datetime',
    ];

    public function allocation()
    {
        return $this->belongsTo(KorwilAllocation::class, 'korwil_allocation_id');
    }

    public function member()
    {
        return $this->belongsTo(TenantMember::class, 'tenant_member_id');
    }

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }
}
