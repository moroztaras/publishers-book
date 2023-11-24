<?php

namespace App\Controller;

use App\Manager\RoleManager;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;
use App\Model\ErrorResponse;

class AdminController extends AbstractController
{
    public function __construct(private RoleManager $roleManager)
    {
    }

    /**
     * @OA\Tag(name="Admin category API")
     * @OA\Response(
     *     response=200,
     *     description="Grants ROLE_AUTHOR to a user"
     * )
     * @OA\Response(
     *     response=404,
     *     description="User not found",
     *     @Model(type=ErrorResponse::class)
     * )
     */
    #[Route(path: '/api/v1/admin/grantAuthor/{userId}', methods: ['POST'])]
    public function grantAuthor(int $userId): Response
    {
        $this->roleManager->grantAuthor($userId);

        return $this->json(null);
    }
}
