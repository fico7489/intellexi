<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    public const ROLE_ADMINISTRATOR = 'Administrator';
    public const ROLE_APPLICANT = 'Applicant';

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'dob',
        'role',
    ];

    public function userType(): BelongsTo
    {
        return $this->belongsTo(UserType::class);
    }

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }
}
