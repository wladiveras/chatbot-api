<?php

namespace App\Jobs;

use App\Repositories\FlowSession\FlowSessionRepository;
use Carbon\Carbon;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\App;

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
     * @param  mixed  $session
     */
    public function __construct($session, bool $isRunning)
    {

        $this->onQueue('flows');
        $this->session = $session;
        $this->isRunning = $isRunning;
        $this->flowSessionRepository = App::make(FlowSessionRepository::class);
    }

    /**
     * Set the connection for the job.
     */

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
     */
    public function retryUntil(): Carbon
    {
        return Carbon::now()->addMinutes(10);
    }

    /**
     * Determine the number of times the job may be attempted.
     */
    public function tries(): int
    {
        return 0;
    }
}
