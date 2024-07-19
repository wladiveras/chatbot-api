<?php

namespace App\Repositories\ConnectionSetting;

use App\Models\ConnectionSetting;
use App\Repositories\BaseRepository;

class ConnectionSettingRepository extends BaseRepository implements ConnectionSettingRepositoryInterface
{
    public function __construct(ConnectionSetting $model)
    {
        parent::__construct($model);
    }
}
