<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('full_calendar_events.new_notification') }}</title>
    @include('mail.fullcalendar.partials.event_styles')
</head>
<body>
    <div class="container">
        @include('mail.fullcalendar.partials.event_body', ['title' => __('full_calendar_events.created_successfully')])
        @include('mail.fullcalendar.partials.event_footer')
    </div>
</body>
</html>
