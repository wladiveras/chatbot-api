<?php

namespace App\Repositories\ConnectionFlow;

use App\Models\ConnectionFlow;
use App\Repositories\BaseRepository;

class ConnectionFlowRepository extends BaseRepository implements ConnectionFlowRepositoryInterface
{
    public function __construct(ConnectionFlow $model)
    {
        parent::__construct($model);
    }
}
