<?php

namespace App\Repositories\Client;

use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Database\Eloquent\Collection;
use stdClass;

interface ClientRepositoryInterface
{
    public function all(): Collection;

    public function paginate(int $limitPerPage): CursorPaginator;

    public function find(int|string $id): ?stdClass;

    public function create(array $data): stdClass;

    public function update(int|string $id, array $data): ?stdClass;

    public function delete(int|string $id): bool;
}
