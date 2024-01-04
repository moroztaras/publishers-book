<?php

namespace App\Manager;

use App\Entity\Subscriber;
use App\Exception\SubscriberAlreadyExistsException;
use App\Model\SubscriberRequest;
use App\Repository\SubscriberRepository;

class SubscriberManager implements SubscriberManagerInterface
{
    public function __construct(private readonly SubscriberRepository $subscriberRepository)
    {
    }

    // Create new subscribe
    public function subscribe(SubscriberRequest $request): void
    {
        // Checking for existence email
        if ($this->subscriberRepository->existsByEmail($request->getEmail())) {
            throw new SubscriberAlreadyExistsException();
        }

        $this->subscriberRepository->saveAndCommit((new Subscriber())->setEmail($request->getEmail()));
    }
}
