<?php

namespace App\Listeners\Api;

use App\Events\Api\NewFullCalendarEvent;
use App\Jobs\Api\SendEmailJob;
use App\Mail\Api\NewFullCalendarEventEmail;

class NewFullCalendarEventListener
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
    public function handle(NewFullCalendarEvent $event): void
    {
        dispatch(new SendEmailJob($event->event, NewFullCalendarEventEmail::class))->delay(now()->addSeconds(5));
    }
}
