<?php

namespace NotificationChannels\PusherPushNotifications\Test;

use Exception;
use Illuminate\Events\Dispatcher;
use Illuminate\Notifications\Events\NotificationFailed;
use Illuminate\Notifications\Notifiable;
use Illuminate\Notifications\Notification;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use NotificationChannels\PusherPushNotifications\PusherBeams;
use NotificationChannels\PusherPushNotifications\PusherMessage;
use Pusher\PushNotifications\PushNotifications;

class BeamsTest extends MockeryTestCase
{
    public function setUp(): void
    {
        $this->pusher = Mockery::mock(PushNotifications::class);

        $this->events = Mockery::mock(Dispatcher::class);

        $this->beams = new PusherBeams($this->pusher, $this->events);

        $this->notification = new TestNotification;

        $this->notifiable = new TestNotifiable;
    }

    /** @test */
    public function it_can_send_a_notification()
    {
        $message = $this->notification->toPushNotification($this->notifiable);

        $data = $message->toArray();

        $this->pusher->shouldReceive('publishToInterests')->once()->with(['interest_name'], $data);

        $this->beams->send($this->notifiable, $this->notification);
    }

    /** @test */
    public function it_fires_failure_event_on_failure()
    {
        $message = $this->notification->toPushNotification($this->notifiable);

        $data = $message->toArray();

        $this->pusher->shouldReceive('publishToInterests')->once()->with(['interest_name'], $data)->andThrow(new Exception('Something happened'));

        $this->events->shouldReceive('dispatch')->once()->with(Mockery::type(NotificationFailed::class));

        $this->beams->send($this->notifiable, $this->notification);
    }
}

class TestNotifiable
{
    use Notifiable;

    public function routeNotificationForPusherPushNotifications()
    {
        return 'interest_name';
    }
}

class TestNotification extends Notification
{
    public function toPushNotification($notifiable)
    {
        return new PusherMessage();
    }
}
