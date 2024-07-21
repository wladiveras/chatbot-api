<?php

namespace App\Repositories\Flow;

use App\Models\Flow;
use App\Repositories\BaseRepository;

class FlowRepository extends BaseRepository implements FlowRepositoryInterface
{
    public function __construct(Flow $model)
    {
        parent::__construct($model);
    }
}
