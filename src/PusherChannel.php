<?php

namespace NotificationChannels\PusherPushNotifications;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Notifications\Events\NotificationFailed;
use Illuminate\Notifications\Notification;
use Pusher\PushNotifications\PushNotifications;
use Throwable;

class PusherChannel
{
    /**
     * @var PushNotifications
     */
    protected $beamsClient;

    /**
     * @var \Illuminate\Contracts\Events\Dispatcher
     */
    private $events;

    /**
     * @param PushNotifications $beamsClient
     * @param Dispatcher $events
     */
    public function __construct(PushNotifications $beamsClient, Dispatcher $events)
    {
        $this->beamsClient = $beamsClient;
        $this->events = $events;
    }

    /**
     * Send the given notification.
     *
     * @param mixed $notifiable
     * @param \Illuminate\Notifications\Notification $notification
     *
     * @return void
     */
    public function send($notifiable, Notification $notification)
    {
        $interest = $notifiable->routeNotificationFor('PusherPushNotifications')
            ?: $this->interestName($notifiable);

        try {
            $this->beamsClient->publishToInterests(
                is_array($interest) ? $interest : [$interest],
                $notification->toPushNotification($notifiable)->toArray()
            );
        } catch (Throwable $exception) {
            $this->events->dispatch(
                new NotificationFailed($notifiable, $notification, 'pusher-push-notifications')
            );
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
