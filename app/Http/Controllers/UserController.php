<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserCollection;
use App\Http\Resources\UserResource;
use App\Services\User\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Validation\Rule;
use App\Enums\UserStatus;
use Carbon\Carbon;

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

        try {
            $users = $this->userService->findAllUsers();

            return $this->success(
                response: Carbon::now()->toDateTimeString(),
                payload: $users,
            );

        } catch (\Exception $exception) {
            return $this->error(
                path: __CLASS__ . '.' . __FUNCTION__,
                response: $exception->getMessage(),
                payload: $request->all(),
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
            'name' => 'nullable|string|required|max:100|min:3',
            'email' => 'nullable|string|email|max:254',
            'status' => ['nullable', Rule::enum(UserStatus::class)],
        ]);

        if ($data->fails()) {
            return $this->error(
                path: __CLASS__ . '.' . __FUNCTION__,
                response: Carbon::now()->toDateTimeString(),
                payload: $data->errors(),
                code: 400
            );
        }

        try {
            $user = $this->userService->createUser($data->validate());

            return $this->success(
                response: Carbon::now()->toDateTimeString(),
                payload: new UserResource($user)
            );

        } catch (\Exception $exception) {
            return $this->error(
                path: __CLASS__ . '.' . __FUNCTION__,
                response: Carbon::now()->toDateTimeString(),
                payload: $request->all(),
                code: $exception->getCode()
            );
        }

    }

    public function show(int|string $id, Request $request): JsonResponse
    {
        Log::debug(__CLASS__ . '.' . __FUNCTION__ . ' => running', [
            'request' => $request,
        ]);

        try {
            $user = $this->userService->findUser($id);

            return $this->success(
                response: Carbon::now()->toDateTimeString(),
                payload: new UserResource($user)
            );

        } catch (\Exception $exception) {
            return $this->error(
                path: __CLASS__ . '.' . __FUNCTION__,
                response: Carbon::now()->toDateTimeString(),
                payload: $request->all(),
                code: $exception->getCode()
            );
        }
    }

    public function update(Request $request, int|string $id): JsonResponse
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
                response: Carbon::now()->toDateTimeString(),
                payload: $data->errors(),
                code: 400
            );
        }

        try {
            $user = $this->userService->updateUser($id, $data->validate());

            return $this->success(
                response: Carbon::now()->toDateTimeString(),
                payload: new UserResource($user)
            );

        } catch (\Exception $exception) {
            return $this->error(
                path: __CLASS__ . '.' . __FUNCTION__,
                response: $exception->getMessage(),
                payload: $request->all(),
                code: $exception->getCode()
            );
        }

    }

    public function destroy(int|string $id, Request $request): JsonResponse
    {
        Log::debug(__CLASS__ . '.' . __FUNCTION__ . ' => running', [
            'request' => $request,
        ]);

        try {
            $user = $this->userService->deleteUser($id);

            return $this->success(
                response: Carbon::now()->toDateTimeString(),
                payload: $user
            );

        } catch (\Exception $exception) {
            return $this->error(
                path: __CLASS__ . '.' . __FUNCTION__,
                response: $exception->getMessage(),
                payload: $request->all(),
                code: $exception->getCode()
            );
        }
    }
}
