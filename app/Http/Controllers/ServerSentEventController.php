<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ServerSentEventController
{
    public function stream()
    {
        $response = new StreamedResponse(function () {
            while (true) {
                if (connection_aborted()) {
                    break;
                }

                $event = [
                    'event' => 'ping',
                    'data' => [
                        'message' =>
                            '',
                        'timestamp' => now()->toDateTimeString()
                    ]
                ];

                echo "event: {$event['event']}\n";
                echo "data: " . json_encode($event['data']) . "\n\n";

                ob_flush();
                flush();

                $this->listenForEvents();

                usleep(25); // 0.25 seconds

                if (microtime(true) - LARAVEL_START > 55) { // Break after ~55 seconds
                    break;
                }
            }
        });

        $response->headers->set('Content-Type', 'text/event-stream');
        $response->headers->set('Cache-Control', 'no-cache');
        $response->headers->set('Connection', 'keep-alive');
        $response->headers->set('X-Accel-Buffering', 'no');

        // Set CORS headers if needed
        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->headers->set('Access-Control-Allow-Methods', 'GET, POST');
        $response->headers->set('Access-Control-Allow-Headers', 'X-Requested-With');

        return $response;
    }

    private function listenForEvents()
    {
        Event::listen('*', function ($eventName, array $data) {

            echo "event: {$eventName}\n";
            echo "data: " . json_encode($data) . "\n\n";

            ob_flush();
            flush();
        });

        sleep(1);
    }
}
