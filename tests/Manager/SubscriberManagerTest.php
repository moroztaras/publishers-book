<?php

namespace App\Tests\Manager;

use App\Entity\Subscriber;
use App\Exception\SubscriberAlreadyExistsException;
use App\Manager\SubscriberManager;
use App\Model\SubscriberRequest;
use App\Repository\SubscriberRepository;
use App\Tests\AbstractTestCase;

class SubscriberManagerTest extends AbstractTestCase
{
    private SubscriberRepository $repository;

    private const EMAIL = 'test@test.com';

    protected function setUp(): void
    {
        parent::setUp();

        // Set mock
        $this->repository = $this->createMock(SubscriberRepository::class);
    }

    public function testSubscribeAlreadyExists(): void
    {
        // Expect exception
        $this->expectException(SubscriberAlreadyExistsException::class);

        // Set behavior for existsByEmail method
        $this->repository->expects($this->once())
            ->method('existsByEmail')
            ->with(self::EMAIL)
            ->willReturn(true);

        $request = new SubscriberRequest();
        $request->setEmail(self::EMAIL);

        // Run method subscribe
        (new SubscriberManager($this->repository))->subscribe($request);
    }

    public function testSubscribe(): void
    {
        // Set behavior for existsByEmail method
        $this->repository->expects($this->once())
            ->method('existsByEmail')
            ->with(self::EMAIL)
            ->willReturn(false);

        $expectedSubscriber = new Subscriber();
        $expectedSubscriber->setEmail(self::EMAIL);

        // Create request
        $request = new SubscriberRequest();
        $request->setEmail(self::EMAIL);

        // Set behavior for method 'saveAndCommit'
        $this->repository->expects($this->once())
            ->method('saveAndCommit')
            ->with($expectedSubscriber);

        // Create subscribe manager and run method
        (new SubscriberManager($this->repository))->subscribe($request);
    }
}
