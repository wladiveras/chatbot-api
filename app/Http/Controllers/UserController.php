<?php

namespace App\Http\Controllers;

use app\Http\Requests\UserRequest;
use App\Http\Resources\UserCollection;
use App\Http\Resources\UserResource;
use App\Services\User\UserService;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    private UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function index(): UserCollection
    {
        Log::debug(__CLASS__.'.'.__FUNCTION__.' => start');

        try {
            $users = $this->userService->findAllUsers();
        } catch (\Exception $exception) {
            $this->error(exception: $exception);
        }

        return new UserCollection($users);
    }

    public function store(UserRequest $request): UserResource
    {
        Log::debug(__CLASS__.'.'.__FUNCTION__.' => start', [
            'request' => $request,
        ]);

        try {
            $user = $this->userService->createUser($request->validated());
        } catch (\Exception $exception) {
            $this->error(data: $request, exception: $exception);
        }

        return new UserResource($user);
    }

    public function show(int|string $id): UserResource
    {
        Log::debug(__CLASS__.'.'.__FUNCTION__.' => start', [
            'id' => $id,
        ]);

        try {
            $user = $this->userService->findUser($id);
        } catch (\Exception $exception) {
            $this->error(data: $id, exception: $exception);
        }

        return new UserResource($user);
    }

    public function update(UserRequest $request, int|string $id): UserResource
    {
        Log::debug(__CLASS__.'.'.__FUNCTION__.' => start', [
            'id' => $id,
            'request' => $request,
        ]);

        try {
            $user = $this->userService->updateUser($id, $request->validated());
        } catch (\Exception $exception) {
            $this->error(data: $request, exception: $exception);
        }

        return new UserResource($user);
    }

    public function destroy(int|string $id): bool
    {
        Log::debug(__CLASS__.'.'.__FUNCTION__.' => start', [
            'id' => $id,
        ]);

        try {
            $user = $this->userService->deleteUser($id);
        } catch (\Exception $exception) {
            $this->error(data: $id, exception: $exception);
        }

        return $user;
    }

    private function error($data, \Exception $exception)
    {
        Log::error(__CLASS__.'.'.__FUNCTION__.' => error', [
            'data' => $data,
            'exception' => $exception,
            'message' => $exception->getMessage(),
        ]);

        abort(500, $exception->getMessage());
    }
}
