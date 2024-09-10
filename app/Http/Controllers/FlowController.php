<?php

namespace App\Http\Controllers;

use App\Http\Resources\ResponseResource;
use App\Http\Resources\ResponseCollection;
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

    public function index(Request $request): JsonResponse
    {
        Log::debug(__CLASS__ . '.' . __FUNCTION__ . ' => running', [
            'request' => $request,
        ]);

        try {
            $flowService = $this->flowService->fetchFlows();

            return $this->success(
                response: Carbon::now()->toDateTimeString(),
                service: new ResponseResource($flowService)
            );

        } catch (\Exception $exception) {
            return $this->error(
                path: __CLASS__ . '.' . __FUNCTION__,
                response: $exception->getMessage(),
                service: $request->all(),
                code: $exception->getCode()
            );
        }
    }

    public function show(Request $request, int $id): JsonResponse
    {
        Log::debug(__CLASS__ . '.' . __FUNCTION__ . ' => running', [
            'request' => $request,
        ]);

        try {
            $flowService = $this->flowService->fetchFlow($id);

            return $this->success(
                response: Carbon::now()->toDateTimeString(),
                service: new ResponseResource($flowService)
            );

        } catch (\Exception $exception) {
            return $this->error(
                path: __CLASS__ . '.' . __FUNCTION__,
                response: $exception->getMessage(),
                service: $request->all(),
                code: $exception->getCode()
            );
        }
    }

    public function store(Request $request): JsonResponse
    {
        Log::debug(__CLASS__ . '.' . __FUNCTION__ . ' => running', [
            'request' => $request,
        ]);

        $data = Validator::make($request->all(), [
            'name' => 'required|string',
            'description' => 'required|string',
            'node' => 'array',
            'edge' => 'array',
            'commands' => 'required|array',
            'type' => 'required|string',
        ]);

        if ($data->fails()) {
            return $this->error(
                path: __CLASS__ . '.' . __FUNCTION__,
                response: Carbon::now()->toDateTimeString(),
                service: $data->errors(),
                code: 400
            );
        }

        try {
            $flowService = $this->flowService->create($data->validate());

            return $this->success(
                response: Carbon::now()->toDateTimeString(),
                service: new ResponseResource($flowService)
            );

        } catch (\Exception $exception) {
            return $this->error(
                path: __CLASS__ . '.' . __FUNCTION__,
                response: $exception->getMessage(),
                service: $request->all(),
                code: $exception->getCode()
            );
        }
    }

    public function delete(int $id, Request $request): JsonResponse
    {
        Log::debug(__CLASS__ . '.' . __FUNCTION__ . ' => running', [
            'request' => $request,
        ]);

        try {
            $flowService = $this->flowService->delete($id);

            return $this->success(
                response: Carbon::now()->toDateTimeString(),
                service: $flowService
            );

        } catch (\Exception $exception) {
            return $this->error(
                path: __CLASS__ . '.' . __FUNCTION__,
                response: $exception->getMessage(),
                service: $request->all(),
                code: $exception->getCode()
            );
        }
    }

    public function reset(int $id, Request $request): JsonResponse
    {
        Log::debug(__CLASS__ . '.' . __FUNCTION__ . ' => running', [
            'request' => $request,
        ]);

        try {
            $flowService = $this->flowService->resetFlowSession($id);

            return $this->success(
                response: Carbon::now()->toDateTimeString(),
                service: $flowService
            );

        } catch (\Exception $exception) {
            return $this->error(
                path: __CLASS__ . '.' . __FUNCTION__,
                response: $exception->getMessage(),
                service: $request->all(),
                code: $exception->getCode()
            );
        }
    }

    public function update(int $id, Request $request): JsonResponse
    {
        Log::debug(__CLASS__ . '.' . __FUNCTION__ . ' => running', [
            'request' => $request,
        ]);

        $data = Validator::make($request->all(), [
            'name' => 'string',
            'description' => 'string',
            'node' => 'array',
            'edge' => 'array',
            'commands' => 'array',
            'type' => 'string',
        ]);

        if ($data->fails()) {
            return $this->error(
                path: __CLASS__ . '.' . __FUNCTION__,
                response: Carbon::now()->toDateTimeString(),
                service: $data->errors(),
                code: 400
            );
        }

        try {
            $flowService = $this->flowService->update($id, $data->validate());

            return $this->success(
                response: Carbon::now()->toDateTimeString(),
                service: new ResponseResource($flowService)
            );

        } catch (\Exception $exception) {
            return $this->error(
                path: __CLASS__ . '.' . __FUNCTION__,
                response: $exception->getMessage(),
                service: $request->all(),
                code: $exception->getCode()
            );
        }
    }
}
