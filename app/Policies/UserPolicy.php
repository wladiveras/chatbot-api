<?php

namespace App\Policies;

use App\Models\User;


class UserPolicy
{
    public function uploadFiles(User $user)
    {
        return true;
    }
}
