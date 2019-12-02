<?php

namespace NotificationChannels\PusherPushNotifications;

use Pusher\Pusher;
use Illuminate\Events\Dispatcher;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Events\NotificationFailed;

class PusherChannel
{
    /**
     * @var Pusher
     */
    protected $pusher;

    /**
     * @var \Illuminate\Events\Dispatcher
     */
    private $events;

    /**
     * @param Pusher $pusher
     */
    public function __construct(Pusher $pusher, Dispatcher $events)
    {
        $this->pusher = $pusher;
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

        $response = $this->pusher->notify(
            is_array($interest) ? $interest : [$interest],
            $notification->toPushNotification($notifiable)->toArray(),
            true
        );

        if (! in_array($response['status'], [200, 202])) {
            $this->events->dispatch(
                new NotificationFailed($notifiable, $notification, 'pusher-push-notifications', $response)
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
