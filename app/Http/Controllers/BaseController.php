<?php
namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller as Controller;

class BaseController extends Controller
{

    public function success(string|null $response, mixed $payload): JsonResponse
    {
        Log::debug(__CLASS__ . '.' . __FUNCTION__ . ' => success', [
            'success' => false,
            'response' => $response,
            'payload' => $payload,
        ]);

        $response = [
            'data' => [
                'success' => true,
                'response' => $response,
                'payload' => $payload
            ]
        ];

        return response()->json($response, 200);
    }


    public function error(string $path, string|null $response, $payload, int|null $code = 404): JsonResponse
    {
        Log::error(__CLASS__ . '.' . __FUNCTION__ . ' => error', [
            'success' => false,
            'response' => $response,
            'payload' => $payload,
            'path' => $path,
        ]);

        $response = [
            'data' => [
                'success' => false,
                'response' => $response,
                'payload' => $payload
            ],
        ];

        return response()->json($response, ($code == 0) ? 500 : $code);
    }
}
