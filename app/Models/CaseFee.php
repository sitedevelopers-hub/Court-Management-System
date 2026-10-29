<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CaseFee extends Model
{
    protected $fillable = [
        'case_id',
        'participant_id',
        'created_by',
        'fee_reference',
        'fee_type',
        'description',
        'amount',
        'amount_paid',
        'due_date',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'amount_paid' => 'decimal:2',
            'due_date' => 'date',
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

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function getOutstandingAttribute(): string
    {
        return number_format(
            max(0, (float) $this->amount - (float) $this->amount_paid),
            2,
            '.',
            ''
        );
    }
}
