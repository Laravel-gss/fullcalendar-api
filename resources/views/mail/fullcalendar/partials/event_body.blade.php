
<h1>{{ $title }}</h1>


@if (!empty($full_calendar_event))
    <div class="event-details">
        <p><strong>{{ __('full_calendar_events.name') }}:</strong> {{ $full_calendar_event->name }}</p>
        <p><strong>{{ __('full_calendar_events.date') }}:</strong> {{ $full_calendar_event->date }}</p>
        <p><strong>{{ __('full_calendar_events.description') }}:</strong> {{ $full_calendar_event->description }}</p>
        <p><strong>{{ __('full_calendar_events.status') }}:</strong> <span class="badge badge-{{$full_calendar_event->status}}">{{ $full_calendar_event->status }}</span></p>
    </div>
@endif
