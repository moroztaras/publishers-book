<?php

namespace App\Controller;

use App\Manager\BookCategoryManager;
use App\Manager\RoleManager;
use App\Model\ErrorResponse;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    public function __construct(
        private RoleManager $roleManager,
        private BookCategoryManager $bookCategoryManager
    ) {
    }

    /**
     * @OA\Tag(name="Admin API")
     *
     * @OA\Response(
     *     response=200,
     *     description="Grants ROLE_AUTHOR to a user"
     * )
     * @OA\Response(
     *     response=404,
     *     description="User not found",
     *
     *     @Model(type=ErrorResponse::class)
     * )
     */
    #[Route(path: '/api/v1/admin/grantAuthor/{userId}', methods: ['POST'])]
    public function grantAuthor(int $userId): Response
    {
        $this->roleManager->grantAuthor($userId);

        return $this->json(null);
    }

    /**
     * @OA\Tag(name="Admin category API")
     * @OA\Response(
     *     response=200,
     *     description="Delete a book category"
     * )
     * @OA\Response(
     *     response=404,
     *     description="Book category not found",
     *     @Model(type=ErrorResponse::class)
     * )
     * @OA\Response(
     *     response=400,
     *     description="Book category still contains books",
     *     @Model(type=ErrorResponse::class)
     * )
     */
    #[Route(path: '/api/v1/admin/book-category/{id}', methods: ['DELETE'])]
    public function deleteCategory(int $id): Response
    {
        $this->bookCategoryManager->deleteCategory($id);

        return $this->json(null);
    }
}
