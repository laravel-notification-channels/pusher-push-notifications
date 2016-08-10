<?php

namespace NotificationChannels\PusherPushNotifications;

use Illuminate\Support\ServiceProvider;
use Pusher;

class PusherPushNotificationsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        $this->app->when(Channel::class)
            ->needs(Pusher::class)
            ->give(function () {
                $pusherConfig = config('broadcasting.connections.pusher');

                return new Pusher(
                    $pusherConfig['key'],
                    $pusherConfig['secret'],
                    $pusherConfig['app_id']
                );
            });
    }

    /**
     * Register the application services.
     */
    public function register()
    {
    }
}
