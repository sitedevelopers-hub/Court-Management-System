<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GeneratedReport extends Model
{
    protected $fillable = [
        'title',
        'report_type',
        'start_date',
        'end_date',
        'file_path',
        'created_by',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function shares(): HasMany
    {
        return $this->hasMany(ReportShare::class, 'generated_report_id');
    }

    public function sharedWith(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'report_shares', 'generated_report_id', 'shared_with')
            ->withPivot('note', 'shared_at');
    }
}
