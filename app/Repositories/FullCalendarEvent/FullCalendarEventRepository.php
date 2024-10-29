<?php

namespace App\Repositories\FullCalendarEvent;

use App\Models\FullCalendarEvent;
use App\Repositories\FullCalendarEvent\FullCalendarEventInterface;
use Illuminate\Support\Collection;

class FullCalendarEventRepository implements FullCalendarEventInterface
{
    /**
     * @param string $user_id
     * @param array $filters
     * @return Collection<FullCalendarEvent>
    */
    public function getEventsByUserId(string $user_id, array $filters = []): Collection
    {
        $query = FullCalendarEvent::where('user_id', $user_id);

        if (isset($filters['start_date']) && isset($filters['end_date'])) {
            $query->whereBetween('date', [$filters['start_date'], $filters['end_date']]);
        }

        return $query->get();
    }

    /**
     * @param array $data
     * @return FullCalendarEvent
    */
    public function createUserEvent(array $data): FullCalendarEvent
    {
        return FullCalendarEvent::create($data);
    }

    /**
     * @param string $user_id
     * @param string $event_id
     * @return FullCalendarEvent|null
    */
    public function getUserEventById(string $user_id, string $event_id): ?FullCalendarEvent
    {
        return FullCalendarEvent::where('id', $event_id)->where('user_id', $user_id)->first();
    }

    /**
     * @param string $user_id
     * @param string $event_id
     * @return bool
    */
    public function deleteUserEventById(string $user_id, string $event_id): bool
    {
        $user_event = $this->getUserEventById($user_id, $event_id);

        if($user_event) {
            $user_event->delete();
            return true;
        }

        return false;
    }

    /**
     * @param string $user_id
     * @param string $event_id
     * @param array $data
     * @return FullCalendarEvent|null
    */
    public function updateUserEventById(string $user_id, string $event_id, array $data): ?FullCalendarEvent
    {
        $user_event = $this->getUserEventById($user_id, $event_id);

        if($user_event) {
            $user_event->update($data);
            return $user_event;
        }

        return null;
    }
}
