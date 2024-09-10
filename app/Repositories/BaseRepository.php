<?php

namespace App\Repositories;

use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class BaseRepository implements BaseRepositoryInterface
{
    public function __construct(protected Model $model)
    {
    }

    /**
     * Get all records.
     */
    public function all(): Collection
    {
        return $this->model->all();
    }

    /**
     * Paginate records.
     */
    public function paginate(int $limitPerPage): CursorPaginator
    {
        return $this->model->cursorPaginate($limitPerPage);
    }

    /**
     * Find a record by a specific column.
     */
    public function find(mixed $value, string $column = 'id'): ?Model
    {
        return $this->model->where($column, $value)->first();
    }

    /**
     * Create a new record.
     */
    public function create(array $data): Model
    {
        return $this->model->create($data);
    }

    /**
     * Update a record by a specific column.
     */
    public function update(mixed $value, array $data, string $column = 'id'): ?Model
    {
        $record = $this->model->where($column, $value)->first();
        if ($record) {
            $record->update($data);
        }

        return $record;
    }

    /**
     * Delete a record by a specific column.
     */
    public function delete(mixed $value, string $column = 'id'): bool
    {
        return (bool) $this->model->where($column, $value)->delete();
    }

    /**
     * Check if a record exists by a specific column.
     */
    public function exists(mixed $value, string $column = 'id'): bool
    {
        return $this->model->where($column, $value)->exists();
    }

    /**
     * Delete a cache key if it exists.
     *
     * @return bool True if the cache key was deleted, false otherwise.
     */
    public function deleteCacheKey(string $cacheKey): bool
    {
        if (!Cache::has($cacheKey)) {
            return false;
        }

        Cache::forget($cacheKey);

        return true;
    }
}
