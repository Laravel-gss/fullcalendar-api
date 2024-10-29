<?php

namespace App\Observers\Api;

use App\Enums\Api\FullCalendarEventStatus;
use App\Models\FullCalendarEvent;

class FullCalendarEventObserver
{
    /**
     * Handle the FullCalendarEvent "creating" event.
     *
     * @param  \App\Models\FullCalendarEvent  $event
     * @return void
     */
    public function creating(FullCalendarEvent $event)
    {
        if (empty($event->status)) {
            $event->status = FullCalendarEventStatus::PENDING;
        }
    }
}
