<?php

namespace App\Repositories\Client;

use App\Models\Client;
use App\Repositories\BaseRepository;

class ClientRepository extends BaseRepository implements ClientRepositoryInterface
{
    public function __construct(Client $model)
    {
        parent::__construct($model);
    }
}
