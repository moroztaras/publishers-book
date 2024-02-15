<?php

namespace App\Mapper;

use App\Entity\User;
use App\Model\UserDetails;

class UserMapper
{
    public static function map(User $user, UserDetails $model): void
    {
        $model
            ->setFirstName($user->getFirstName())
            ->setLastName($user->getLastName())
            ->setEmail($user->getEmail())
        ;
    }
}
