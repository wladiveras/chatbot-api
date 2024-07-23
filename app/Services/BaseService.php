<?php

namespace App\Services;

use App\Services\BaseServiceInterface;
use Illuminate\Support\Facades\Log;

class BaseService implements BaseServiceInterface
{
    public function error(string $path, string $message, mixed $payload = [], int $code = 404): object|array
    {

        Log::error(__CLASS__ . '.' . __FUNCTION__ . ' => error', [
            'success' => false,
            'message' => $message,
            'payload' => $payload,
            'path' => $path,
        ]);


        throw new \Exception($message, ($code == 0) ? 500 : $code);
    }

    public function success(string $message, mixed $payload = []): object|array
    {
        Log::debug(__CLASS__ . '.' . __FUNCTION__ . ' => success', [
            'success' => false,
            'message' => $message,
            'payload' => $payload,
        ]);

        return (object) [
            'success' => true,
            'message' => $message,
            'payload' => $payload,
        ];
    }
}
