<?php
namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller as Controller;

class BaseController extends Controller
{

    public function success(string|null $message, mixed $payload): JsonResponse
    {
        Log::debug(__CLASS__ . '.' . __FUNCTION__ . ' => success', [
            'success' => false,
            'message' => $message,
            'payload' => $payload,
        ]);

        $response = [
            'data' => [
                'success' => true,
                'message' => $message,
                'payload' => $payload
            ]
        ];

        return response()->json($response, 200);
    }


    public function error(string|null $message, $payload, int|null $code = 404): JsonResponse
    {
        Log::error(__CLASS__ . '.' . __FUNCTION__ . ' => error', [
            'success' => false,
            'message' => $message,
            'payload' => $payload,
        ]);

        $response = [
            'data' => [
                'success' => false,
                'message' => $message,
                'payload' => $payload
            ],
        ];

        return response()->json($response, $code ?? 404);
    }
}
