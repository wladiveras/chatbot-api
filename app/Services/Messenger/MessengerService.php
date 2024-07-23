<?php

namespace App\Services\Messenger;

use App\Services\Messenger\Provinder\WhatsappProvinder;
use Illuminate\Support\Facades\Log;

class MessengerService
{
    public static function integration(string $provinder): MessengerServiceInterface
    {
        Log::debug(__CLASS__.'.'.__FUNCTION__.' => running');

        return match ($provinder) {
            'whatsapp' => new WhatsappProvinder,
            default => throw new \InvalidArgumentException('Invalid integration service.', 404),
        };
    }
}
