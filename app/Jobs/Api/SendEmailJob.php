<?php

namespace App\Jobs\Api;

use App\Models\FullCalendarEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class SendEmailJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        protected FullCalendarEvent $event,
        protected string $mail_class
    )
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $user = $this->event->user;
        Mail::to($user->email)->send(new $this->mail_class($this->event));
    }
}
