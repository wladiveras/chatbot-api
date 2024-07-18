<?php

namespace App\Observers;

use App\Models\FlowSession;

class FlowSessionObserver
{
    /**
     * Handle the FlowSession "created" event.
     */
    public function created(FlowSession $flowSession): void
    {
        //
    }

    /**
     * Handle the FlowSession "updated" event.
     */
    public function updated(FlowSession $flowSession): void
    {
        //
    }

    /**
     * Handle the FlowSession "deleted" event.
     */
    public function deleted(FlowSession $flowSession): void
    {
        //
    }

    /**
     * Handle the FlowSession "restored" event.
     */
    public function restored(FlowSession $flowSession): void
    {
        //
    }

    /**
     * Handle the FlowSession "force deleted" event.
     */
    public function forceDeleted(FlowSession $flowSession): void
    {
        //
    }
}
