<?php

namespace App\Repositories\Connection;

use App\Models\Connection;
use App\Repositories\BaseRepository;
use Illuminate\Support\Collection;

class ConnectionRepository extends BaseRepository implements ConnectionRepositoryInterface
{
    public function __construct(Connection $model)
    {
        parent::__construct($model);
    }

    public function getUserConnections(): ?Collection
    {
        $user = auth()->user();

        return $this->model->where('user_id', $user->id)->with(['connectionProfile'])->orderBy('id', 'desc')->select('id', 'name', 'description', 'is_active')->get();
    }

    public function getUserConnection(int $id): ?Connection
    {
        $user = auth()->user();

        return $this->model->where('user_id', $user->id)->where('id', $id)->orderBy('id', 'desc')->first();
    }

    public function updateSelectFlow($connection_id, $data): ?bool
    {
        $user = auth()->user();

        return $this->model
            ->where('id', $connection_id)
            ->where('user_id', $user->id)
            ->update([
                'flow_id' => $data['flow_id'],
            ]);
    }
}
