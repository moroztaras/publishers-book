<?php

namespace App\Controller;

use App\Attribute\RequestBody;
use App\Attribute\RequestFile;
use App\Manager\AuthorManager;
use App\Model\Author\BookListResponse;
use App\Model\Author\CreateBookRequest;
use App\Model\Author\PublishBookRequest;
use App\Model\Author\UploadCoverResponse;
use App\Model\ErrorResponse;
use App\Model\IdResponse;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\NotNull;

class AuthorController extends AbstractController
{
    public function __construct(private AuthorManager $authorManager)
    {
    }

    /**
     * @OA\Tag(name="Author API")
     * @OA\Response(
     *     response=200,
     *     description="Upload book cover",
     *     @Model(type=UploadCoverResponse::class)
     * )
     * @OA\Response(
     *     response="400",
     *     description="Validation failed",
     *     @Model(type=ErrorResponse::class)
     * )
     */
    #[Route(path: '/api/v1/author/book/{id}/upload-cover', methods: ['POST'])]
    public function uploadCover(
        int $id,
        #[RequestFile(field: 'cover', constraints: [
                new NotNull(),
                new Image(maxSize: '1M', mimeTypes: ['image/jpeg', 'image/png', 'image/jpg']),
            ])]
        UploadedFile $file
    ): Response {
        return $this->json($this->authorManager->uploadCover($id, $file));
    }

    /**
     * @OA\Tag(name="Author API")
     *
     * @OA\Response(
     *     response=200,
     *     description="Publish a book"
     * )
     * @OA\Response(
     *     response="400",
     *     description="Validation failed",
     *
     *     @Model(type=ErrorResponse::class)
     * )
     *
     * @OA\RequestBody(@Model(type=PublishBookRequest::class))
     */
    #[Route(path: '/api/v1/author/book/{id}/publish', methods: ['POST'])]
    public function publish(int $id, #[RequestBody] PublishBookRequest $request): Response
    {
        $this->authorManager->publish($id, $request);

        return $this->json(null);
    }

    /**
     * @OA\Tag(name="Author API")
     *
     * @OA\Response(response=200,description="Unpublish a book")
     */
    #[Route(path: '/api/v1/author/book/{id}/unpublish', methods: ['POST'])]
    public function unpublish(int $id): Response
    {
        $this->authorManager->unpublish($id);

        return $this->json(null);
    }

    /**
     * @OA\Tag(name="Author API")
     *
     * @OA\Response(
     *     response=200,
     *     description="Get authors owned books",
     *
     *     @Model(type=BookListResponse::class)
     * )
     */
    #[Route(path: '/api/v1/author/books', methods: ['GET'])]
    public function books(): Response
    {
        return $this->json($this->authorManager->getBooks());
    }

    /**
     * @OA\Tag(name="Author API")
     *
     * @OA\Response(
     *     response=200,
     *     description="Create a book",
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
     * @OA\RequestBody(@Model(type=CreateBookRequest::class))
     */
    #[Route(path: '/api/v1/author/book', methods: ['POST'])]
    public function createBook(#[RequestBody] CreateBookRequest $request): Response
    {
        return $this->json($this->authorManager->createBook($request));
    }

    /**
     * @OA\Tag(name="Author API")
     *
     * @OA\Response(
     *     response=200,
     *     description="Remove a book"
     * )
     * @OA\Response(
     *     response=404,
     *     description="Book not found",
     *
     *     @Model(type=ErrorResponse::class)
     * )
     */
    #[Route(path: '/api/v1/author/book/{id}', methods: ['DELETE'])]
    public function deleteBook(int $id): Response
    {
        $this->authorManager->deleteBook($id);

        return $this->json(null);
    }
}
