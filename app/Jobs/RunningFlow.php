<?php

namespace App\Jobs;


use App\Repositories\FlowSession\FlowSessionRepository;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Bus\Batchable;
use Illuminate\Support\Facades\App;
use Carbon\Carbon;

class RunningFlow implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var mixed
     */
    protected $session;

    /**
     * @var bool
     */
    protected $isRunning;

    /**
     * @var FlowSessionRepository
     */
    protected $flowSessionRepository;

    /**
     * Create a new job instance.
     *
     * @param mixed $session
     * @param bool $isRunning
     */
    public function __construct($session, bool $isRunning)
    {
        $this->setConnection();
        $this->onQueue('flows');
        $this->session = $session;
        $this->isRunning = $isRunning;
        $this->flowSessionRepository = App::make(FlowSessionRepository::class);
    }

    /**
     * Set the connection for the job.
     */
    protected function setConnection(): void
    {
        if (App::environment('production')) {
            $this->onConnection('sqs');
        }
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->session->is_running = $this->isRunning;
        $this->session->save();
    }

    /**
     * Determine the time at which the job should timeout.
     *
     * @return Carbon
     */
    public function retryUntil(): Carbon
    {
        return Carbon::now()->addMinutes(10);
    }

    /**
     * Determine the number of times the job may be attempted.
     *
     * @return int
     */
    public function tries(): int
    {
        return 0;
    }
}
