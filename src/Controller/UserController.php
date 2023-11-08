<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class UserController extends AbstractController
{
    #[Route(path: '/api/v1/user/profile', methods: ['GET'])]
    public function profile(#[CurrentUser] UserInterface $user): Response
    {
        return $this->json(['userProfile' => $user]);
    }
}
