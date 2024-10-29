<?php

namespace App\Repositories\User;

use App\Models\User;
use App\Repositories\User\UserInterface;

class UserRepository implements UserInterface
{
    public function create(array $data): User
    {
        return User::create($data);
    }
}
