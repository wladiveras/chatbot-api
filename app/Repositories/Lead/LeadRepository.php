<?php

namespace App\Repositories\Lead;

use App\Models\Lead;
use App\Repositories\BaseRepository;

class LeadRepository extends BaseRepository implements LeadRepositoryInterface
{
    public function __construct(Lead $model)
    {
        parent::__construct($model);
    }
}
