<?php

namespace App\Repositories\FullCalendarEvent;

use App\Models\FullCalendarEvent;
use Illuminate\Support\Collection;

interface FullCalendarEventInterface
{
    /**
     * @param string $user_id
    * @return Collection<FullCalendarEvent>
    */
    public function getEventsByUserId(string $user_id, array $filters = []): Collection;

    /**
    * @param array $data
    * @return FullCalendarEvent
    */
    public function createUserEvent(array $data): FullCalendarEvent;

    /**
    * @param string $user_id
    * @param string $event_id
    * @return FullCalendarEvent|null
    */
    public function getUserEventById(string $user_id, string $event_id): ?FullCalendarEvent;

    /**
    * @param string $user_id
    * @param string $event_id
    * @return bool
    */
    public function deleteUserEventById(string $user_id, string $event_id): bool;

    /**
    * @param string $user_id
    * @param string $event_id
    * @param array $data
    * @return FullCalendarEvent|null
    */
    public function updateUserEventById(string $user_id, string $event_id, array $data): ?FullCalendarEvent;

}
