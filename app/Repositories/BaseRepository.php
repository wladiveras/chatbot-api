<?php

namespace App\Repositories;

use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use stdClass;

class BaseRepository implements BaseRepositoryInterface
{
    public function __construct(protected Model $model)
    {
    }

    public function all(): array|object
    {
        return $this->model->all();
    }

    public function paginate(int $limitPerPage): array|object
    {
        return $this->model->cursorPaginate($limitPerPage);
    }

    public function find(mixed $value, $column = 'id'): array|object
    {
        return (object) $this->model->where($column, $value)->first();
    }

    public function first(mixed $value, $column = 'id'): array|object
    {
        return (object) $this->model->where($column, $value)->first();
    }

    public function create(array $data, $column = 'id'): array|object
    {
        return (object) $this->model->create($data);
    }

    public function update(mixed $value, array $data, $column = 'id'): array|object
    {
        return (object) tap($this->model->where($column, $value))->update($data)->first();
    }

    public function delete(mixed $value, $column = 'id'): bool
    {
        return $this->model->where($column, $value)->delete();
    }

    public function exists(mixed $value, $column = 'id'): bool
    {

        return (bool) $this->model->where($column, $value)->exists();
    }
}
