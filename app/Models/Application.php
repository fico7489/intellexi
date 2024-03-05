<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Application extends Model
{
    use HasUuids;

    protected $fillable = [
        'first_name',
        'last_name',
        'club',
        'race_id',
        'user_id',
    ];

    public function race(): BelongsTo
    {
        return $this->belongsTo(Race::class);
    }
}
