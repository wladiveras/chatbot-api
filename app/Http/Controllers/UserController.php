<?php

namespace App\Http\Controllers;

use app\Http\Requests\UserRequest;
use App\Http\Resources\UserCollection;
use App\Http\Resources\UserResource;
use App\Services\User\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class UserController extends BaseController
{
    private UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function index(): JsonResponse
    {
        Log::debug(__CLASS__ . '.' . __FUNCTION__ . ' => start');

        try {
            $users = $this->userService->findAllUsers();

            return $this->success(
                message: 'Usuário atualizado com sucesso.',
                payload: new UserCollection($users)
            );

        } catch (\Exception $exception) {
            return $this->error(
                message: $exception->getMessage(),
                payload: $exception,
                code: 500
            );
        }

    }

    public function store(UserRequest $request): JsonResponse
    {
        Log::debug(__CLASS__ . '.' . __FUNCTION__ . ' => start', [
            'request' => $request,
        ]);

        try {
            $user = $this->userService->createUser($request->validated());

            return $this->success(
                message: 'Usuário atualizado com sucesso.',
                payload: new UserResource($user)
            );

        } catch (\Exception $exception) {
            return $this->error(
                message: $exception->getMessage(),
                payload: $request,
                code: 500
            );
        }

    }

    public function show(int|string $id): JsonResponse
    {
        Log::debug(__CLASS__ . '.' . __FUNCTION__ . ' => start', [
            'id' => $id,
        ]);

        try {
            $user = $this->userService->findUser($id);

            return $this->success(
                message: 'Usuário atualizado com sucesso.',
                payload: new UserResource($user)
            );

        } catch (\Exception $exception) {
            return $this->error(
                message: $exception->getMessage(),
                payload: $exception,
                code: 500
            );
        }
    }

    public function update(UserRequest $request, int|string $id): JsonResponse
    {
        Log::debug(__CLASS__ . '.' . __FUNCTION__ . ' => start', [
            'id' => $id,
            'request' => $request,
        ]);

        try {
            $user = $this->userService->updateUser($id, $request->validated());

            return $this->success(
                message: 'Usuário atualizado com sucesso.',
                payload: new UserResource($user)
            );

        } catch (\Exception $exception) {
            return $this->error(
                message: $exception->getMessage(),
                payload: $request,
                code: 500
            );
        }

    }

    public function destroy(int|string $id): JsonResponse
    {
        Log::debug(__CLASS__ . '.' . __FUNCTION__ . ' => start', [
            'id' => $id,
        ]);

        try {
            $user = $this->userService->deleteUser($id);

            return $this->success(
                message: 'Usuário deletado com sucesso.',
                payload: $user
            );

        } catch (\Exception $exception) {
            return $this->error(
                message: $exception->getMessage(),
                payload: $exception,
                code: 500
            );
        }
    }
}
