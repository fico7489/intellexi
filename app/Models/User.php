<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    public const string ROLE_ADMINISTRATOR = 'Administrator';
    public const string ROLE_APPLICANT = 'Applicant';

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

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }
}
