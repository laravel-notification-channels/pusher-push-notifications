<?php

namespace NotificationChannels\PusherPushNotifications;

use Illuminate\Notifications\Notification;
use Pusher;

class PushNotificationsChannel
{
    /**
     * The Pusher instance.
     *
     * @var \Pusher
     */
    protected $pusher;

    /**
     * Create a new channel instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->pusher = new Pusher(
            config('broadcasting.connections.pusher.key'),
            config('broadcasting.connections.pusher.secret'),
            config('broadcasting.connections.pusher.app_id')
        );
    }

    /**
     * Send the given notification.
     *
     * @param  mixed $notifiable
     * @param  \Illuminate\Notifications\Notification $notification
     * @return void
     */
    public function send($notifiable, Notification $notification)
    {
        $this->pusher->notify(
            $notifiable->routeNotificationFor('PusherPushNotifications'),
            $notification->toPushNotification($notifiable)->toArray()
        );
    }
}
