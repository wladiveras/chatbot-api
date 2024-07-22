<?php

namespace App\Http\Controllers;

use App\Http\Requests\FlowRequest;
use App\Http\Resources\FlowResource;
use App\Services\Flow\FlowService;
use Illuminate\Support\Facades\Log;


class FlowController extends BaseController
{
    private FlowService $flowService;

    public function __construct(FlowService $flowService)
    {
        $this->flowService = $flowService;
    }

    public function create(string $flow_id, FlowRequest $request)
    {
        Log::debug(__CLASS__ . '.' . __FUNCTION__ . ' => start', [
            'request' => $request,
            'provinder' => $flow_id,
        ]);

        try {
            $flowService = $this->flowService->validate($request->validated())->create();
            $this->response(message: 'Fluxo criado com sucesso.', payload: $flowService);

        } catch (\Exception $exception) {
            $this->error(message: $request, payload: $exception, code: 500);
        }

        return new FlowResource($flowService);
    }
}
