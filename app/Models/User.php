<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasUuids;

    public const ROLE_ADMINISTRATOR = 'Administrator';
    public const ROLE_APPLICANT = 'Applicant';

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'dob',
        'role',
    ];

    public function getJWTIdentifier()
    {
        return $this->id;
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
}
