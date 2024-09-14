<?php

namespace App\Http\Controllers;


use App\Enums\UserStatus;
use App\Services\User\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Validator;

class UserController extends BaseController
{
    private UserService $userService;


    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function index(Request $request): JsonResponse
    {
        Log::debug(__CLASS__ . '.' . __FUNCTION__ . ' => running', [
            'request' => $request,
        ]);

        $service = $this->userService->findAllUsers();

        if ($service->success) {
            return $this->success(
                title: "Usuários retornados.",
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
            'name' => 'nullable|string|required|max:100|min:3',
            'email' => 'nullable|string|email|max:254',
            'status' => ['nullable', Rule::enum(UserStatus::class)],
        ]);

        if ($data->fails()) {
            return $this->error(
                path: __CLASS__ . '.' . __FUNCTION__,
                code: 422
            );
        }

        $service = $this->userService->createUser($data->validate());

        if ($service->success) {
            return $this->success(
                title: "Usuário criado.",
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

    public function show(int|string $id, Request $request): JsonResponse
    {
        Log::debug(__CLASS__ . '.' . __FUNCTION__ . ' => running', [
            'request' => $request,
        ]);

        $service = $this->userService->findUser($id);

        if ($service->success) {
            return $this->success(
                title: "Usuário retornado.",
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

    public function update(Request $request, int $id): JsonResponse
    {
        Log::debug(__CLASS__ . '.' . __FUNCTION__ . ' => running', [
            'request' => $request,
        ]);

        $data = Validator::make($request->all(), [
            'name' => 'string|required|max:100|min:3',
            'avatar' => 'string|max:254',
            'status' => [Rule::enum(UserStatus::class)],
        ]);

        if ($data->fails()) {
            return $this->error(
                path: __CLASS__ . '.' . __FUNCTION__,
                code: 422
            );
        }

        $service = $this->userService->updateUser($id, $data->validate());

        if ($service->success) {
            return $this->success(
                title: "Usuário atualizado.",
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

    public function destroy(int|string $id, Request $request): JsonResponse
    {
        Log::debug(__CLASS__ . '.' . __FUNCTION__ . ' => running', [
            'request' => $request,
        ]);

        $service = $this->userService->deleteUser($id);

        if ($service->success) {
            return $this->success(
                title: "Usuário deletado.",
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
