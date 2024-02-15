<?php

namespace App\Manager;

use App\Entity\User;
use App\Mapper\UserMapper;
use App\Model\UserDetails;

class UserManager
{
    public function getUserProfile(User $user): UserDetails
    {
        $userDetails = (new UserDetails())
            ->setFirstName($user->getFirstName())
            ->setLastName($user->getLastName())
            ->setEmail($user->getEmail())
            ->setRoles($user->getRoles());

        UserMapper::map($user, $userDetails);

        return $userDetails;
    }
}
