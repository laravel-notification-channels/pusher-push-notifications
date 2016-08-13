<?php

namespace NotificationChannels\PusherPushNotifications;

use NotificationChannels\PusherPushNotifications\Exceptions\CouldNotSendNotification;
use NotificationChannels\PusherPushNotifications\Events\MessageWasSent;
use NotificationChannels\PusherPushNotifications\Events\SendingMessage;
use Illuminate\Notifications\Notification;
use Pusher;

class PusherChannel
{
    /** @var Pusher */
    protected $pusher;

    /**
     * @param \Pusher $pusher
     */
    public function __construct(Pusher $pusher)
    {
        $this->pusher = $pusher;
    }

    /**
     * Send the given notification.
     *
     * @param mixed $notifiable
     * @param \Illuminate\Notifications\Notification $notification
     *
     * @return void
     * @throws \NotificationChannels\PusherPushNotifications\Exceptions\CouldNotSendNotification
     */
    public function send($notifiable, Notification $notification)
    {
        $interest = $notifiable->routeNotificationFor('PusherPushNotifications')
            ?: $this->interestName($notifiable);

        $response = $this->pusher->notify(
            $interest,
            $notification->toPushNotification($notifiable)->toArray(),
            true
        );

        if (! in_array($response['status'], [200, 202])) {
            throw CouldNotSendNotification::pusherRespondedWithAnError($response);
        }
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
