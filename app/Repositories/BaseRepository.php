<?php

namespace App\Repositories;

use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use stdClass;

class BaseRepository implements BaseRepositoryInterface
{
    public function __construct(protected Model $model) {}

    public function all(): Collection
    {
        return $this->model->all();
    }

    public function paginate(int $limitPerPage): CursorPaginator
    {
        return $this->model->cursorPaginate($limitPerPage);
    }

    public function find(string $column, mixed $value): ?stdClass
    {
        return (object) $this->model->where($column, $value)->firstOrFail()->toArray();
    }

    public function first($column, mixed $value): ?stdClass
    {
        return (object) $this->model->where($column, $value)->firstOrFail()->toArray();
    }

    public function create(array $data): stdClass
    {
        return (object) $this->model->create($data)->toArray();
    }

    public function update(string $column, mixed $value, array $data): ?stdClass
    {
        return (object) tap($this->model->where($column, $value))->update($data)->firstOrFail()->toArray();
    }

    public function delete(string $column, mixed $value): bool
    {
        return $this->model->where($column, $value)->delete();
    }

    public function exists($column, mixed $value): bool
    {

        return (bool) $this->model->where($column, $value)->exists();
    }
}
