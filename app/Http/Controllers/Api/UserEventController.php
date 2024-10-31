<?php

namespace App\Http\Controllers\Api;

use App\Events\Api\FullCalendarEventDeleted;
use App\Events\Api\FullCalendarEventUpdated;
use App\Events\Api\NewFullCalendarEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\GetUserEventByIdRequest;
use App\Http\Requests\Api\NewUserEventRequest;
use App\Http\Requests\Api\UpdateUserEventRequest;
use App\Http\Requests\Api\UserEventParamsRequest;
use App\Http\Resources\Api\FullCalendarEventResource;
use App\Repositories\FullCalendarEvent\FullCalendarEventInterface;
use App\Utils\Api\CommonUtil;
use Exception;
use Illuminate\Http\Response;

class UserEventController extends Controller
{
    protected $full_calendar_event;

    public function __construct(FullCalendarEventInterface $full_calendar_event) {
        $this->full_calendar_event = $full_calendar_event;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(UserEventParamsRequest $request)
    {
        $data = $request->validated();

        try {
            $user = $request->user();
            $user_events = $this->full_calendar_event->getEventsByUserId($user->id, $data);
            $user_events_collection = FullCalendarEventResource::collection($user_events);

            return CommonUtil::successResponse([
                'data' => [
                    'events' => $user_events_collection->toArray($request)
                ],
            ], __('full_calendar_events.list'));

        } catch (Exception $e) {
            return CommonUtil::errorResponse(__('messages.something_went_wrong'), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(NewUserEventRequest $request)
    {
        $data = $request->validated();

        try {

            $user_event = $this->full_calendar_event->createUserEvent($data);

            event(new NewFullCalendarEvent($user_event));

            return CommonUtil::successResponse([
                'data' => [
                    'event' => new FullCalendarEventResource($user_event)
                ],
            ], __('full_calendar_events.created_successfully'), Response::HTTP_CREATED);

        } catch (Exception $e) {
            return CommonUtil::errorResponse(__('messages.something_went_wrong'), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(GetUserEventByIdRequest $request)
    {
        $data = $request->validated();
        $event_id = $data['event'];

        try {
            $user = $request->user();
            $user_event = $this->full_calendar_event->getUserEventById($user->id, $event_id);

            if (!$user_event) {
                return CommonUtil::errorResponse(__('full_calendar_events.not_found'), Response::HTTP_NOT_FOUND);
            }

            return CommonUtil::successResponse([
                'data' => [
                    'event' => new FullCalendarEventResource($user_event)
                ],
            ], __('full_calendar_events.event'));

        } catch (Exception $e) {
            return CommonUtil::errorResponse(__('messages.something_went_wrong'), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserEventRequest $request)
    {
        $data = $request->validated();
        $event_id = $data['event'];
        unset($data['event']); // Quitar 'event' del array para no enviarlo a la base de datos

        try {
            $user = $request->user();
            $user_event = $this->full_calendar_event->updateUserEventById($user->id, $event_id, $data);

            if (!$user_event) {
                return CommonUtil::errorResponse(__('full_calendar_events.not_found'), Response::HTTP_NOT_FOUND);
            }

            event(new FullCalendarEventUpdated($user_event));

            return CommonUtil::successResponse([
                'data' => [
                    'event' => new FullCalendarEventResource($user_event)
                ],
            ], __('full_calendar_events.updated_successfully'));

        } catch (Exception $e) {
            return CommonUtil::errorResponse(__('messages.something_went_wrong'), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(GetUserEventByIdRequest $request)
    {
        $data = $request->validated();
        $event_id = $data['event'];

        try {

            $user = $request->user();
            $user_event = $this->full_calendar_event->deleteUserEventById($user->id, $event_id);

            if (!$user_event) {
                return CommonUtil::errorResponse(__('full_calendar_events.not_found'), Response::HTTP_NOT_FOUND);
            }

            event(new FullCalendarEventDeleted($user_event));

            return CommonUtil::successResponse([], __('full_calendar_events.deleted_successfully'));

        } catch (Exception $e) {
            return CommonUtil::errorResponse(__('messages.something_went_wrong'), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

    }
}
