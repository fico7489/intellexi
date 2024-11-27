<?php

namespace App\ES;

use App\Models\Application;

class ApplicationIndex
{
    public function getClassName(): string
    {
        return Application::class;
    }

    public function getData() : array
    {
        return [
            'first_name',
            'last_name',
            'user' => [
                'email',
                'userType' => [
                    'name'
                ],
                'applications' => [
                    'club'
                ]
            ]
        ];
    }
}
