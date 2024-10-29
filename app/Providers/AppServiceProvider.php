<?php

namespace App\Providers;

use App\Models\FullCalendarEvent;
use App\Observers\Api\FullCalendarEventObserver;
use App\Repositories\FullCalendarEvent\FullCalendarEventInterface;
use App\Repositories\FullCalendarEvent\FullCalendarEventRepository;
use App\Repositories\User\UserInterface;
use App\Repositories\User\UserRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        FullCalendarEvent::observe(FullCalendarEventObserver::class);

        $bindings = [
            UserInterface::class => UserRepository::class,
            FullCalendarEventInterface::class => FullCalendarEventRepository::class
        ];

        foreach ($bindings as $abstract => $concrete) {
            $this->app->bind($abstract, $concrete);
        }
    }
}
