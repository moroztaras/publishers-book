<?php

namespace App\Tests\Manager;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Manager\RoleManager;
use App\Tests\AbstractTestCase;
use Doctrine\ORM\EntityManagerInterface;

class RoleManagerTest extends AbstractTestCase
{
    private UserRepository $userRepository;

    private EntityManagerInterface $em;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Create user
        $this->user = new User();
        // Mock and behavior for UserRepository
        $this->userRepository = $this->createMock(UserRepository::class);
        $this->userRepository
            ->expects($this->once())
            ->method('getUser')
            ->with(1)
            ->willReturn($this->user)
        ;
        // Mock and behavior for EntityManager
        $this->em = $this->createMock(EntityManagerInterface::class);
        $this->em->expects($this->once())->method('flush');
    }

    public function testGrantAdmin(): void
    {
        $this->createManager()->grantAdmin(1);
        $this->assertEquals([User::ROLE_ADMIN], $this->user->getRoles());
    }

    public function testGrantAuthor(): void
    {
        $this->createManager()->grantAuthor(1);
        $this->assertEquals([User::ROLE_AUTHOR], $this->user->getRoles());
    }

    // Helper for create manager
    private function createManager(): RoleManager
    {
        return new RoleManager($this->userRepository, $this->em);
    }
}
