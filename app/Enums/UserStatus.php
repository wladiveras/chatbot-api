<?php

namespace App\Enums;

enum UserStatus: string
{
    case ACTIVE = 'active';
    case BANNED = 'banned';
    case PENDING = 'pending';
}
