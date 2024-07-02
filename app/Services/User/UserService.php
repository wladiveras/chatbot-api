<?php

namespace App\Services\User;

use App\Repositories\User\UserRepositoryInterface;
use App\Services\User\UserServiceInterface;
use Illuminate\Database\Eloquent\Collection;
use stdClass;

class UserService implements UserServiceInterface
{
    private $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function findAllUsers(): Collection
    {
        return $this->userRepository->all();
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
