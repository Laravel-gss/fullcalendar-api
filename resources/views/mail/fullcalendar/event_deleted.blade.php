<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('full_calendar_events.deleted_notification') }}</title>
    @include('mail.fullcalendar.partials.event_styles')
</head>
<body>
    <div class="container">

        <h1>{{ __('full_calendar_events.deleted_successfully') }}</h1>

        <div class="event-details">
            <p><strong>{{ __('full_calendar_events.name') }}:</strong> {{ $full_calendar_event->name }}</p>
            <p><strong>{{ __('full_calendar_events.date') }}:</strong> {{ $full_calendar_event->date }}</p>
        </div>

        @include('mail.fullcalendar.partials.event_footer')
    </div>
</body>
</html>
