<?php

namespace App\Http\Controllers;

use App\Http\Resources\FlowResource;
use App\Services\Flow\FlowService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Validator;

class FlowController extends BaseController
{
    private FlowService $flowService;

    public function __construct(FlowService $flowService)
    {
        $this->flowService = $flowService;
    }

    public function create(string $flow_id, Request $request): JsonResponse
    {
        Log::debug(__CLASS__.'.'.__FUNCTION__.' => running', [
            'request' => $request,
        ]);

        $data = Validator::make($request->all(), [
            'data' => 'array',
        ]);

        if ($data->fails()) {
            return $this->error(
                path: __CLASS__.'.'.__FUNCTION__,
                response: Carbon::now()->toDateTimeString(),
                service: $data->errors(),
                code: 400
            );
        }

        try {
            $flowService = $this->flowService->validate($data->validate())->create();

            return $this->success(
                response: Carbon::now()->toDateTimeString(),
                service: new FlowResource($flowService)
            );

        } catch (\Exception $exception) {
            return $this->error(
                path: __CLASS__.'.'.__FUNCTION__,
                response: $exception->getMessage(),
                service: $request->all(),
                code: $exception->getCode()
            );
        }
    }
}
