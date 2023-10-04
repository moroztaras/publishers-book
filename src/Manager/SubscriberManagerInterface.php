<?php

namespace App\Manager;

use App\Model\SubscriberRequest;

interface SubscriberManagerInterface
{
    public function subscribe(SubscriberRequest $request): void;
}
