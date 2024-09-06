<?php

namespace App\Repositories\Connection;

use App\Models\ConnectionProfile;
use App\Repositories\BaseRepository;
use Illuminate\Support\Collection;

class ConnectionProfileRepository extends BaseRepository implements ConnectionRepositoryInterface
{
    protected $user;

    public function __construct(ConnectionProfile $model)
    {
        parent::__construct($model);
        $this->user = auth()->user();
    }

    /**
     * Get all connections for the authenticated user.
     *
     * @return Collection|null
     */
    public function getUserConnections(): ?Collection
    {
        return $this->model->where('user_id', $this->user->id)->get();
    }

    /**
     * Create or update a connection profile.
     *
     * @param array $data
     * @return ConnectionProfile
     */
    public function createOrUpdateProfile(array $data): ConnectionProfile
    {
        return $this->model->updateOrCreate(
            ['connection_key' => $data['connection_key']],
            $data
        );
    }

}
