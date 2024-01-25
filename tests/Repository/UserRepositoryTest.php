<?php

namespace App\Tests\Repository;

use App\Entity\User;
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
}
