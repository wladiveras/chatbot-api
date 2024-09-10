<?php

namespace App\Repositories;

use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface BaseRepositoryInterface
{
    /**
     * Get all records.
     */
    public function all(): Collection;

    /**
     * Paginate records.
     */
    public function paginate(int $limitPerPage): CursorPaginator;

    /**
     * Find a record by a specific column.
     */
    public function find(mixed $value, string $column = 'id'): ?Model;

    /**
     * Create a new record.
     */
    public function create(array $data): Model;

    /**
     * Update a record by a specific column.
     */
    public function update(mixed $value, array $data, string $column = 'id'): ?Model;

    /**
     * Delete a record by a specific column.
     */
    public function delete(mixed $value, string $column = 'id'): bool;

    /**
     * Check if a record exists by a specific column.
     */
    public function exists(mixed $value, string $column = 'id'): bool;
}
