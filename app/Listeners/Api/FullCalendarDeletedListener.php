<?php

namespace App\Listeners\Api;

use App\Events\Api\FullCalendarEventDeleted;
use App\Jobs\Api\SendEmailJob;
use App\Mail\Api\FullCalendarEventDeletedEmail;

class FullCalendarDeletedListener
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
    public function handle(FullCalendarEventDeleted $event): void
    {
        dispatch(new SendEmailJob($event->event, FullCalendarEventDeletedEmail::class))->delay(now()->addSeconds(10));
    }
}
