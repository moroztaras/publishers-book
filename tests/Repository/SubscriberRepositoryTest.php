<?php

namespace App\Tests\Repository;

use App\Entity\Subscriber;
use App\Repository\SubscriberRepository;
use App\Tests\AbstractTestRepository;
use App\Tests\MockUtils;

class SubscriberRepositoryTest extends AbstractTestRepository
{
    private SubscriberRepository $subscriberRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->subscriberRepository = $this->getRepositoryForEntity(Subscriber::class);
    }

    public function testExistsByEmail()
    {
        $subscriber = MockUtils::createSubscriber();
        $this->em->flush();

        $this->assertFalse($this->subscriberRepository->existsByEmail($subscriber->getEmail()));
    }
}
