<?php

namespace App\Listeners\Api;

use App\Events\Api\FullCalendarEventUpdated;
use App\Jobs\Api\SendEmailJob;
use App\Mail\Api\FullCalendarEventUpdatedEmail;

class FullCalendarUpdatedListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(FullCalendarEventUpdated $event): void
    {
        dispatch(new SendEmailJob($event->event, FullCalendarEventUpdatedEmail::class))->delay(now()->addSeconds(5));
    }
}
