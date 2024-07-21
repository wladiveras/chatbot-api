<?php

namespace App\Repositories\Connection;

use App\Models\Connection;
use App\Repositories\BaseRepository;

class ConnectionRepository extends BaseRepository implements ConnectionRepositoryInterface
{
    public function __construct(Connection $model)
    {
        parent::__construct($model);
    }
}
