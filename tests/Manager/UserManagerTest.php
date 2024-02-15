<?php

namespace App\Tests\Manager;

use App\Entity\User;
use App\Manager\UserManager;
use App\Model\UserDetails;
use App\Tests\AbstractTestCase;
use App\Tests\MockUtils;

class UserManagerTest extends AbstractTestCase
{
    public function testGetUserProfile()
    {
        // Create user
        $user = MockUtils::createUser();
        $this->setEntityId($user, 1);

        // Expected UserDetails model
        $expected = (new UserDetails())
            ->setEmail('test@localhost.local')
            ->setFirstName('Vasya')
            ->setLastName('Testerov')
            ->setRoles((array) User::ROLE_AUTHOR)
        ;

        // Comparing the expected value with the actual returned value.
        $this->assertEquals($expected, (new UserManager())->getUserProfile($user));
    }
}
