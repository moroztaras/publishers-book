<?php

namespace App\Controller;

use App\Manager\BookCategoryManager;
use App\Model\BookCategoryListResponse;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BookCategoryController extends AbstractController
{
    public function __construct(private readonly BookCategoryManager $bookCategoryManager)
    {
    }

    #[Route(path: '/api/v1/book/categories', methods: ['GET'])]
    #[OA\Tag(name: 'Book category API')]
    #[OA\Response(response: 200, description: 'Returns book categories', attachables: [new Model(type: BookCategoryListResponse::class)])]
    public function categories(): Response
    {
        return $this->json($this->bookCategoryManager->getCategories());
    }
}
