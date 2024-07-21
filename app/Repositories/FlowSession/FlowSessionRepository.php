<?php

namespace App\Repositories\FlowSession;

use App\Models\FlowSession;
use App\Repositories\BaseRepository;

class FlowSessionRepository extends BaseRepository implements FlowSessionRepositoryInterface
{
    public function __construct(FlowSession $model)
    {
        parent::__construct($model);
    }
}
