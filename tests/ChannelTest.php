<?php

namespace NotificationChannels\PusherPushNotifications\Test;

use Illuminate\Notifications\Notifiable;
use NotificationChannels\PusherPushNotifications\PusherChannel;
use Illuminate\Notifications\Notification;
use NotificationChannels\PusherPushNotifications\PusherMessage;
use PHPUnit_Framework_TestCase;
use Mockery;
use Pusher;

class ChannelTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->pusher = Mockery::mock(Pusher::class);

        $this->channel = new PusherChannel($this->pusher);

        $this->notification = new TestNotification;

        $this->notifiable = new TestNotifiable;
    }

    public function tearDown()
    {
        Mockery::close();

        parent::tearDown();
    }

    /** @test */
    public function it_can_send_a_notification()
    {
        $message = $this->notification->toPushNotification($this->notifiable);

        $data = $message->toArray();

        $this->pusher->shouldReceive('notify')->with('interest_name', $data, true)->andReturn(['status' => 202]);

        $this->channel->send($this->notifiable, $this->notification);
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
