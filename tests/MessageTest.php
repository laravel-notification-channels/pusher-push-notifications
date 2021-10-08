<?php

namespace NotificationChannels\PusherPushNotifications\Test;

use Illuminate\Support\Arr;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use NotificationChannels\PusherPushNotifications\Exceptions\CouldNotCreateMessage;
use NotificationChannels\PusherPushNotifications\PusherMessage;

class MessageTest extends MockeryTestCase
{
    /** @var \NotificationChannels\PusherPushNotifications\PusherMessage */
    protected $message;

    public function setUp(): void
    {
        parent::setUp();

        $this->message = new PusherMessage();
    }

    /** @test */
    public function it_can_accept_a_message_when_constructing_a_message()
    {
        $message = new PusherMessage('myMessage');

        $this->assertEquals('myMessage', Arr::get($message->toiOS(), 'apns.aps.alert.body'));
    }

    /** @test */
    public function it_provides_a_create_method()
    {
        $message = PusherMessage::create('myMessage');

        $this->assertEquals('myMessage', Arr::get($message->toiOS(), 'apns.aps.alert.body'));
    }

    /** @test */
    public function by_default_it_will_send_a_message_to_ios()
    {
        $this->assertTrue(Arr::has($this->message->toArray(), 'apns'));
        $this->assertFalse(Arr::has($this->message->toArray(), 'fcm'));
    }

    /** @test */
    public function it_can_send_a_message_to_the_right_platform()
    {
        $this->message->ios();

        $this->assertTrue(Arr::has($this->message->toArray(), 'apns'));
        $this->assertFalse(Arr::has($this->message->toArray(), 'fcm'));

        $this->message->android();

        $this->assertTrue(Arr::has($this->message->toArray(), 'fcm'));
        $this->assertFalse(Arr::has($this->message->toArray(), 'apns'));
    }

    /** @test */
    public function it_sets_a_default_sound()
    {
        $this->assertEquals('default', Arr::get($this->message->toArray(), 'apns.aps.sound'));
    }

    /** @test */
    public function it_can_set_the_title()
    {
        $this->message->title('myTitle');

        $this->assertEquals('myTitle', Arr::get($this->message->toiOS(), 'apns.aps.alert.title'));

        $this->assertEquals('myTitle', Arr::get($this->message->toAndroid(), 'fcm.notification.title'));
    }

    /** @test */
    public function it_can_set_the_body()
    {
        $this->message->body('myBody');

        $this->assertEquals('myBody', Arr::get($this->message->toiOS(), 'apns.aps.alert.body'));

        $this->assertEquals('myBody', Arr::get($this->message->toAndroid(), 'fcm.notification.body'));
    }

    /** @test */
    public function it_can_set_the_sound()
    {
        $this->message->sound('mySound');

        $this->assertEquals('mySound', Arr::get($this->message->toiOS(), 'apns.aps.sound'));

        $this->assertEquals('mySound', Arr::get($this->message->toAndroid(), 'fcm.notification.sound'));
    }

    /** @test */
    public function it_can_set_the_badge()
    {
        $this->message->badge(5);

        $this->assertEquals(5, Arr::get($this->message->toiOS(), 'apns.aps.badge'));
    }

    /** @test */
    public function it_can_set_the_icon()
    {
        $this->message->icon('myIcon');

        $this->assertEquals('myIcon', Arr::get($this->message->toAndroid(), 'fcm.notification.icon'));
    }

    /** @test */
    public function it_will_throw_an_exception_when_an_unsupported_platform_is_used()
    {
        $this->expectException(CouldNotCreateMessage::class);

        $this->message->platform('bla bla');
    }

    /** @test */
    public function it_can_send_message_to_multiple_platforms()
    {
        $this->message->ios()->withAndroid(new PusherMessage());

        $this->assertTrue(Arr::has($this->message->toArray(), 'apns'));
        $this->assertTrue(Arr::has($this->message->toArray(), 'fcm'));
    }

    /** @test */
    public function it_has_no_meta_by_default()
    {
        $this->message;
        $this->assertFalse(Arr::has($this->message->toArray(), 'apns.data'));
        $this->assertFalse(Arr::has($this->message->toArray(), 'apns.data'));
    }

    /** @test */
    public function it_can_add_meta()
    {
        $this->message->meta(['foo' => 'bar']);
        $this->assertEquals(['foo' => 'bar'], Arr::get($this->message->toArray(), 'apns.data'));

        $this->message->android();
        $this->assertEquals(['foo' => 'bar'], Arr::get($this->message->toArray(), 'fcm.data'));
    }
}
