<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BailBond extends Model
{
    protected $fillable = [
        'case_id',
        'participant_id',
        'recorded_by',
        'reference_number',
        'bond_type',
        'ordered_amount',
        'deposited_amount',
        'order_date',
        'status',
        'conditions',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'ordered_amount' => 'decimal:2',
            'deposited_amount' => 'decimal:2',
            'order_date' => 'date',
        ];
    }

    public function courtCase(): BelongsTo
    {
        return $this->belongsTo(CourtCase::class, 'case_id');
    }

    public function participant(): BelongsTo
    {
        return $this->belongsTo(Participant::class);
    }

    public function adjustments(): HasMany
    {
        return $this->hasMany(FinancialAdjustment::class, 'bail_bond_id');
    }
}
