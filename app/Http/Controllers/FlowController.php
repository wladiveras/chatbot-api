<?php

namespace App\Http\Controllers;


use App\Http\Resources\FlowResource;
use App\Services\Flow\FlowService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Validation\Rule;

class FlowController extends BaseController
{
    private FlowService $flowService;

    public function __construct(FlowService $flowService)
    {
        $this->flowService = $flowService;
    }

    public function create(string $flow_id, Request $request): JsonResponse
    {
        Log::debug(__CLASS__ . '.' . __FUNCTION__ . ' => start', [
            'request' => $request,
            'provinder' => $flow_id,
        ]);

        $request = Validator::make($request->all(), [
            'data' => 'array',
        ]);

        if ($request->fails()) {
            return $this->error(
                message: 'Error de validação de dados.',
                payload: $request->errors(),
                code: 400
            );
        }

        try {
            $flowService = $this->flowService->validate($request->validated())->create();

            return $this->success(
                message: 'Fluxo criado com sucesso.',
                payload: new FlowResource($flowService)
            );

        } catch (\Exception $exception) {
            return $this->error(
                message: $request,
                payload: $exception,
                code: 500
            );
        }
    }
}
