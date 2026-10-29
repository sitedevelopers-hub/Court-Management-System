<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CaseParticipant extends Model
{
    protected $fillable = [
        'case_id',
        'participant_id',
        'role',
        'notes',
    ];

    public function courtCase(): BelongsTo
    {
        return $this->belongsTo(CourtCase::class, 'case_id');
    }

    public function participant(): BelongsTo
    {
        return $this->belongsTo(Participant::class);
    }
}
