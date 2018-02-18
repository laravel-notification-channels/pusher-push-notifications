<?php

namespace NotificationChannels\PusherPushNotifications;

use Illuminate\Support\ServiceProvider;
use Illuminate\Broadcasting\BroadcastManager;
use Pusher\PushNotifications\PushNotifications;

class PusherPushNotificationsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        $this->app->when(PusherChannel::class)
            ->needs(PushNotifications::class)
            ->give(function() {
                $config = config('broadcasting.connections.pusher');
                return new PushNotifications([
                    "instanceId" => $config['push_notification_instance_id'],
                    "secretKey" => $config['push_notification_secret'],
                ]);
            });
    }
}
