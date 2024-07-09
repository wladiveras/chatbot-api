<?php
namespace App\Services\Integration;

use App\Services\Integration\Connection\EvolutionConnection;


class IntegrationService
{
    public static function integration(string $connection): IntegrationServiceInterface
    {
        return match($connection) {
            'evolution' => new EvolutionConnection(),
            default => throw new \InvalidArgumentException('Invalid integration service'),
        };
    }
}
