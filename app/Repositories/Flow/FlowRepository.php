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

        return $this->model->where('user_id', $user->id)->orderBy('id', 'desc')->select('name', 'id', 'description', 'updated_at', 'created_at', 'is_active')->get();
    }

    public function getUserFlow($id): ?Flow
    {
        $user = auth()->user();

        return $this->model->where('user_id', $user->id)->where('id', $id)->first();
    }
}
