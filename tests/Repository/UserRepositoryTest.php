<?php

namespace App\Tests\Repository;

use App\Entity\User;
use App\Exception\UserNotFoundException;
use App\Repository\UserRepository;
use App\Tests\AbstractTestRepository;
use App\Tests\MockUtils;

class UserRepositoryTest extends AbstractTestRepository
{
    private UserRepository $userRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userRepository = $this->getRepositoryForEntity(User::class);
    }

    public function testExistsByEmail()
    {
        // Create user
        $user = MockUtils::createUser();
        $this->em->persist($user);

        // Save
        $this->em->flush();

        // Comparing true with the actual returned value.
        $this->assertTrue($this->userRepository->existsByEmail($user->getEmail()));
    }

    public function testGetUser()
    {
        // Create user
        $user = MockUtils::createUser();
        $this->em->persist($user);

        // Save
        $this->em->flush();

        // Comparing the expected value with the actual returned value
        $this->assertEquals($user->getEmail(), $this->userRepository->getUser($user->getId())->getEmail());
    }

    public function testGetUserNotFound()
    {
        // Expect exception
        $this->expectException(UserNotFoundException::class);

        // Run method of user repository
        $this->userRepository->getUser(1);
    }
}
