<?php
namespace App\Services\User;

use Illuminate\Contracts\Pagination\CursorPaginator;
use stdClass;

interface UserServiceInterface
{

    public function findAllUsers(): CursorPaginator;
    public function findUser(int|string $id): ?stdClass;
    public function createUser(array $data): stdClass;
    public function updateUser(int|string $id, array $data): ?stdClass;
    public function deleteUser(int|string $id): bool;
}
