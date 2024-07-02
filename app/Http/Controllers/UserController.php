<?php

namespace App\Http\Controllers;

use App\Services\User\UserService;
use app\Http\Requests\User\UserRequest;
use App\Http\Resources\User\UserResource;
use App\Http\Resources\User\UserCollection;

class UserController extends Controller
{
    private UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function index()
    {
        $users = $this->userService->findAllUsers();

        return new UserCollection($users);
    }

    public function store(UserRequest $request)
    {
        $user = $this->userService->createUser($request->validated());

        return new UserResource($user);
    }

    public function show(int|string $id)
    {
        $user = $this->userService->findUser($id);

        return new UserResource($user);
    }

    public function update(UserRequest $request, int|string $id)
    {
        $user = $this->userService->updateUser($id, $request->validated());

        return new UserResource($user);
    }

    public function destroy(int|string $id): bool
    {
        $user = $this->userService->deleteUser($id);

        return $user;
    }
}
