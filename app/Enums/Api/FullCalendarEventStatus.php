<?php

namespace App\Enums\Api;

enum FullCalendarEventStatus: string
{
    case SUCCESS    = 'success';
    case PENDING    = 'pending';
    case CANCELLED  = 'cancelled';
}
