<?php

namespace App\Repositories\User;

use App\Models\User;

interface UserInterface
{
    /**
     * @param array $data
     * @return User
    */
    public function create(array $data): User;
}