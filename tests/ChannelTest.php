<?php

namespace NotificationChannels\PusherPushNotifications\Test;

use Exception;
use Illuminate\Events\Dispatcher;
use Illuminate\Notifications\Events\NotificationFailed;
use Illuminate\Notifications\Notifiable;
use Illuminate\Notifications\Notification;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use NotificationChannels\PusherPushNotifications\PusherChannel;
use NotificationChannels\PusherPushNotifications\PusherMessage;
use Pusher\PushNotifications\PushNotifications;

class ChannelTest extends MockeryTestCase
{
    public function setUp(): void
    {
        $this->pusher = Mockery::mock(PushNotifications::class);

        $this->events = Mockery::mock(Dispatcher::class);

        $this->channel = new PusherChannel($this->pusher, $this->events);

        $this->notification = new TestNotification;

        $this->notifiableInterest = new TestNotifiableInterest;
        $this->notifiableInterests = new TestNotifiableInterests;

        $this->notifiableUser = new TestNotifiableUser;
        $this->notifiableUsers = new TestNotifiableUsers;
    }

    /** @test */
    public function it_can_send_a_notification_to_interest(): void
    {
        $message = $this->notification->toPushNotification($this->notifiableInterest);

        $data = $message->toArray();

        $this->pusher->shouldReceive('publishToInterests')->once()->with(['interest_name'], $data);

        $this->channel->send($this->notifiableInterest, $this->notification);
    }

    /** @test */
    public function it_can_send_a_notification_to_interests(): void
    {
        $message = $this->notification->toPushNotification($this->notifiableInterests);

        $data = $message->toArray();

        $this->pusher->shouldReceive('publishToInterests')->once()->with([
            'interest_one', 'interest_two', 'interest_three',
        ], $data);

        $this->channel->send($this->notifiableInterests, $this->notification);
    }

    /** @test */
    public function it_fires_failure_event_on_interest_failure(): void
    {
        $message = $this->notification->toPushNotification($this->notifiableInterest);

        $data = $message->toArray();

        $this->pusher->shouldReceive('publishToInterests')->once()->with(['interest_name'], $data)->andThrow(new Exception('Something happened'));

        $this->events->shouldReceive('dispatch')->once()->with(Mockery::type(NotificationFailed::class));

        $this->channel->send($this->notifiableInterest, $this->notification);
    }

    /** @test */
    public function it_can_send_a_notification_to_user(): void
    {
        $message = $this->notification->toPushNotification($this->notifiableUser);

        $data = $message->toArray();

        $this->pusher->shouldReceive('publishToUsers')->once()->with(['user_1'], $data);

        $this->channel->send($this->notifiableUser, $this->notification);
    }

    /** @test */
    public function it_can_send_a_notification_to_users(): void
    {
        $message = $this->notification->toPushNotification($this->notifiableUsers);

        $data = $message->toArray();

        $this->pusher->shouldReceive('publishToUsers')->once()->with([
            'user_1', 'user_2', 'user_3',
        ], $data);

        $this->channel->send($this->notifiableUsers, $this->notification);
    }

    /** @test */
    public function it_fires_failure_event_on_user_failure(): void
    {
        $message = $this->notification->toPushNotification($this->notifiableUser);

        $data = $message->toArray();

        $this->pusher->shouldReceive('publishToUsers')->once()->with(['user_1'], $data)->andThrow(new Exception('Something happened'));

        $this->events->shouldReceive('dispatch')->once()->with(Mockery::type(NotificationFailed::class));

        $this->channel->send($this->notifiableUser, $this->notification);
    }
}

class TestNotifiableInterest
{
    use Notifiable;

    public function routeNotificationForPusherPushNotifications()
    {
        return 'interest_name';
    }
}

class TestNotifiableInterests
{
    use Notifiable;

    public function routeNotificationForPusherPushNotifications()
    {
        return ['interest_one', 'interest_two', 'interest_three'];
    }
}

class TestNotifiableUser
{
    use Notifiable;

    public $pushNotificationType = 'users';

    public function routeNotificationForPusherPushNotifications()
    {
        return 'user_1';
    }
}

class TestNotifiableUsers
{
    use Notifiable;

    public $pushNotificationType = 'users';

    public function routeNotificationForPusherPushNotifications()
    {
        return ['user_1', 'user_2', 'user_3'];
    }
}

class TestNotification extends Notification
{
    public function toPushNotification($notifiable)
    {
        return new PusherMessage();
    }
}
