<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ConnectionStatusEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $connection;

    public function __construct($connection)
    {
        $this->connection = $connection;
    }

    public function broadcastOn(): void
    {
    }

    public function broadcastAs(): string
    {
        return 'event.connection.changed';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->connection->id,
            'timestamp' => now()->toDateTimeString()
        ];
    }
}
