<?php

namespace App\ES;

class ApplicationIndex
{
    public function getData() : array
    {
        return [
            'firstName',
            'lastName',
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
