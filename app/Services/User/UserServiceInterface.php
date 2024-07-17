<?php

namespace App\Services\User;

interface UserServiceInterface
{
    public function findAllUsers();

    public function findUser(int|string $id);

    public function createUser(array $data);

    public function updateUser(int|string $id, array $data);

    public function deleteUser(int|string $id);
}
