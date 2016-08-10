<?php

namespace NotificationChannels\PusherPushNotifications;

use Illuminate\Notifications\Notification;
use NotificationChannels\PusherPushNotifications\Events\MessageWasSent;
use NotificationChannels\PusherPushNotifications\Events\SendingMessage;
use NotificationChannels\PusherPushNotifications\Exceptions\CouldNotSendNotification;
use Pusher;

class Channel
{
    /**
     * @var \Pusher
     */
    protected $pusher;

    public function __construct()
    {
        $pusherConfig = config('broadcasting.connections.pusher');

        $this->pusher = new Pusher(
            $pusherConfig['key'],
            $pusherConfig['secret'],
            $pusherConfig['app_id']
        );
    }

    /**
     * Send the given notification.
     *
     * @param mixed $notifiable
     * @param \Illuminate\Notifications\Notification $notification
     */
    public function send($notifiable, Notification $notification)
    {
        $interest = $notifiable->routeNotificationFor('PusherPushNotifications') ?: $this->interestName($notifiable);

        $shouldSendMessage = event(new SendingMessage($notifiable, $notification), [], true) !== false;

        if (! $shouldSendMessage) {
            return;
        }

        $response = $this->pusher->notify(
            $interest,
            $notification->toPushNotification($notifiable)->toArray(),
            true
        );

        if (! in_array($response['status'], [200, 202])) {
            throw CouldNotSendNotification::pusherRespondedWithAnError($response);
        }

        event(new MessageWasSent($notifiable, $notification));
    }

    /**
     * Get the interest name for the notifiable.
     *
     * @param $notifiable
     *
     * @return string
     */
    protected function interestName($notifiable)
    {
        $class = str_replace('\\', '.', get_class($notifiable));

        return $class.'.'.$notifiable->getKey();
    }
}
