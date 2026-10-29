<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CourtCase extends Model
{
    use SoftDeletes;

    protected $table = 'cases';

    protected $fillable = [
        'case_number',
        'case_title',
        'case_type',
        'description',
        'court_name',
        'filing_status',
        'filing_date',
        'closed_date',
    ];

    protected function casts(): array
    {
        return [
            'filing_date' => 'date',
            'closed_date' => 'date',
        ];
    }
    
    public function participants(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(
            Participant::class,
            'case_participants',
            'case_id',
            'participant_id'
        )->withPivot('role', 'notes')->withTimestamps();
    }

    public function hearings(): HasMany
    {
        return $this->hasMany(Hearing::class, 'case_id');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(CaseDocument::class, 'case_id');
    }

    public function fees(): HasMany
    {
        return $this->hasMany(CaseFee::class, 'case_id');
    }

    public function bailBonds(): HasMany
    {
        return $this->hasMany(BailBond::class, 'case_id');
    }

    public function events(): HasMany
    {
        return $this->hasMany(CourtEvent::class, 'case_id');
    }

}
