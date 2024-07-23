<?php
namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller as Controller;

class BaseController extends Controller
{

    public function success(string|null $response, mixed $service): JsonResponse
    {
        Log::debug(__CLASS__ . '.' . __FUNCTION__ . ' => success', [
            'success' => true,
            'response' => $response,
            'service' => $service,
        ]);

        $response = [
            'data' => [
                'success' => true,
                'response' => $response,
                'service' => $service
            ]
        ];

        return response()->json($response, 200);
    }


    public function error(string $path, string|null $response, $service, string|int|null $code = 404): JsonResponse
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
                'service' => $service
            ],
        ];

        return response()->json($response, ($code == 0) ? 500 : $code);
    }
}
