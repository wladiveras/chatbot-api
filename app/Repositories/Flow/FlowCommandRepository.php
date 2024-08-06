<?php

namespace App\Repositories\Connection;

use App\Models\FlowCommand;
use App\Repositories\BaseRepository;
use Illuminate\Support\Collection;

class FlowCommandRepository extends BaseRepository implements ConnectionRepositoryInterface
{
    public function __construct(FlowCommand $model)
    {
        parent::__construct($model);
    }

    public function getUserConnections(): ?Collection
    {
        $user = auth()->user();

        return $this->model->where('user_id', $user->id)->get();
    }

    public function updateCommand($data)
    {
        $user = auth()->user();

        $this->model->where('user_id', $user->id)->where('node_id', $data['id'])->update($data);
    }
}
