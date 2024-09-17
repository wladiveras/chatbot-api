<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class BaseService implements BaseServiceInterface
{
    public function success(string $message, mixed $payload = []): object|array
    {
        Log::debug(__CLASS__ . '.' . __FUNCTION__ . ' => success', [
            'success' => true,
            'message' => $message,
            'payload' => $payload,
        ]);

        return (object) [
            'success' => true,
            'message' => $message,
            'payload' => $payload,
        ];
    }

    public function error(
        string $path,
        string $message,
        mixed $payload = [],
        string|int|null $code = 404
    ): object|array {

        Log::error(__CLASS__ . '.' . __FUNCTION__ . ' => error', [
            'success' => false,
            'message' => $message,
            'payload' => $payload,
            'path' => $path,
        ]);

        return (object) [
            'success' => false,
            'message' => $code === 500 ? "Não foi possível processar sua solicitação no momento, tente novamente mais tarde." : $message,
            'payload' => $payload,
            'code' => $code,
        ];
    }
}
