<?php

namespace App\Repositories\Flow;

use App\Models\Flow;
use App\Repositories\BaseRepository;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class FlowRepository extends BaseRepository implements FlowRepositoryInterface
{
    public function __construct(Flow $model)
    {
        parent::__construct($model);
    }

    public function getUserFlows(): ?Collection
    {
        $user = Auth::user();

        return $this->model->where('user_id', $user->id)->get();
    }

    public function getUserFlow($id): ?Flow
    {
        $user = Auth::user();

        return $this->model->where('user_id', $user->id)->where('id', $id)->first();
    }
}
