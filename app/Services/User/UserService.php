<?php

namespace App\Services\User;

use App\Repositories\User\UserRepository;
use Illuminate\Support\Facades\App;

class UserService implements UserServiceInterface
{
    private $userRepository;

    public function __construct()
    {
        $this->userRepository = App::make(UserRepository::class);
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

    private function response(bool $success, string $message, mixed $payload = []): object
    {

        if ($success === false) {
            throw new \Exception($message, 502); // bad gateway
        }

        return (object) [
            'success' => $success,
            'message' => $message,
            'payload' => $payload,
        ];
    }
}
