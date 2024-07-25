<?php

namespace App\Repositories\Flow;

use App\Models\Flow;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;
use stdClass;

class FlowRepository extends BaseRepository implements FlowRepositoryInterface
{
    public function __construct(Flow $model)
    {
        parent::__construct($model);
    }

    public function userFlows(): ?Collection
    {
        $user = Auth::user();

        return $this->model->where('user_id', $user->id)->get();
    }
}
