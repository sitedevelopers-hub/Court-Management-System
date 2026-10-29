<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FinancialAdjustment extends Model
{
    protected $fillable = [
        'case_id',
        'payment_id',
        'bail_bond_id',
        'recorded_by',
        'reference_number',
        'adjustment_type',
        'amount',
        'reason',
        'processed_at',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'processed_at' => 'datetime',
        ];
    }

    public function courtCase(): BelongsTo
    {
        return $this->belongsTo(CourtCase::class, 'case_id');
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    public function bailBond(): BelongsTo
    {
        return $this->belongsTo(BailBond::class, 'bail_bond_id');
    }

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}
