<?php

namespace App\Http\Controllers;

use App\Http\Resources\ResponseResource;
use App\Http\Resources\ResponseCollection;
use App\Enums\UserStatus;
use App\Services\User\UserService;
use Carbon\Carbon;
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

        try {
            $users = $this->userService->findAllUsers();

            return $this->success(
                response: Carbon::now()->toDateTimeString(),
                service: new ResponseResource($users),
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
            'name' => 'nullable|string|required|max:100|min:3',
            'email' => 'nullable|string|email|max:254',
            'status' => ['nullable', Rule::enum(UserStatus::class)],
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
            $user = $this->userService->createUser($data->validate());

            return $this->success(
                response: Carbon::now()->toDateTimeString(),
                service: new ResponseResource($user)
            );

        } catch (\Exception $exception) {
            return $this->error(
                path: __CLASS__ . '.' . __FUNCTION__,
                response: Carbon::now()->toDateTimeString(),
                service: $request->all(),
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
                service: new ResponseResource($user)
            );

        } catch (\Exception $exception) {
            return $this->error(
                path: __CLASS__ . '.' . __FUNCTION__,
                response: Carbon::now()->toDateTimeString(),
                service: $request->all(),
                code: $exception->getCode()
            );
        }
    }

    public function update(Request $request, int $id): JsonResponse
    {
        Log::debug(__CLASS__ . '.' . __FUNCTION__ . ' => running', [
            'request' => $request,
        ]);

        $data = Validator::make($request->all(), [
            'name' => 'string|required|max:100|min:3',
            'avatar' => 'string|url|max:254',
            'status' => [Rule::enum(UserStatus::class)],
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
            $user = $this->userService->updateUser($id, $data->validate());

            return $this->success(
                response: Carbon::now()->toDateTimeString(),
                service: new ResponseResource($user)
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

    public function destroy(int|string $id, Request $request): JsonResponse
    {
        Log::debug(__CLASS__ . '.' . __FUNCTION__ . ' => running', [
            'request' => $request,
        ]);

        try {
            $user = $this->userService->deleteUser($id);

            return $this->success(
                response: Carbon::now()->toDateTimeString(),
                service: $user
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
