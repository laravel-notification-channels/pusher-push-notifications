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

    public function __construct(Pusher $pusher)
    {
        $this->pusher = $pusher;
    }

    /**
     * Send the given notification.
     *
     * @param mixed                                  $notifiable
     * @param \Illuminate\Notifications\Notification $notification
     *
     * @return void
     * @throws \NotificationChannels\PusherPushNotifications\Exceptions\CouldNotSendNotification
     */
    public function send($notifiable, Notification $notification)
    {
        $interest = $notifiable->routeNotificationFor('PusherPushNotifications') ?: $this->interestName($notifiable);

        $shouldSendMessage = event(new SendingMessage($notifiable, $notification), [], true) !== false;

        if (!$shouldSendMessage) {
            return;
        }

        $response = $this->pusher->notify(
            $interest,
            $notification->toPushNotification($notifiable)->toArray(),
            true
        );

        if (!in_array($response['status'], [200, 202])) {
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
