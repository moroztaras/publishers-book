<?php

namespace App\Controller;

use App\Attribute\RequestBody;
use App\Attribute\RequestFile;
use App\Exception\BookCoverNotFoundException;
use App\Manager\AuthorBookManager;
use App\Manager\BookPublishManager;
use App\Model\Author\BookDetails;
use App\Model\Author\BookListResponse;
use App\Model\Author\CreateBookRequest;
use App\Model\Author\PublishBookRequest;
use App\Model\Author\UpdateBookRequest;
use App\Model\Author\UploadCoverResponse;
use App\Model\ErrorResponse;
use App\Model\IdResponse;
use App\Security\Voter\AuthorBookVoter;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\NotNull;

class AuthorController extends AbstractController
{
    public function __construct(private readonly AuthorBookManager $authorBookManager, private readonly BookPublishManager $bookPublishManager)
    {
    }

    #[Route(path: '/api/v1/author/book/{id}/cover', methods: ['POST'])]
    #[IsGranted(AuthorBookVoter::IS_AUTHOR, subject: 'id')]
    #[OA\Tag(name: 'Author API')]
    #[OA\Response(response: 200, description: 'Upload book cover', attachables: [new Model(type: UploadCoverResponse::class)])]
    #[OA\Response(response: 400, description: 'Validation failed', attachables: [new Model(type: ErrorResponse::class)])]
    public function uploadCover(
        int $id,
        #[RequestFile(field: 'cover', constraints: [
                new NotNull(),
                new Image(maxSize: '1M', mimeTypes: ['image/jpeg', 'image/png', 'image/jpg']),
            ])]
        UploadedFile $file
    ): Response {
        return $this->json($this->authorBookManager->uploadCover($id, $file));
    }

    // TODO Add ', attachables: [new Model(type: BookCoverNotFoundException::class)]' to response 404
    #[Route(path: '/api/v1/author/book/{id}/cover', methods: ['DELETE'])]
    #[IsGranted(AuthorBookVoter::IS_AUTHOR, subject: 'id')]
    #[OA\Tag(name: 'Author API')]
    #[OA\Response(response: 200, description: 'Remove book cover.')]
    #[OA\Response(response: 404, description: 'Book cover not found.')]
    public function removeCover(int $id): Response
    {
        return $this->json($this->authorBookManager->removeCover($id));
    }

    #[Route(path: '/api/v1/author/book/{id}/publish', methods: ['POST'])]
    #[IsGranted(AuthorBookVoter::IS_AUTHOR, subject: 'id')]
    #[OA\Tag(name: 'Author API')]
    #[OA\Response(response: 200, description: 'Publish a book')]
    #[OA\Response(response: 400, description: 'Validation failed', attachables: [new Model(type: ErrorResponse::class)])]
    #[OA\RequestBody(attachables: [new Model(type: PublishBookRequest::class)])]
    public function publish(int $id, #[RequestBody] PublishBookRequest $request): Response
    {
        $this->bookPublishManager->publish($id, $request);

        return $this->json(null);
    }

    #[Route(path: '/api/v1/author/book/{id}/unpublish', methods: ['POST'])]
    #[IsGranted(AuthorBookVoter::IS_AUTHOR, subject: 'id')]
    #[OA\Tag(name: 'Author API')]
    #[OA\Response(response: 200, description: 'Unpublished a book')]
    public function unPublish(int $id): Response
    {
        $this->bookPublishManager->unPublish($id);

        return $this->json(null);
    }

    #[Route(path: '/api/v1/author/books', methods: ['GET'])]
    #[OA\Tag(name: 'Author API')]
    #[OA\Response(response: 200, description: 'Get authors owned books', attachables: [new Model(type: BookListResponse::class)])]
    public function books(#[CurrentUser] UserInterface $user): Response
    {
        return $this->json($this->authorBookManager->getBooks($user));
    }

    // TODO Need add ' attachables: [new Model(type: BookDetails::class)]' to response 200
    #[Route(path: '/api/v1/author/book/{id}', methods: ['GET'])]
    #[IsGranted(AuthorBookVoter::IS_AUTHOR, subject: 'id')]
    #[OA\Tag(name: 'Author API')]
    #[OA\Response(response: 200, description: 'Get authors owned book')]
    #[OA\Response(response: 404, description: 'Book not found', attachables: [new Model(type: ErrorResponse::class)])]
    public function book(int $id): Response
    {
        return $this->json($this->authorBookManager->getBook($id));
    }

    #[Route(path: '/api/v1/author/book', methods: ['POST'])]
    #[OA\Tag(name: 'Author API')]
    #[OA\Response(response: 200, description: 'Create a book', attachables: [new Model(type: IdResponse::class)])]
    #[OA\Response(response: 400, description: 'Validation failed', attachables: [new Model(type: ErrorResponse::class)])]
    #[OA\RequestBody(attachables: [new Model(type: CreateBookRequest::class)])]
    public function createBook(#[RequestBody] CreateBookRequest $request, #[CurrentUser] UserInterface $user): Response
    {
        return $this->json($this->authorBookManager->createBook($request, $user));
    }

    #[Route(path: '/api/v1/author/book/{id}', methods: ['PUT'])]
    #[IsGranted(AuthorBookVoter::IS_AUTHOR, subject: 'id')]
    #[OA\Tag(name: 'Author API')]
    #[OA\Response(response: 200, description: 'Update a book')]
    #[OA\Response(response: 400, description: 'Validation failed', attachables: [new Model(type: ErrorResponse::class)])]
    #[OA\RequestBody(attachables: [new Model(type: UpdateBookRequest::class)])]
    public function updateBook(int $id, #[RequestBody] UpdateBookRequest $request): Response
    {
        $this->authorBookManager->updateBook($id, $request);

        return $this->json(null);
    }

    #[Route(path: '/api/v1/author/book/{id}', methods: ['DELETE'])]
    #[IsGranted(AuthorBookVoter::IS_AUTHOR, subject: 'id')]
    #[OA\Tag(name: 'Author API')]
    #[OA\Response(response: 200, description: 'Remove a book')]
    #[OA\Response(response: 404, description: 'Book not found', attachables: [new Model(type: ErrorResponse::class)])]
    public function deleteBook(int $id): Response
    {
        $this->authorBookManager->deleteBook($id);

        return $this->json(null);
    }
}
