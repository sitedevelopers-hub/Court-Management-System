<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourtEvent extends Model
{
    protected $fillable = [
        'case_id',
        'created_by',
        'title',
        'description',
        'event_type',
        'starts_at',
        'ends_at',
        'remind_at',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'remind_at' => 'datetime',
        ];
    }

    public function courtCase(): BelongsTo
    {
        return $this->belongsTo(CourtCase::class, 'case_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
