<?php

namespace App\Repositories\Connection;

use App\Models\Connection;
use App\Repositories\BaseRepository;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class ConnectionRepository extends BaseRepository implements ConnectionRepositoryInterface
{
    public function __construct(Connection $model)
    {
        parent::__construct($model);
    }

    public function getUserConnections(): ?Collection
    {
        $user = Auth::user();

        return $this->model->where('user_id', $user->id)->with(['connectionProfile'])->get();
    }

    public function getUserConnection(int $id): ?Connection
    {
        $user = Auth::user();

        return $this->model->where('user_id', $user->id)->where('id', $id)->first();
    }
}
