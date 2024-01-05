<?php

namespace App\Controller;

use App\Attribute\RequestBody;
use App\Exception\BookChapterNotFoundException;
use App\Manager\BookContentManager;
use App\Model\Author\CreateBookChapterContentRequest;
use App\Model\ErrorResponse;
use App\Model\IdResponse;
use App\Security\Voter\AuthorBookVoter;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class AuthorBookChapterContentController extends AbstractController
{
    public function __construct(private readonly BookContentManager $bookContentManager)
    {
    }

    // TODO Need add 'attachables: [new Model(type: BookChapterContentPage::class)]' to response 200
    #[Route(path: '/api/v1/author/book/{bookId}/chapter/{chapterId}/content', methods: ['GET'])]
    #[IsGranted(AuthorBookVoter::IS_AUTHOR, subject: 'bookId')]
    #[OA\Tag(name: 'Author book chapter content API')]
    #[OA\Parameter(name: 'page', description: 'Page number', in: 'query', schema: new OA\Schema(type: 'integer'))]
    #[OA\Response(response: 200, description: 'Get book chapter content')]
    public function chapterContent(Request $request, int $chapterId, int $bookId): Response
    {
        return $this->json(
            $this->bookContentManager->getAllContent($chapterId, (int) $request->query->get('page', 1))
        );
    }

    // TODO Need add 'BookChapterNotFoundException' to response 404
    #[Route(path: '/api/v1/author/book/{bookId}/chapter/{chapterId}/content', methods: ['POST'])]
    #[IsGranted(AuthorBookVoter::IS_AUTHOR, subject: 'bookId')]
    #[OA\Tag(name: 'Author book chapter content API')]
    #[OA\Response(response: 200, description: 'Create a book chapter content', attachables: [new Model(type: IdResponse::class)])]
    #[OA\Response(response: 400, description: 'Validation failed', attachables: [new Model(type: ErrorResponse::class)])]
    #[OA\Response(response: 404, description: 'Book chapter not found')]
    #[OA\RequestBody(attachables: [new Model(type: CreateBookChapterContentRequest::class)])]
    public function createBookChapterContent(#[RequestBody] CreateBookChapterContentRequest $request, int $bookId, int $chapterId): Response
    {
        return $this->json($this->bookContentManager->createContent($request, $chapterId));
    }

    #[Route(path: '/api/v1/author/book/{bookId}/chapter/{chapterId}/content/{contentId}', methods: ['PUT'])]
    #[IsGranted(AuthorBookVoter::IS_AUTHOR, subject: 'bookId')]
    #[OA\Tag(name: 'Author book chapter content API')]
    #[OA\Response(response: 200, description: 'Update a book chapter content')]
    #[OA\Response(response: 404, description: 'Book chapter content not found', attachables: [new Model(type: ErrorResponse::class)])]
    #[OA\Response(response: 400, description: 'Validation failed', attachables: [new Model(type: ErrorResponse::class)])]
    #[OA\RequestBody(attachables: [new Model(type: CreateBookChapterContentRequest::class)])]
    public function updateBookChapterContent(#[RequestBody] CreateBookChapterContentRequest $request, int $bookId, int $contentId): Response
    {
        $this->bookContentManager->updateContent($request, $contentId);

        return $this->json(null);
    }

    #[Route(path: '/api/v1/author/book/{bookId}/chapter/{chapterId}/content/{contentId}', methods: ['DELETE'])]
    #[IsGranted(AuthorBookVoter::IS_AUTHOR, subject: 'bookId')]
    #[OA\Tag(name: 'Author book chapter content API')]
    #[OA\Response(response: 200, description: 'Remove a book chapter content')]
    #[OA\Response(response: 404, description: 'Book chapter content not found', attachables: [new Model(type: ErrorResponse::class)])]
    public function deleteBookChapterContent(int $id, int $bookId): Response
    {
        $this->bookContentManager->deleteContent($id);

        return $this->json(null);
    }
}
