<?php

namespace App\Services\User;

use App\Repositories\User\UserRepositoryInterface;

class UserService implements UserServiceInterface
{
    private $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function findAllUsers()
    {
        return $this->userRepository->paginate(10);
    }

    public function findUser(int|string $id)
    {
        return (object) $this->userRepository->find($id);
    }

    public function createUser(array $data)
    {
        return (object) $this->userRepository->create($data);
    }

    public function updateUser(int|string $id, array $data)
    {
        return $this->userRepository->update($id, $data);
    }

    public function deleteUser(int|string $id)
    {
        return $this->userRepository->delete($id);
    }
}
