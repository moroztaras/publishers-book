<?php

namespace App\Controller;

use App\Attribute\RequestBody;
use App\Manager\BookCategoryManager;
use App\Manager\RoleManager;
use App\Model\IdResponse;
use App\Model\BookCategoryUpdateRequest;
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
     * @OA\Tag(name="Admin API")
     * @OA\Response(
     *     response=200,
     *     description="Create a new category",
     *     @Model(type=IdResponse::class)
     * )
     * @OA\Response(
     *     response="400",
     *     description="Validation failed",
     *     @Model(type=ErrorResponse::class)
     * )
     * @OA\Response(
     *     response=409,
     *     description="Book category already exists",
     *     @Model(type=ErrorResponse::class)
     * )
     * @OA\RequestBody(@Model(type=BookCategoryUpdateRequest::class))
     */
    #[Route(path: '/api/v1/admin/bookCategory', methods: ['POST'])]
    public function createCategory(#[RequestBody] BookCategoryUpdateRequest $request): Response
    {
        return $this->json($this->bookCategoryManager->createCategory($request));
    }

    /**
     * @OA\Tag(name="Admin API")
     * @OA\Response(
     *     response=200,
     *     description="Update a book category",
     *     @Model(type=IdResponse::class)
     * )
     * @OA\Response(
     *     response="400",
     *     description="Validation failed",
     *     @Model(type=ErrorResponse::class)
     * )
     * @OA\Response(
     *     response=409,
     *     description="Book category already exists",
     *     @Model(type=ErrorResponse::class)
     * )
     * @OA\RequestBody(@Model(type=BookCategoryUpdateRequest::class))
     */
    #[Route(path: '/api/v1/admin/bookCategory/{id}', methods: ['PUT'])]
    public function updateCategory(int $id, #[RequestBody] BookCategoryUpdateRequest $request): Response
    {
        $this->bookCategoryManager->updateCategory($id, $request);

        return $this->json(null);
    }

    /**
     * @OA\Tag(name="Admin API")
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
    #[Route(path: '/api/v1/admin/bookCategory/{id}', methods: ['DELETE'])]
    public function deleteCategory(int $id): Response
    {
        $this->bookCategoryManager->deleteCategory($id);

        return $this->json(null);
    }
}
