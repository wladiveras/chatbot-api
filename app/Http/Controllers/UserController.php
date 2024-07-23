<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserCollection;
use App\Http\Resources\UserResource;
use App\Services\User\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;
use Validator;
use Illuminate\Validation\Rule;
use App\Enums\UserStatus;

class UserController extends BaseController
{
    private UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function index(Request $request): JsonResponse
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
                payload: $request->all(),
                code: $exception->getCode()
            );
        }

    }

    public function store(Request $request): JsonResponse
    {
        Log::debug(__CLASS__ . '.' . __FUNCTION__ . ' => start', [
            'request' => $request,
        ]);

        $request = Validator::make($request->all(), [
            'name' => 'nullable|string|required|max:100|min:3',
            'email' => 'nullable|string|email|max:254',
            'status' => ['nullable', Rule::enum(UserStatus::class)],
        ]);

        if ($request->fails()) {
            return $this->error(
                message: 'Error de validação de dados.',
                payload: $request->errors(),
                code: 400
            );
        }

        try {
            $user = $this->userService->createUser($request->validated());

            return $this->success(
                message: 'Usuário atualizado com sucesso.',
                payload: new UserResource($user)
            );

        } catch (\Exception $exception) {
            return $this->error(
                message: $exception->getMessage(),
                payload: $request->all(),
                code: $exception->getCode()
            );
        }

    }

    public function show(int|string $id, Request $request): JsonResponse
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
                payload: $request->all(),
                code: $exception->getCode()
            );
        }
    }

    public function update(Request $request, int|string $id): JsonResponse
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
                payload: $request->all(),
                code: $exception->getCode()
            );
        }

    }

    public function destroy(int|string $id, Request $request): JsonResponse
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
                payload: $request->all(),
                code: $exception->getCode()
            );
        }
    }
}
