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

        $service = $this->flowService->fetchFlows();

        if ($service->success) {
            return $this->success(
                title: "Fluxos retornados.",
                message: $service->message,
                payload: $service->payload
            );
        }

        return $this->error(
            path: __CLASS__ . '.' . __FUNCTION__,
            message: $service->message,
            code: $service->code
        );
    }

    public function show(Request $request, int $id): JsonResponse
    {
        Log::debug(__CLASS__ . '.' . __FUNCTION__ . ' => running', [
            'request' => $request,
        ]);

        $service = $this->flowService->fetchFlow($id);

        if ($service->success) {
            return $this->success(
                title: "Fluxo retornado.",
                message: $service->message,
                payload: $service->payload
            );
        }

        return $this->error(
            path: __CLASS__ . '.' . __FUNCTION__,
            message: $service->message,
            code: $service->code
        );
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
                code: 422
            );
        }

        $service = $this->flowService->create($data->validate());

        if ($service->success) {
            return $this->success(
                title: "Fluxo criado.",
                message: $service->message,
                payload: $service->payload
            );
        }

        return $this->error(
            path: __CLASS__ . '.' . __FUNCTION__,
            message: $service->message,
            code: $service->code
        );
    }

    public function delete(int $id, Request $request): JsonResponse
    {
        Log::debug(__CLASS__ . '.' . __FUNCTION__ . ' => running', [
            'request' => $request,
        ]);

        $service = $this->flowService->delete($id);

        if ($service->success) {
            return $this->success(
                title: "Fluxo deletado.",
                message: $service->message,
                payload: $service->payload
            );
        }

        return $this->error(
            path: __CLASS__ . '.' . __FUNCTION__,
            message: $service->message,
            code: $service->code
        );
    }

    public function resetSession(int $id, Request $request): JsonResponse
    {
        Log::debug(__CLASS__ . '.' . __FUNCTION__ . ' => running', [
            'request' => $request,
        ]);

        $service = $this->flowService->resetFlowSession($id);

        if ($service->success) {
            return $this->success(
                title: "Sessão do fluxo reiniciada.",
                message: $service->message,
                payload: $service->payload
            );
        }

        return $this->error(
            path: __CLASS__ . '.' . __FUNCTION__,
            message: $service->message,
            code: $service->code
        );
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
                code: 422
            );
        }

        $service = $this->flowService->update($id, $data->validate());

        if ($service->success) {
            return $this->success(
                title: "Fluxo atualizado.",
                message: $service->message,
                payload: $service->payload
            );
        }

        return $this->error(
            path: __CLASS__ . '.' . __FUNCTION__,
            message: $service->message,
            code: $service->code
        );
    }
}
