<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class UserPolicy
{

    public function uploadFiles(User $user)
    {
        return true;
    }
}
