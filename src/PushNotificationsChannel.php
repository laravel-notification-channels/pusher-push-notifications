<?php

namespace NotificationChannels\PusherPushNotifications;

use NotificationChannels\PusherPushNotifications\Events\MessageSending;
use NotificationChannels\PusherPushNotifications\Events\MessageSent;
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
        $interest = $notifiable->routeNotificationFor('PusherPushNotifications')
            ?: $this->interestName($notifiable);

        if (event(new MessageSending($notifiable, $notification), [], true) === false) {
            return;
        }

        $this->pusher->notify(
            $interest,
            $notification->toPushNotification($notifiable)->toArray()
        );

        event(
            new MessageSent($notifiable, $notification)
        );
    }

    /**
     * Get the interest name for the notifiable.
     *
     * @return string
     */
    protected function interestName($notifiable)
    {
        $class = str_replace('\\', '.', get_class($notifiable));

        return $class.'.'.$notifiable->getKey();
    }
}
