<?php
namespace App\Services\Integration;

use App\Services\Integration\Connection\EvolutionConnection;


class IntegrationService
{
    public static function integration(string $service): IntegrationServiceInterface
    {
        return match($service) {
            'evolution' => new EvolutionConnection(),
            default => throw new \InvalidArgumentException('Invalid integration service'),
        };
    }
}
