<?php

namespace App\Repositories\User;

use App\Models\MagicLink;
use App\Repositories\BaseRepository;

class magicLinkRepository extends BaseRepository implements magicLinkRepositoryInterface
{
    public function __construct(MagicLink $model)
    {
        parent::__construct($model);
    }
}
