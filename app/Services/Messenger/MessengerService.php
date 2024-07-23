<?php

namespace App\Services\Messenger;

use Illuminate\Support\Facades\Log;

use App\Services\Messenger\Provinder\WhatsappProvinder;


class MessengerService
{
    public static function integration(string $provinder): MessengerServiceInterface
    {
        Log::debug(__CLASS__ . '.' . __FUNCTION__ . ' => running');

        return match ($provinder) {
            'whatsapp' => new WhatsappProvinder,
            default => throw new \InvalidArgumentException('Invalid integration service.', 404),
        };
    }
}
