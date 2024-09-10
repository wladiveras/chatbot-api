<?php

use Illuminate\Support\Facades\Schedule;

// Commands auto executable
Schedule::command('queue:prune-batches')->daily();
