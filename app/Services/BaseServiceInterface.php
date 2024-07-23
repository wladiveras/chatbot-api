<?php

namespace App\Services;

use Illuminate\Http\JsonResponse;

interface BaseServiceInterface
{
    public function error(string $path, string $message, mixed $payload = [], int $code = 404): object|array;
    public function success(string $message, mixed $payload = []): object|array;
}
