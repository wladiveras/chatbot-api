<?php

use App\Mail\MagicLinkEmail;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;


Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Artisan::command('send-welcome-mail', function () {
    Mail::to('wladinart@gmail.com')->send(new MagicLinkEmail('Jon', 'http://localhost:8000'));
})->purpose('Send magic link mail');


// Commands auto executable
Schedule::command('queue:prune-batches --hours=48')->daily();
Schedule::command('horizon:snapshot')->everyFiveMinutes();
