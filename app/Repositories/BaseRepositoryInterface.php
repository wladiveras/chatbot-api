<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\CursorPaginator;

interface BaseRepositoryInterface
{
    /**
     * Get all records.
     *
     * @return Collection
     */
    public function all(): Collection;

    /**
     * Paginate records.
     *
     * @param int $limitPerPage
     * @return CursorPaginator
     */
    public function paginate(int $limitPerPage): CursorPaginator;

    /**
     * Find a record by a specific column.
     *
     * @param mixed $value
     * @param string $column
     * @return Model|null
     */
    public function find(mixed $value, string $column = 'id'): ?Model;

    /**
     * Create a new record.
     *
     * @param array $data
     * @return Model
     */
    public function create(array $data): Model;

    /**
     * Update a record by a specific column.
     *
     * @param mixed $value
     * @param array $data
     * @param string $column
     * @return Model|null
     */
    public function update(mixed $value, array $data, string $column = 'id'): ?Model;

    /**
     * Delete a record by a specific column.
     *
     * @param mixed $value
     * @param string $column
     * @return bool
     */
    public function delete(mixed $value, string $column = 'id'): bool;

    /**
     * Check if a record exists by a specific column.
     *
     * @param mixed $value
     * @param string $column
     * @return bool
     */
    public function exists(mixed $value, string $column = 'id'): bool;
}
