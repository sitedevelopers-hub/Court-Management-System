<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkTemplate extends Model
{
    protected $fillable = [
        'title',
        'content',
        'file_format',
        'file_path',
        'created_by',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function shares(): HasMany
    {
        return $this->hasMany(TemplateShare::class, 'work_template_id');
    }

    public function sharedWith(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'template_shares', 'work_template_id', 'shared_with')
            ->withPivot('note', 'shared_at');
    }
}
