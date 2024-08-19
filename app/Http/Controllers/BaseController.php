<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller as Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class BaseController extends Controller
{
    public function success(?string $response, mixed $service): JsonResponse
    {
        if ($service) {
            Log::debug(__CLASS__ . '.' . __FUNCTION__ . ' => success', [
                'success' => true,
                'response' => $response,
                'service' => $service,
            ]);
        }

        $response = [
            'data' => [
                'success' => true,
                'response' => $response,
                'service' => $service,
            ],
        ];

        return response()->json($response, 200);
    }

    public function error(string $path, ?string $response, $service, string|int|null $code = 404): JsonResponse
    {
        Log::error(__CLASS__ . '.' . __FUNCTION__ . ' => error', [
            'success' => false,
            'response' => $response,
            'service' => $service,
            'path' => $path,
        ]);

        $response = [
            'data' => [
                'success' => false,
                'response' => $response,
                'service' => $service,
            ],
        ];

        return response()->json($response, ($code == 0) ? 500 : $code);
    }
}
