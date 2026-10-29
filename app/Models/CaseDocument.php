<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CaseDocument extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'case_id',
        'uploaded_by',
        'document_title',
        'category',
        'original_filename',
        'stored_filename',
        'storage_path',
        'mime_type',
        'file_size',
        'description',
        'confidentiality',
    ];

    public function courtCase(): BelongsTo
    {
        return $this->belongsTo(CourtCase::class, 'case_id');
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function accessLogs(): HasMany
    {
        return $this->hasMany(DocumentAccessLog::class);
    }
}
