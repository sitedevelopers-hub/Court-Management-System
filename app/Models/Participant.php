<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Participant extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'participant_number',
        'first_name',
        'middle_name',
        'last_name',
        'participant_type',
        'email',
        'phone',
        'national_id',
        'organization',
        'address',
        'status',
    ];

    public function cases(): BelongsToMany
    {
        return $this->belongsToMany(
            CourtCase::class,
            'case_participants',
            'participant_id',
            'case_id'
        )->withPivot('role', 'notes')->withTimestamps();
    }

    public function caseAssignments(): HasMany
    {
        return $this->hasMany(CaseParticipant::class);
    }

    public function bailBonds(): HasMany
    {
        return $this->hasMany(BailBond::class);
    }

    public function fees(): HasMany
    {
        return $this->hasMany(CaseFee::class);
    }

    public function getFullNameAttribute(): string
    {
        return trim(implode(' ', array_filter([
            $this->first_name,
            $this->middle_name,
            $this->last_name,
        ])));
    }
}
