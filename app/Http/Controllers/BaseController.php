<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller as Controller;

class BaseController extends Controller
{
    /**
     * success response method.
     *
     * @return \Illuminate\Http\Response
     */
    public function response($message, $payload)
    {
        Log::debug(__CLASS__ . '.' . __FUNCTION__ . ' => response', [
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

    /**
     * return error response.
     *
     * @return \Illuminate\Http\Response
     */
    public function error($message, $payload, $code = 404)
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

        return response()->json($response, $code);
    }
}
