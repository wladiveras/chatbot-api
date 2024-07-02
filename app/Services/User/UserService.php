<?php

namespace App\Services\User;

use Illuminate\Contracts\Pagination\CursorPaginator;
use App\Repositories\User\UserRepositoryInterface;
use App\Services\User\UserServiceInterface;
use stdClass;

class UserService implements UserServiceInterface
{
    private $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function findAllUsers(): CursorPaginator
    {
        return $this->userRepository->paginate(10);
    }

    public function findUser(int|string $id): ?stdClass
    {
        return (object) $this->userRepository->find($id);
    }

    public function createUser(array $data): stdClass
    {
        return (object) $this->userRepository->create($data);
    }

    public function updateUser(int|string $id, array $data): ?stdClass
    {
        return $this->userRepository->update($id, $data);
    }

    public function deleteUser(int|string $id): bool
    {
        return $this->userRepository->delete($id);
    }

}
