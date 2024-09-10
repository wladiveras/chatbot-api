<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class BaseController extends Controller
{
    /**
     * Handle a successful response.
     *
     * @param string|null $response
     * @param mixed $service
     * @return JsonResponse
     */
    public function success(?string $response, mixed $service): JsonResponse
    {
        if ($service) {
            Log::debug(__CLASS__ . '.' . __FUNCTION__ . ' => success', [
                'success' => true,
                'response' => $response,
                'service' => $service,
            ]);
        }

        return response()->json([
            'data' => [
                'success' => true,
                'response' => $response,
                'service' => $service,
            ],
        ], 200);
    }

    /**
     * Handle an error response.
     *
     * @param string $path
     * @param string|null $response
     * @param mixed $service
     * @param string|int|null $code
     * @return JsonResponse
     */
    public function error(string $path, ?string $response, mixed $service, string|int|null $code = JsonResponse::HTTP_NOT_FOUND): JsonResponse
    {
        Log::error(__CLASS__ . '.' . __FUNCTION__ . ' => error', [
            'success' => false,
            'response' => $response,
            'service' => $service,
            'path' => $path,
        ]);

        return response()->json([
            'data' => [
                'success' => false,
                'response' => $response,
                'service' => $service,
            ],
        ], $code == 0 ? 500 : $code);
    }
}
