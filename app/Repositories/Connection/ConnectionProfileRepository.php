<?php

namespace App\Repositories\Connection;

use App\Models\ConnectionProfile;
use App\Repositories\BaseRepository;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class ConnectionProfileRepository extends BaseRepository implements ConnectionRepositoryInterface
{
    public function __construct(ConnectionProfile $model)
    {
        parent::__construct($model);
    }

    public function getUserConnections(): ?Collection
    {
        $user = auth()->user();
        return $this->model->where('user_id', $user->id)->get();
    }

    public function createOrUpdateProfile($data)
    {
        return $this->model->updateOrCreate(
            ['connection_key' => $data['connection_key']],
            $data
        );
    }
}
