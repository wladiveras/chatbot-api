<?php

namespace App\Repositories\User;

use App\Models\User;
use App\Repositories\BaseRepository;
use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Database\Eloquent\Collection;
use stdClass;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    public function all(): Collection
    {
        return $this->model->all();
    }

    public function paginate(int $limitPerPage): CursorPaginator
    {
        return $this->model->cursorPaginate($limitPerPage);
    }

    public function find(int|string $id): ?stdClass
    {
        return (object) $this->model->findOrFail($id)->toArray();
    }

    public function update(string|int $id, array $data): ?stdClass
    {
        return (object) tap($this->model->findOrFail($id))->update($data)->toArray();
    }

    public function create(array $data): stdClass
    {
        return (object) $this->model->create($data)->toArray();
    }

    public function delete(int|string $id): bool
    {
        return $this->model->findOrFail($id)->delete();
    }
}
