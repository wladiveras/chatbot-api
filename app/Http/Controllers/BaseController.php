<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class BaseController
{
    /**
     * Handle a successful response.
     *
     * @param string|null $title
     * @param string|null $message
     * @param array|object|string $payload
     * @return JsonResponse
     */
    public function success(?string $title, ?string $message, array|object|string $payload): JsonResponse
    {
        if ($payload) {
            Log::debug(__CLASS__ . '.' . __FUNCTION__ . ' => success', [
                'success' => true,
                'service' => $payload,
            ]);
        }

        return response()->json([
            'data' => [
                'success' => true,
                'title' => $title,
                'message' => $message,
                'service' => $payload,
            ],
        ], 200);
    }

    /**
     * Handle an error response.
     *
     * @param string $path
     * @param string|null $title
     * @param string|null $message
     * @param string|int|null $code
     * @return JsonResponse
     */
    public function error(string $path, ?string $title = null, ?string $message = null, string|int|null $code = JsonResponse::HTTP_NOT_FOUND): JsonResponse
    {
        $defaultMessage = "Os dados fornecidos são inválidos. Por favor, verifique e tente novamente.";
        $title ??= "Erro ao processar a requisição";
        $message ??= $defaultMessage;

        Log::error(__CLASS__ . '.' . __FUNCTION__ . ' => error', [
            'success' => false,
            'title' => $title,
            'message' => $message,
            'path' => $path,
        ]);

        return response()->json([
            'data' => [
                'success' => false,
                'title' => $title,
                'message' => $message,
                'service' => [],
            ],
        ], $code == 0 ? 500 : $code);
    }
}
