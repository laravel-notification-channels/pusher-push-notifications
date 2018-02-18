<?php

namespace NotificationChannels\PusherPushNotifications;

use Illuminate\Events\Dispatcher;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Events\NotificationFailed;
use Pusher\PushNotifications\PushNotifications;

class PusherChannel
{
    /**
     * @var \Pusher\PushNotifications\PushNotifications $pusher
     */
    protected $pusher;

    /**
     * @var \Illuminate\Events\Dispatcher
     */
    private $events;

    /**
     * @param \Pusher\PushNotifications\PushNotifications $pusher
     * @param \Illuminate\Events\Dispatcher
     */
    public function __construct(PushNotifications $pusher, Dispatcher $events)
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
        
        if(is_string($interest)) {
            $interest = [$interest];
        }

        try {
            $response = $this->pusher->publish(
                $interest,
                $notification->toPushNotification($notifiable)->toArray()
            );
        }catch (\Exception $e) {
            $this->events->fire(
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
