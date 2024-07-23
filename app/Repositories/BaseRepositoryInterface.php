<?php

namespace App\Repositories;

use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Database\Eloquent\Collection;
use stdClass;

interface BaseRepositoryInterface
{
    public function all(): array|object;

    public function paginate(int $limitPerPage): array|object;

    public function find(mixed $value, $column = 'id'): array|object;

    public function create(array $data, $column = 'id'): array|object;

    public function update(mixed $value, array $data, $column = 'id'): array|object;

    public function delete(mixed $value, $column = 'id'): bool;
}
