<?php

namespace App\Manager;

use App\Entity\User;
use App\Repository\UserRepository;

class RoleManager
{
    public function __construct(private UserRepository $userRepository)
    {
    }

    public function grantAdmin(int $userId): void
    {
        $this->grantRole($userId, User::ROLE_ADMIN);
    }

    public function grantAuthor(int $userId): void
    {
        $this->grantRole($userId, User::ROLE_AUTHOR);
    }

    // Assigning a role to a user
    private function grantRole(int $userId, string $role): void
    {
        $user = $this->userRepository->getUser($userId);

        $user->setRoles([$role]);

        $this->userRepository->commit();
    }
}
