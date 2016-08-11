<?php

namespace NotificationChannels\PusherPushNotifications\Test;

use Mockery;
use NotificationChannels\PusherPushNotifications\Channel;
use PHPUnit_Framework_TestCase;

class ChannelTest extends PHPUnit_Framework_TestCase
{
    /** @var Mockery\Mock */
    protected $pusher;

    /** @var NotificationChannels\PusherPushNotifications\Channel */
    protected $channel;

    /** @test */
    public function setUp()
    {
        $this->pusher = Mockery::mock(Pusher::class);

        $this->channel = new Channel($this->pusher);
    }

    /** @test */
    public function it_can_send_a_notification()
    {
        /* TODO: add a test */
    }
}
