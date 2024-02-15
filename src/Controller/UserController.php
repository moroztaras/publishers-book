<?php

namespace App\Controller;

use App\Manager\UserManager;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class UserController extends AbstractController
{
    public function __construct(private readonly UserManager $userManager)
    {
    }

    // TODO Need add ' attachables: [new Model(type: UserDetails::class)]' to response 200
    #[Route(path: '/api/v1/user/profile', methods: ['GET'])]
    #[OA\Tag(name: 'User API')]
    #[OA\Response(response: 200, description: 'Get user profile')]
    public function profile(#[CurrentUser] UserInterface $user): Response
    {
        return $this->json($this->userManager->getUserProfile($user));
    }
}
