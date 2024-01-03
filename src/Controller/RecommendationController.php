<?php

namespace App\Controller;

use App\Manager\RecommendationManager;
use App\Model\RecommendedBookListResponse;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RecommendationController extends AbstractController
{
    public function __construct(private readonly RecommendationManager $recommendationManager)
    {
    }

    #[Route(path: '/api/v1/book/{id}/recommendations', methods: ['GET'])]
    #[OA\Tag(name: 'Recommendation API')]
    #[OA\Response(response: 200, description: 'Returns recommendations for the book', attachables: [new Model(type: RecommendedBookListResponse::class)])]
    public function recommendationsByBookId(int $id): Response
    {
        return $this->json($this->recommendationManager->getRecommendationsByBookId($id));
    }
}
