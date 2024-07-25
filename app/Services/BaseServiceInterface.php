<?php

namespace App\Services;

interface BaseServiceInterface
{
    public function error(string $path, string $message, mixed $payload = [], string|int|null $code = 404): object|array;

    public function success(string $message, mixed $payload = []): object|array;
}
