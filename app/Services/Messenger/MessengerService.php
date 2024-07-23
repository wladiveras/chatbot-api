<?php

namespace App\Services\Messenger;

use App\Services\Messenger\Provinder\WhatsappProvinder;

class MessengerService
{
    public static function integration(string $provinder): MessengerServiceInterface
    {
        return match ($provinder) {
            'whatsapp' => new WhatsappProvinder,
            default => throw new \InvalidArgumentException('Invalid integration service.', 404),
        };
    }
}
