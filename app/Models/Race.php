<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Race extends Model
{
    use HasUuids;

    public const RACE_DISTANCE_5K = '5k';
    public const RACE_DISTANCE_10K = '10k';
    public const RACE_HALF_MARATHON = 'HalfMarathon';
    public const RACE_MARATHON = 'Marathon';

    public const RACES = [
        self::RACE_DISTANCE_5K,
        self::RACE_DISTANCE_10K,
        self::RACE_HALF_MARATHON,
        self::RACE_MARATHON,
    ];

    protected $fillable = [
        'name', 'distance',
    ];

    public function race(): HasMany
    {
        return $this->hasMany(Race::class);
    }
}
