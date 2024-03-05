<?php

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function asAdministrator(): self
    {
        return $this->prepareUser('administrator@example.com');
    }

    protected function asApplicant(): self
    {
        return $this->prepareUser('applicant@example.com');
    }

    protected function asApplicant2(): self
    {
        return $this->prepareUser('applicant2@example.com');
    }

    private function prepareUser(string $email): self
    {
        $user = User::where(['email' => $email])->first();

        $token = JWTAuth::fromUser($user);

        return $this->withToken($token);
    }

    protected function findUserByEmail(string $email): User
    {
        return User::where(['email' => $email])->first();
    }
}
