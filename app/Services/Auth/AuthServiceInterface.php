<?php

namespace App\Services\Auth;

use Illuminate\Http\JsonResponse;
use stdClass;

interface AuthServiceInterface
{
    public function signIn(array $data): ?stdClass;
    public function auth(): ?stdClass;
    public function refreshToken(): ?stdClass;


}
