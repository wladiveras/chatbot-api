<?php

namespace App\Services\User;

interface UserServiceInterface
{
    public function findAllUsers();

    public function findUser(int $id);

    public function createUser(array $data);

    public function updateUser(int $id, array $data);

    public function deleteUser(int $id);
}
