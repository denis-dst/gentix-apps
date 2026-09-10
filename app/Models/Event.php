<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tenant_id', 'name', 'slug', 'description', 'terms_conditions', 'venue', 'city', 'google_maps_url',
        'background_image', 'banner_image', 'event_start_date', 
        'event_end_date', 'gate_open_at', 'gate_close_at', 'status', 'meta', 'security_code',
        'is_free', 'max_tickets_per_transaction', 'umroh_question_enabled', 'evoucher_info',
        'purchase_flow', 'thermal_paper_width_mm', 'thermal_paper_height_mm',
        'season_name', 'home_team_name', 'away_team_name', 'home_team_logo', 'away_team_logo',
        'home_score', 'away_score', 'allow_season_pass', 'season_pass_claim_days_before',
        'is_member_priority_sale', 'member_priority_start_at', 'general_sale_start_at',
    ];

    protected $casts = [
        'event_start_date' => 'datetime',
        'event_end_date' => 'datetime',
        'gate_open_at' => 'datetime',
        'gate_close_at' => 'datetime',
        'member_priority_start_at' => 'datetime',
        'general_sale_start_at' => 'datetime',
        'meta' => 'array',
        'is_free' => 'boolean',
        'allow_season_pass' => 'boolean',
        'is_member_priority_sale' => 'boolean',
        'season_pass_claim_days_before' => 'integer',
        'max_tickets_per_transaction' => 'integer',
        'umroh_question_enabled' => 'boolean',
        'thermal_paper_width_mm' => 'integer',
        'thermal_paper_height_mm' => 'integer',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function ticketCategories()
    {
        return $this->hasMany(TicketCategory::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function gates()
    {
        return $this->hasMany(Gate::class);
    }

    public function seasonPassClaims()
    {
        return $this->hasMany(SeasonPassClaim::class);
    }

    public function matchPredictions()
    {
        return $this->hasMany(MatchPrediction::class);
    }

    public function korwilAllocations()
    {
        return $this->hasMany(KorwilAllocation::class);
    }

    // Current occupancy calculation
    public function getCurrentOccupancyAttribute()
    {
        $in = GateLog::where('event_id', $this->id)->where('type', 'IN')->count();
        $out = GateLog::where('event_id', $this->id)->where('type', 'OUT')->count();
        return $in - $out;
    }

    /**
     * Get the dynamic upload tasks/proofs required for registration.
     */
    public function getRegistrationProofs(): array
    {
        if (isset($this->meta['registration_proofs']) && is_array($this->meta['registration_proofs'])) {
            return $this->meta['registration_proofs'];
        }

        $proofs = [];
        if ($this->meta['proof_ig_required'] ?? true) {
            $proofs[] = [
                'id' => 'proof_ig',
                'label' => 'Bukti follow IG',
                'instruction' => 'Klik untuk follow @batikumrah dan ambil screenshot',
                'link' => 'https://www.instagram.com/batikumrah?igsh=MTFibTFtOHF3dGp4MQ==',
                'is_required' => true,
            ];
        }
        if ($this->meta['proof_review_required'] ?? true) {
            $proofs[] = [
                'id' => 'proof_review',
                'label' => 'Bukti Google Review',
                'instruction' => 'Isi Google Review lalu ambil screenshot',
                'link' => 'https://bit.ly/googlereviewbatik',
                'is_required' => true,
            ];
        }
        return $proofs;
    }
}
