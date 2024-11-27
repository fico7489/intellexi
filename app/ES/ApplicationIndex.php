<?php

namespace App\ES;

class ApplicationIndex
{
    public function getData() : array
    {
        return [
            'first_name',
            'last_name',
            'user' => [
                'email',
                'userType' => [
                    'name'
                ]
            ]
        ];
    }
}
