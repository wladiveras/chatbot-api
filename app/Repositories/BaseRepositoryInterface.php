<?php

namespace App\Repositories;

use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Database\Eloquent\Collection;
use stdClass;

interface BaseRepositoryInterface
{
    public function all(): Collection;

    public function paginate(int $limitPerPage): array|object;

    public function find(mixed $value, $column = 'id'): ?stdClass;

    public function create(array $data, $column = 'id'): stdClass;

    public function update(mixed $value, array $data, $column = 'id'): ?stdClass;

    public function delete(mixed $value, $column = 'id'): bool;
}
