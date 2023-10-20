<?php

namespace App\Manager;

use App\Entity\Subscriber;
use App\Exception\SubscriberAlreadyExistsException;
use App\Model\SubscriberRequest;
use App\Repository\SubscriberRepository;
use Doctrine\ORM\EntityManagerInterface;

class SubscriberManager implements SubscriberManagerInterface
{
    public function __construct(
        private SubscriberRepository $subscriberRepository,
        private EntityManagerInterface $em
    ) {
    }

    // Create new subscribe
    public function subscribe(SubscriberRequest $request): void
    {
        // Checking for existence email
        if ($this->subscriberRepository->existsByEmail($request->getEmail())) {
            throw new SubscriberAlreadyExistsException();
        }

        $subscriber = new Subscriber();
        $subscriber->setEmail($request->getEmail());

        $this->em->persist($subscriber);
        $this->em->flush();
    }
}
