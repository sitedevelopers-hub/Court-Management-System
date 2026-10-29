<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TemplateShare extends Model
{
    protected $fillable = [
        'work_template_id',
        'shared_with',
        'note',
        'shared_at',
    ];

    protected $casts = ['shared_at' => 'datetime'];

    public function template(): BelongsTo
    {
        return $this->belongsTo(WorkTemplate::class, 'work_template_id');
    }

    public function recipient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'shared_with');
    }
}
