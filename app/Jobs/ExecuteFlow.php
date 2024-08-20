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
use Illuminate\Support\Facades\Config;

class ExecuteFlow implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $payload;

    protected $messengerService;

    protected $flowSessionRepository;

    protected $session;

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

        $this->session = $this->flowSessionRepository->fetchClientSession(
            flow_id: $this->payload['connection']->flow_id,
            connection_id: $this->payload['connection']->id,
            session_key: $this->payload['session']->session_key,
            id: $this->payload['session']->id ?? null,
        );
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info('App\Jobs\ExecuteFlow ........................... RUNNING');
        $action = Arr::get($this->payload['command'], 'action', null);
        $data = $this->prepareData();
        try {
            $this->executeCommand($action, $data);

            Log::info('App\Jobs\ExecuteFlow ........................... DONE', [$action, $data]);
        } catch (\Exception $e) {
            Log::error('App\Jobs\ExecuteFlow ...................... FAIL', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
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

    protected function nextStep()
    {
        return $this->flowSessionRepository->nextSessionStep(
            flow_id: $this->payload['connection']->flow_id,
            connection_id: $this->payload['connection']->id,
            session_key: $this->payload['session']->session_key,
            last_step: $this->payload['steps'],
        );
    }

    protected function waitingClientResponse(bool $isWaiting)
    {
        return $this->flowSessionRepository->waitingClientResponse(
            flow_id: $this->payload['connection']->flow_id,
            connection_id: $this->payload['connection']->id,
            session_key: $this->payload['session']->session_key,
            is_waiting: $isWaiting
        );
    }
    // Commands
    protected function commandDelay($command)
    {
        Log::debug(__CLASS__ . '.' . __FUNCTION__ . ' => running');
        Log::channel('supervisor')->info($command);

        sleep(Arr::get($command, 'command.value', 1));

        return $this->nextStep();
    }

    protected function commandMessage($command)
    {
        Log::debug(__CLASS__ . '.' . __FUNCTION__ . ' => running');
        Log::channel('supervisor')->info($command);

        $messageText = Arr::get($command, 'command.value', null);
        $commandType = Arr::get($command, 'command.type', 'text');

        if (in_array($commandType, ['video', 'image', 'audio', 'media_audio'])) {
            $directory = ($commandType === 'media_audio') ? 'audios' : "{$commandType}s";
            $url = Config::get('app.storage_url');
            $messageText = "{$url}/{$directory}/{$messageText}";
        }

        $message = [
            'connection' => Arr::get($command, 'token', null),
            'number' => Arr::get($command, 'session_key', null),
            'delay' => Arr::get($command, 'command.delay', 1),
            'type' => $commandType ?? null, // text, audio, video, image, media_audio, list, pool, status
            'value' => $messageText ?? null,
            'caption' => Arr::get($command, 'command.caption', null)
        ];

        $this->messengerService->integration('whatsapp')->send($message);

        return $this->nextStep();
    }

    protected function commandInput($command)
    {
        Log::debug(__CLASS__ . '.' . __FUNCTION__ . ' => running');

        Log::channel('supervisor')->info($this->payload['text']);

        if ($this->payload['text'] != null || $this->session->is_waiting) {
            $this->flowSessionRepository->setSessionMeta(
                flow_session_id: $this->payload['session']->id,
                key: Arr::get($command, 'command.name', null),
                value: $this->payload['text'],
                type: Arr::get($command, 'command.type', 'input'),
            );

            $this->waitingClientResponse(false);
            $this->nextStep();
            return;
        }

        return $this->waitingClientResponse(true);
    }
}
