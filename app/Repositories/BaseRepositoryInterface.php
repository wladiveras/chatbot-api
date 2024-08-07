<?php

namespace App\Repositories;

interface BaseRepositoryInterface
{
    public function all(): array|object;

    public function paginate(int $limitPerPage): array|object;

    public function find(mixed $value, $column = 'id'): array|object|null;

    public function first(mixed $value, $column = 'id'): array|object|null;

    public function create(array $data): array|object;

    public function update(mixed $value, array $data, $column = 'id'): array|object|null;

    public function delete(mixed $value, $column = 'id'): bool;
}
