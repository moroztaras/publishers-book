<?php

namespace App\Controller;

use App\Attribute\RequestBody;
use App\Model\Author\CreateBookChapterRequest;
use App\Model\ErrorResponse;
use App\Model\IdResponse;
use App\Security\Voter\AuthorBookVoter;
use App\Manager\AuthorBookChapterManager;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AuthorBookChapterController extends AbstractController
{
    public function __construct(private AuthorBookChapterManager $bookChapterManager)
    {
    }

    /**
     * @OA\Tag(name="Author book chapter API")
     * @OA\Response(
     *     response=200,
     *     description="Create a book chapter",
     *     @Model(type=IdResponse::class)
     * )
     * @OA\Response(
     *     response="400",
     *     description="Validation failed",
     *     @Model(type=ErrorResponse::class)
     * )
     * @OA\RequestBody(@Model(type=CreateBookChapterRequest::class))
     */
    #[Route(path: '/api/v1/author/book/{bookId}/chapter', methods: ['POST'])]
    #[IsGranted(AuthorBookVoter::IS_AUTHOR, subject: 'bookId')]
    public function createBookChapter(#[RequestBody] CreateBookChapterRequest $request, int $bookId): Response
    {
        return $this->json($this->bookChapterManager->createChapter($request, $bookId));
    }
}
