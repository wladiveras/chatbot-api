<?php

namespace App\Repositories\Flow;

use App\Models\Flow;
use App\Repositories\BaseRepository;
use Illuminate\Support\Collection;

class FlowRepository extends BaseRepository implements FlowRepositoryInterface
{
    public function __construct(Flow $model)
    {
        parent::__construct($model);
    }

    public function getUserFlows(): ?Collection
    {
        $user = auth()->user();

        return $this->model->where('user_id', $user->id)->get();
    }

    public function getUserFlow($id): ?Flow
    {
        $user = auth()->user();

        return $this->model->where('user_id', $user->id)->with('commands')->where('id', $id)->first();
    }

    public function createFlowWithCommands($flow_id, $node_id, $data): ?Collection
    {
        $user = auth()->user();

        return $this->model->where('user_id', $user->id)->where('flow_id', $flow_id)->where('node_id', $node_id)->get();
    }
}
