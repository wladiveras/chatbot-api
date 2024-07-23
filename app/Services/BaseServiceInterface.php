<?php

namespace App\Services;

use Illuminate\Http\JsonResponse;

interface BaseServiceInterface
{
    public function error(string $message, mixed $payload = [], int $code = 404): JsonResponse;
    public function success(string $message, mixed $payload = []): JsonResponse;
}
