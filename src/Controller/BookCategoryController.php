<?php

namespace App\Controller;

use App\Manager\BookCategoryManager;
use App\Model\BookCategoryListResponse;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BookCategoryController extends AbstractController
{
    public function __construct(private BookCategoryManager $bookCategoryManager)
    {
    }

    /**
     * @OA\Tag(name="Book category API")
     *
     * @OA\Response(
     *     response=200,
     *     description="Returns book categories",
     *
     *     @Model(type=BookCategoryListResponse::class)
     * )
     */
    #[Route(path: '/api/v1/book/categories', methods: ['GET'])]
    public function categories(): Response
    {
        // TODO It's for testing
        //        throw new \RuntimeException('testing');
        return $this->json($this->bookCategoryManager->getCategories());
    }
}
