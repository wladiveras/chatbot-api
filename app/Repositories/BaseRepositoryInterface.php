<?php

namespace App\Repositories;

use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Database\Eloquent\Collection;
use stdClass;

interface BaseRepositoryInterface
{
    public function all(): Collection;

    public function paginate(int $limitPerPage): CursorPaginator;

    public function find(string $column, mixed $value): ?stdClass;

    public function create(array $data): stdClass;

    public function update(string $column, mixed $value, array $data): ?stdClass;

    public function delete(string $column, mixed $value): bool;
}
