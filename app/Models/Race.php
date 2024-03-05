<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Race extends Model
{
    use HasUuids;

    protected $fillable = [
        'name', 'distance',
    ];

    public function race(): HasMany
    {
        return $this->hasMany(Race::class);
    }
}
