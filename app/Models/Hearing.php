<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Hearing extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'case_id',
        'courtroom_id',
        'scheduled_start',
        'scheduled_end',
        'hearing_type',
        'status',
        'outcome',
        'adjournment_reason',
        'rescheduled_from',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_start' => 'datetime',
            'scheduled_end' => 'datetime',
            'rescheduled_from' => 'datetime',
        ];
    }

    public function courtCase(): BelongsTo
    {
        return $this->belongsTo(CourtCase::class, 'case_id');
    }

    public function courtroom(): BelongsTo
    {
        return $this->belongsTo(Courtroom::class);
    }
}
