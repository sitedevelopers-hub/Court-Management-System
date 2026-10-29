<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReportShare extends Model
{
    protected $fillable = [
        'generated_report_id',
        'shared_with',
        'note',
        'shared_at',
    ];

    protected $casts = ['shared_at' => 'datetime'];

    public function report(): BelongsTo
    {
        return $this->belongsTo(GeneratedReport::class, 'generated_report_id');
    }

    public function recipient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'shared_with');
    }
}
