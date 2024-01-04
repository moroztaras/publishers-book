<?php

namespace App\Controller;

use App\Attribute\RequestBody;
use App\Exception\BookCoverNotFoundException;
use App\Manager\AuthorBookChapterManager;
use App\Model\Author\CreateBookChapterRequest;
use App\Model\Author\UpdateBookChapterRequest;
use App\Model\Author\UpdateBookChapterSortRequest;
use App\Model\BookChapterTreeResponse;
use App\Model\ErrorResponse;
use App\Model\IdResponse;
use App\Security\Voter\AuthorBookVoter;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class AuthorBookChapterController extends AbstractController
{
    public function __construct(private readonly AuthorBookChapterManager $bookChapterManager)
    {
    }

    #[Route(path: '/api/v1/author/book/{bookId}/chapters', methods: ['GET'])]
    #[IsGranted(AuthorBookVoter::IS_AUTHOR, subject: 'bookId')]
    #[OA\Tag(name: 'Author book chapter API')]
    #[OA\Response(response: 200, description: 'Get book chapters as tree', attachables: [new Model(type: BookChapterTreeResponse::class)])]
    public function chapters(int $bookId): Response
    {
        return $this->json($this->bookChapterManager->getChaptersTree($bookId));
    }

    #[Route(path: '/api/v1/author/book/{bookId}/chapter', methods: ['POST'])]
    #[IsGranted(AuthorBookVoter::IS_AUTHOR, subject: 'bookId')]
    #[OA\Tag(name: 'Author book chapter API')]
    #[OA\Response(response: 200, description: 'Create a book chapter', attachables: [new Model(type: IdResponse::class)])]
    #[OA\Response(response: 400, description: 'Validation failed', attachables: [new Model(type: ErrorResponse::class)])]
    #[OA\RequestBody(attachables: [new Model(type: CreateBookChapterRequest::class)])]
    public function createBookChapter(#[RequestBody] CreateBookChapterRequest $request, int $bookId): Response
    {
        return $this->json($this->bookChapterManager->createChapter($request, $bookId));
    }

    #[Route(path: '/api/v1/author/book/{bookId}/chapter/{id}', methods: ['POST'])]
    #[IsGranted(AuthorBookVoter::IS_AUTHOR, subject: 'bookId')]
    #[OA\Tag(name: 'Author book chapter API')]
    #[OA\Response(response: 200, description: 'Update a book chapter')]
    #[OA\Response(response: 404, description: 'Book chapter not found', attachables: [new Model(type: ErrorResponse::class)])]
    #[OA\Response(response: 400, description: 'Validation failed', attachables: [new Model(type: ErrorResponse::class)])]
    #[OA\RequestBody(attachables: [new Model(type: UpdateBookChapterRequest::class)])]
    public function updateBookChapter(#[RequestBody] UpdateBookChapterRequest $request, int $bookId, int $id): Response
    {
        $this->bookChapterManager->updateChapter($request, $id);

        return $this->json(null);
    }

    // TODO Add ' attachables: [new Model(type: BookCoverNotFoundException::class)]' to response 404
    #[Route(path: '/api/v1/author/book/{bookId}/chapter/{chapterId}', methods: ['DELETE'])]
    #[IsGranted(AuthorBookVoter::IS_AUTHOR, subject: 'bookId')]
    #[OA\Tag(name: 'Author book chapter API"')]
    #[OA\Response(response: 200, description: 'Remove a book chapter')]
    #[OA\Response(response: 404, description: 'Book chapter not found')]
    public function deleteBookChapter(int $chapterId, int $bookId): Response
    {
        $this->bookChapterManager->deleteChapter($chapterId);

        return $this->json(null);
    }

    #[Route(path: '/api/v1/author/book/{bookId}/chapter/sort', methods: ['POST'])]
    #[IsGranted(AuthorBookVoter::IS_AUTHOR, subject: 'bookId')]
    #[OA\Tag(name: 'Author book chapter API"')]
    #[OA\Response(response: 200, description: 'Sort a book chapter')]
    #[OA\Response(response: 404, description: 'Book chapter not found', attachables: [new Model(type: ErrorResponse::class)])]
    #[OA\Response(response: 400, description: 'Validation failed', attachables: [new Model(type: ErrorResponse::class)])]
    #[OA\RequestBody(attachables: [new Model(type: UpdateBookChapterSortRequest::class)])]
    public function updateBookChapterSort(#[RequestBody] UpdateBookChapterSortRequest $request, int $bookId, int $id): Response
    {
        $this->bookChapterManager->updateChapterSort($request, $id);

        return $this->json(null);
    }
}
