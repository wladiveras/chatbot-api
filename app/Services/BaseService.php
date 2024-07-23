<?php

namespace App\Services;

use App\Services\BaseServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class BaseService implements BaseServiceInterface
{
    public function error(string $message, mixed $payload = [], int $code = 404): JsonResponse
    {
        Log::error(__CLASS__ . '.' . __FUNCTION__ . ' => error', [
            'success' => false,
            'message' => $message,
            'payload' => $payload,
        ]);

        throw new \Exception($message, $code);
    }

    public function success(string $message, mixed $payload = []): JsonResponse
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
