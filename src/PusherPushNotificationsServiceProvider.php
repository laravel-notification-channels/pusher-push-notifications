<?php

namespace NotificationChannels\PusherPushNotifications;

use Illuminate\Support\ServiceProvider;
use Pusher\PushNotifications\PushNotifications;

class PusherPushNotificationsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        $this->app->when(PusherBeams::class)
            ->needs(PushNotifications::class)
            ->give(function () {
                $config = config('services.pusher');

                return new PushNotifications([
                    'instanceId' => $config['beams_instance_id'],
                    'secretKey' => $config['beams_secret_key'],
                ]);
            });
    }
}
