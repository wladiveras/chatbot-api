<?php

namespace App\Repositories\Connection;

use App\Models\Connection;
use App\Repositories\BaseRepository;
use Illuminate\Support\Collection;

class ConnectionRepository extends BaseRepository implements ConnectionRepositoryInterface
{
    protected $user;

    public function __construct(Connection $model)
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
        return $this->model->where('user_id', $this->user->id)
            ->with(['connectionProfile'])
            ->orderBy('id', 'desc')
            ->select('id', 'name', 'description', 'is_active')
            ->get();
    }

    /**
     * Get a specific connection for the authenticated user by ID.
     *
     * @param int $id
     * @return Connection|null
     */
    public function getUserConnection(int $id): ?Connection
    {
        return $this->model->where('user_id', $this->user->id)
            ->where('id', $id)
            ->orderBy('id', 'desc')
            ->first();
    }

    /**
     * Update the selected flow for a connection.
     *
     * @param int $connection_id
     * @param array $data
     * @return bool|null
     */
    public function updateSelectFlow(int $connection_id, array $data): ?bool
    {
        return $this->model->where('id', $connection_id)
            ->where('user_id', $this->user->id)
            ->update([
                'flow_id' => $data['flow_id'],
            ]);
    }
}
