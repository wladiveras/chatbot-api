<?php

namespace App\Http\Controllers;

use App\Http\Requests\FlowRequest;
use App\Http\Resources\FlowResource;
use App\Services\Flow\FlowService;
use Illuminate\Support\Facades\Log;

class FlowController extends Controller
{
    private FlowService $flowService;

    public function __construct(FlowService $flowService)
    {
        $this->flowService = $flowService;
    }

    public function create(string $flow_id, FlowRequest $request)
    {
        Log::debug(__CLASS__.'.'.__FUNCTION__.' => start', [
            'request' => $request,
            'provinder' => $flow_id,
        ]);

        try {
            $flowService = $this->flowService->validate($request->validated())->create();
        } catch (\Exception $exception) {
            $this->error(data: $request, exception: $exception);
        }

        return new FlowResource($flowService);
    }

    private function error($data, $exception)
    {
        Log::error(__CLASS__.'.'.__FUNCTION__.' => error', [
            'message' => $exception->getMessage(),
            'data' => $data,
            'exception' => $exception,
        ]);

        abort(500, $exception->getMessage());
    }
}
