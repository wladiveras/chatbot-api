<?php

namespace App\Repositories\Example;

use App\Repositories\BaseRepository;
use App\Models\User; // Just an example, maybe Example Models.

class ExampleRepository extends BaseRepository implements ExampleRepositoryInterface
{
    public function __construct(User $model)
    {
        parent::__construct($model);
    }
}
