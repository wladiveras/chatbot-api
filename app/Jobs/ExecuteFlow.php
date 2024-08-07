<?php

namespace App\Jobs;

use App\Repositories\FlowSession\FlowSessionRepository;
use App\Services\Messenger\MessengerService;
use Carbon\Carbon;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

class ExecuteFlow implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $payload;

    protected $messengerService;

    protected $flowSessionRepository;

    /**
     * Create a new job instance.
     */
    public $tries = 1;
    public $maxExceptions = 1;
    public function __construct($payload)
    {

        if (App::environment('production')) {
            $this->onConnection('sqs');
        }

        $this->onQueue('flows');

        $this->payload = $payload;

        $this->messengerService = App::make(MessengerService::class);
        $this->flowSessionRepository = App::make(FlowSessionRepository::class);
    }


    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::debug(__CLASS__ . '.' . __FUNCTION__ . ' => running');

        $action = Arr::get($this->payload['command'], 'action', null);
        $data = $this->prepareData();

        $this->executeCommand($action, $data);
    }

    public function failed(\Exception $exception)
    {
        Log::error('Job de fluxo falhou: ' . $exception->getMessage());
    }

    /**
     * Prepare the data for command execution.
     */
    protected function prepareData(): array
    {
        return [
            'token' => $this->payload['connection']->token,
            'session_key' => $this->payload['session']->session_key,
            'text' => $this->payload['text'],
            'command' => $this->payload['command'],
        ];
    }

    /**
     * Execute the command based on the action.
     */
    protected function executeCommand(?string $action, array $data): void
    {
        match ($action) {
            'delay' => $this->commandDelay($data),
            'message' => $this->commandMessage($data),
            'input' => $this->commandInput($data),
            default => Log::warning("Unknown command action: $action"),
        };
    }

    protected function updateSession()
    {
        return $this->flowSessionRepository->updateSession(
            flow_id: $this->payload['connection']->flow_id,
            connection_id: $this->payload['connection']->id,
            session_key: $this->payload['session']->session_key,
            last_step: $this->payload['steps'],
        );
    }

    // Commands
    protected function commandDelay($command)
    {
        Log::debug(__CLASS__ . '.' . __FUNCTION__ . ' => running');

        sleep(Arr::get($command, 'command.value', 1));

        return $this->updateSession();
    }

    protected function commandMessage($command)
    {
        Log::debug(__CLASS__ . '.' . __FUNCTION__ . ' => running');

        $messageText = Arr::get($command, 'command.value', null);
        $placeholders = $this->extractPlaceholders($messageText);
        $sessionMetas = $this->getSessionMetas($placeholders);

        if ($messageText && $sessionMetas) {
            $messageText = $this->replacePlaceholders($messageText, $sessionMetas);
        }

        $message = [
            'connection' => Arr::get($command, 'token', null),
            'number' => Arr::get($command, 'session_key', null),
            'delay' => Arr::get($command, 'command.delay', null),
            'type' => Arr::get($command, 'command.type', 'text'), // text, audio, video, image, media_audio, list, pool, status
            'value' => $messageText,
            'caption' => Arr::get($command, 'command.caption', null),
        ];

        $this->messengerService->integration('whatsapp')->send($message);

        return $this->updateSession();
    }

    protected function extractPlaceholders($messageText)
    {
        preg_match_all('/\{(\w+)\}/', $messageText, $matches);

        return $matches[1];
    }

    protected function getSessionMetas($placeholders)
    {
        $sessionMetas = [];

        foreach ($placeholders as $placeholder) {
            $sessionMeta = $this->flowSessionRepository->getSessionMeta(
                flow_session_id: $this->payload['session']->id,
                key: $placeholder
            );

            if ($sessionMeta) {
                $sessionMetas[$placeholder] = $sessionMeta->value;
            }

            return $sessionMetas;
        }
    }

    protected function replacePlaceholders($messageText, $sessionMetas)
    {
        return preg_replace_callback('/\{(\w+)\}/', function ($matches) use ($sessionMetas) {
            $key = $matches[1];

            return Arr::get($sessionMetas, $key, $matches[0]);
        }, $messageText);
    }

    protected function commandInput($command)
    {
        Log::debug(__CLASS__ . '.' . __FUNCTION__ . ' => running');

        $this->flowSessionRepository->setSessionMeta(
            flow_session_id: $this->payload['session']->id,
            key: Arr::get($command, 'command.name', null),
            value: $this->payload['text'],
            type: Arr::get($command, 'command.type', 'input'),
        );

        return $this->updateSession();
    }
}
