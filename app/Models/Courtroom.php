<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Courtroom extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'room_number',
        'court_name',
        'capacity',
        'location',
        'status',
        'notes',
    ];

    public function hearings(): HasMany
    {
        return $this->hasMany(Hearing::class);
    }
}
