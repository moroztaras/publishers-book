<?php

namespace App\Controller;

use App\Attribute\RequestBody;
use App\Manager\AuthorBookChapterManager;
use App\Model\Author\CreateBookChapterRequest;
use App\Model\Author\UpdateBookChapterRequest;
use App\Model\Author\UpdateBookChapterSortRequest;
use App\Model\BookChapterTreeResponse;
use App\Model\ErrorResponse;
use App\Model\IdResponse;
use App\Security\Voter\AuthorBookVoter;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AuthorBookChapterController extends AbstractController
{
    public function __construct(private readonly AuthorBookChapterManager $bookChapterManager)
    {
    }

    /**
     * @OA\Tag(name="Author book chapter API")
     *
     * @OA\Response(
     *     response=200,
     *     description="Get book chapters as tree",
     *
     *     @Model(type=BookChapterTreeResponse::class)
     * )
     */
    #[Route(path: '/api/v1/author/book/{bookId}/chapters', methods: ['GET'])]
    #[IsGranted(AuthorBookVoter::IS_AUTHOR, subject: 'bookId')]
    public function chapters(int $bookId): Response
    {
        return $this->json($this->bookChapterManager->getChaptersTree($bookId));
    }

    /**
     * @OA\Tag(name="Author book chapter API")
     *
     * @OA\Response(
     *     response=200,
     *     description="Create a book chapter",
     *
     *     @Model(type=IdResponse::class)
     * )
     *
     * @OA\Response(
     *     response="400",
     *     description="Validation failed",
     *
     *     @Model(type=ErrorResponse::class)
     * )
     *
     * @OA\RequestBody(@Model(type=CreateBookChapterRequest::class))
     */
    #[Route(path: '/api/v1/author/book/{bookId}/chapter', methods: ['POST'])]
    #[IsGranted(AuthorBookVoter::IS_AUTHOR, subject: 'bookId')]
    public function createBookChapter(#[RequestBody] CreateBookChapterRequest $request, int $bookId): Response
    {
        return $this->json($this->bookChapterManager->createChapter($request, $bookId));
    }

    /**
     * @OA\Tag(name="Author book chapter API")
     *
     * @OA\Response(
     *     response=200,
     *     description="Update a book chapter"
     * )
     * @OA\Response(
     *     response=404,
     *     description="Book chapter not found",
     *
     *     @Model(type=ErrorResponse::class)
     * )
     *
     * @OA\Response(
     *     response="400",
     *     description="Validation failed",
     *
     *     @Model(type=ErrorResponse::class)
     * )
     *
     * @OA\RequestBody(@Model(type=UpdateBookChapterRequest::class))
     */
    #[Route(path: '/api/v1/author/book/{bookId}/chapter', methods: ['PUT'])]
    #[IsGranted(AuthorBookVoter::IS_AUTHOR, subject: 'bookId')]
    public function updateBookChapter(#[RequestBody] UpdateBookChapterRequest $request, int $bookId): Response
    {
        $this->bookChapterManager->updateChapter($request);

        return $this->json(null);
    }

    /**
     * @OA\Tag(name="Author book chapter API")
     *
     * @OA\Response(
     *     response=200,
     *     description="Remove a book chapter"
     * )
     * @OA\Response(
     *     response=404,
     *     description="Book chapter not found",
     *
     *     @Model(type=ErrorResponse::class)
     * )
     */
    #[Route(path: '/api/v1/author/book/{bookId}/chapter/{chapterId}', methods: ['DELETE'])]
    #[IsGranted(AuthorBookVoter::IS_AUTHOR, subject: 'bookId')]
    public function deleteBookChapter(int $chapterId, int $bookId): Response
    {
        $this->bookChapterManager->deleteChapter($chapterId);

        return $this->json(null);
    }

    /**
     * @OA\Tag(name="Author book chapter API")
     *
     * @OA\Response(
     *     response=200,
     *     description="Sort a book chapter"
     * )
     * @OA\Response(
     *     response=404,
     *     description="Book chapter not found",
     *
     *     @Model(type=ErrorResponse::class)
     * )
     *
     * @OA\Response(
     *     response="400",
     *     description="Validation failed",
     *
     *     @Model(type=ErrorResponse::class)
     * )
     *
     * @OA\RequestBody(@Model(type=UpdateBookChapterSortRequest::class))
     */
    #[Route(path: '/api/v1/author/book/{bookId}/chapter/sort', methods: ['POST'])]
    #[IsGranted(AuthorBookVoter::IS_AUTHOR, subject: 'bookId')]
    public function updateBookChapterSort(#[RequestBody] UpdateBookChapterSortRequest $request, int $bookId): Response
    {
        $this->bookChapterManager->updateChapterSort($request);

        return $this->json(null);
    }
}
