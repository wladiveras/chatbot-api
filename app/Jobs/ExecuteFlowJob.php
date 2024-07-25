<?php

namespace App\Jobs;

use App\Repositories\FlowSession\FlowSessionRepository;
use App\Services\Messenger\MessengerService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

class ExecuteFlowJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $command;

    protected $messengerService;

    protected $session_key;

    protected $text;

    protected $jobConnection;

    protected $flowSessionRepository;

    protected $steps;

    /**
     * Create a new job instance.
     */
    public function __construct($jobConnection, $session_key, $text, $command, $steps)
    {
        $this->jobConnection = $jobConnection;
        $this->session_key = $session_key;
        $this->text = $text;
        $this->command = $command;
        $this->steps = $steps;

        $this->messengerService = App::make(MessengerService::class);
        $this->flowSessionRepository = App::make(FlowSessionRepository::class);
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {

        $action = Arr::get($this->command, 'action', null);

        $data = [
            'token' => $this->jobConnection->token,
            'session_key' => $this->session_key,
            'text' => $this->text,
            'command' => $this->command,
        ];

        match ($action) {
            'delay' => $this->commandDelay($data),
            'message' => $this->commandMessage($data),
            'input' => $this->commandInput($data),
            default => Log::warning("Unknown command action: $action"),
        };
    }

    protected function commandDelay($data)
    {
        Log::debug('Executing delay command: '.json_encode($data));

        sleep(Arr::get($data, 'command.value', 1));

        return $this->updateSession();
    }

    protected function commandMessage($data)
    {
        Log::debug('Executing message command step: '.$this->steps);

        $message = [
            'connection' => Arr::get($data, 'token', null),
            'number' => Arr::get($data, 'session_key', null),
            'delay' => Arr::get($data, 'command.delay', null),
            'type' => Arr::get($data, 'command.type', 'text'), // text, audio, video, image, media_audio, list, pool, status
            'value' => Arr::get($data, 'command.value', null),
            'caption' => Arr::get($data, 'command.caption', null),
        ];

        $this->messengerService->integration('whatsapp')->send($message);

        return $this->updateSession();
    }

    protected function updateSession()
    {
        return $this->flowSessionRepository->clientFinishSession(
            flow_id: $this->jobConnection->flow_id,
            connection_id: $this->jobConnection->id,
            session_key: $this->session_key,
            last_step: $this->steps
        );
    }

    protected function commandInput($command)
    {
        $this->updateSession();

        Log::debug('Executing input command: '.json_encode($command));
        // Implement input logic here
    }
}
