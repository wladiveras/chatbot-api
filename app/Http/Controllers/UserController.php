<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use App\Services\User\UserService;
use app\Http\Requests\UserRequest;
use App\Http\Resources\UserResource;
use App\Http\Resources\UserCollection;

class UserController extends Controller
{
    private UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function index(): UserCollection
    {
        Log::debug(__CLASS__.'.'.__FUNCTION__." => start");

        $users = $this->userService->findAllUsers();

        Log::debug(__CLASS__.'.'.__FUNCTION__." => end", [
            'data' => [
                'users' => $users,
            ],
        ]);

        return new UserCollection($users);
    }

    public function store(UserRequest $request): UserResource
    {
        Log::debug(__CLASS__.'.'.__FUNCTION__." => end", [
            'data' => [
                'request' => $request->validated(),
            ],
        ]);

        $user = $this->userService->createUser($request->validated());

        Log::debug(__CLASS__.'.'.__FUNCTION__." => end", [
            'data' => [
                'user' => $user,
            ],
        ]);

        return new UserResource($user);
    }

    public function show(int|string $id): UserResource
    {
        Log::debug(__CLASS__.'.'.__FUNCTION__." => start", [
            'data' => [
                'id' => $id,
            ],
        ]);

        $user = $this->userService->findUser($id);

        return new UserResource($user);
    }

    public function update(UserRequest $request, int|string $id): UserResource
    {
        Log::debug(__CLASS__.'.'.__FUNCTION__." => start", [
            'data' => [
                'request' => $request->validated(),
                'id' => $id,
            ],
        ]);

        $user = $this->userService->updateUser($id, $request->validated());

        Log::debug(__CLASS__.'.'.__FUNCTION__." => end", [
            'data' => [
                'user' => $user,
            ],
        ]);

        return new UserResource($user);
    }

    public function destroy(int|string $id): bool
    {
        Log::debug(__CLASS__.'.'.__FUNCTION__." => start", [
            'data' => [
                'id' => $id,
            ],
        ]);

        $user = $this->userService->deleteUser($id);

        Log::debug(__CLASS__.'.'.__FUNCTION__." => end", [
            'data' => [
                'user' => $user,
            ],
        ]);

        return $user;
    }
}
