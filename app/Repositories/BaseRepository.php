<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\CursorPaginator;

class BaseRepository implements BaseRepositoryInterface
{
    public function __construct(protected Model $model)
    {
    }

    /**
     * Get all records.
     *
     * @return Collection
     */
    public function all(): Collection
    {
        return $this->model->all();
    }

    /**
     * Paginate records.
     *
     * @param int $limitPerPage
     * @return CursorPaginator
     */
    public function paginate(int $limitPerPage): CursorPaginator
    {
        return $this->model->cursorPaginate($limitPerPage);
    }

    /**
     * Find a record by a specific column.
     *
     * @param mixed $value
     * @param string $column
     * @return Model|null
     */
    public function find(mixed $value, string $column = 'id'): ?Model
    {
        return $this->model->where($column, $value)->first();
    }

    /**
     * Create a new record.
     *
     * @param array $data
     * @return Model
     */
    public function create(array $data): Model
    {
        return $this->model->create($data);
    }

    /**
     * Update a record by a specific column.
     *
     * @param mixed $value
     * @param array $data
     * @param string $column
     * @return Model|null
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
     *
     * @param mixed $value
     * @param string $column
     * @return bool
     */
    public function delete(mixed $value, string $column = 'id'): bool
    {
        return (bool) $this->model->where($column, $value)->delete();
    }

    /**
     * Check if a record exists by a specific column.
     *
     * @param mixed $value
     * @param string $column
     * @return bool
     */
    public function exists(mixed $value, string $column = 'id'): bool
    {
        return $this->model->where($column, $value)->exists();
    }
}
