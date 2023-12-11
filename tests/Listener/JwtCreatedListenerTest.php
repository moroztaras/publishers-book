<?php

namespace App\Tests\Listener;

use App\Listener\JwtCreatedListener;
use App\Tests\AbstractTestCase;
use App\Tests\MockUtils;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;

class JwtCreatedListenerTest extends AbstractTestCase
{
    public function testInvoke(): void
    {
        // Create user
        $user = MockUtils::createUser();
        $this->setEntityId($user, 123);

        // Create listener
        $listener = new JwtCreatedListener();
        // Create event
        $event = new JWTCreatedEvent(['flag' => true], $user, []);

        $listener($event);

        // Comparing the expected value with the actual returned event value
        $this->assertEquals(['flag' => true, 'id' => 123], $event->getData());
    }
}
