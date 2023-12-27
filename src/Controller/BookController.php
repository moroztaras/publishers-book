<?php

namespace App\Controller;

use App\Manager\BookManager;
use App\Model\BookDetails;
use App\Model\BookListResponse;
use App\Model\ErrorResponse;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BookController extends AbstractController
{
    public function __construct(private BookManager $bookManager)
    {
    }

    //TODO Need add @Model(type=BookListResponse::class) to response 200
    /**
     * @OA\Tag(name="Book API")
     *
     * @OA\Response(
     *     response=200,
     *     description="Returns published books inside a category",
     * )
     *
     * @OA\Response(
     *     response=404,
     *     description="Book category not found",
     *
     *     @Model(type=ErrorResponse::class)
     * )
     */
    #[Route(path: '/api/v1/category/{id}/books', methods: 'GET')]
    public function booksByCategory(int $id): Response
    {
        return $this->json($this->bookManager->getBooksByCategory($id));
    }

    // TODO Need add @Model(type=BookDetails::class) to response 200
    /**
     * @OA\Tag(name="Book API")
     *
     * @OA\Response(
     *     response=200,
     *     description="Returns published book detail information",
     * )
     *
     * @OA\Response(
     *     response=404,
     *     description="Book not found",
     *
     *     @Model(type=ErrorResponse::class)
     * )
     */
    #[Route(path: '/api/v1/book/{id}', methods: ['GET'])]
    public function bookById(int $id): Response
    {
        return $this->json($this->bookManager->getBookById($id));
    }
}
