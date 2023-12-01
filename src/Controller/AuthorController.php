<?php

namespace App\Controller;

use App\Attribute\RequestBody;
use App\Attribute\RequestFile;
use App\Model\Author\BookListResponse;
use App\Model\Author\CreateBookRequest;
use App\Model\Author\PublishBookRequest;
use App\Model\ErrorResponse;
use App\Model\IdResponse;
use App\Security\Voter\AuthorBookVoter;
use App\Manager\AuthorBookManager;
use App\Manager\BookPublishManager;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Validator\Constraints\Image;
use App\Model\Author\UploadCoverResponse;
use Symfony\Component\Validator\Constraints\NotNull;

class AuthorController extends AbstractController
{
    public function __construct(private AuthorBookManager $authorBookManager, private BookPublishManager $bookPublishManager)
    {
    }

    /**
     * @OA\Tag(name="Author API")
     *
     * @OA\Response(response=200, description="Upload book cover",
     *     @Model(type=UploadCoverResponse::class)
     * )
     * @OA\Response(response="400", description="Validation failed",
     *     @Model(type=ErrorResponse::class)
     * )
     */
    #[Route(path: '/api/v1/author/book/{id}/cover', methods: ['POST'])]
    #[IsGranted(AuthorBookVoter::IS_AUTHOR, subject: 'id')]
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

    /**
     * @OA\Tag(name="Author API")
     *
     * @OA\Response(response=200, description="Remove book cover")
     * @OA\Response(response="404", description="Book cover not found.")
     */
    #[Route(path: '/api/v1/author/book/{id}/cover', methods: ['DELETE'])]
    #[IsGranted(AuthorBookVoter::IS_AUTHOR, subject: 'id')]
    public function removeCover(int $id): Response
    {
        return $this->json($this->authorBookManager->removeCover($id));
    }

    /**
     * @OA\Tag(name="Author API")
     *
     * @OA\Response(response=200, description="Publish a book")
     * @OA\Response(response="400",description="Validation failed",
     *     @Model(type=ErrorResponse::class)
     * )
     * @OA\RequestBody(@Model(type=PublishBookRequest::class))
     */
    #[Route(path: '/api/v1/author/book/{id}/publish', methods: ['POST'])]
    #[IsGranted(AuthorBookVoter::IS_AUTHOR, subject: 'id')]
    public function publish(int $id, #[RequestBody] PublishBookRequest $request): Response
    {
        $this->bookPublishManager->publish($id, $request);

        return $this->json(null);
    }

    /**
     * @OA\Tag(name="Author API")
     *
     * @OA\Response(response=200,description="Unpublish a book")
     */
    #[Route(path: '/api/v1/author/book/{id}/unpublish', methods: ['POST'])]
    #[IsGranted(AuthorBookVoter::IS_AUTHOR, subject: 'id')]
    public function unpublish(int $id): Response
    {
        $this->bookPublishManager->unpublish($id);

        return $this->json(null);
    }

    /**
     * @OA\Tag(name="Author API")
     * @OA\Response(response=200, description="Get authors owned books",
     *     @Model(type=BookListResponse::class)
     * )
     */
    #[Route(path: '/api/v1/author/books', methods: ['GET'])]
    public function books(#[CurrentUser] UserInterface $user): Response
    {
        return $this->json($this->authorBookManager->getBooks($user));
    }

    /**
     * @OA\Tag(name="Author API")
     *
     * @OA\Response(response=200, description="Create a book",
     *     @Model(type=IdResponse::class)
     * )
     * @OA\Response(response="400", description="Validation failed",
     *     @Model(type=ErrorResponse::class)
     * )
     * @OA\RequestBody(@Model(type=CreateBookRequest::class))
     */
    #[Route(path: '/api/v1/author/book', methods: ['POST'])]
    public function createBook(#[RequestBody] CreateBookRequest $request, #[CurrentUser] UserInterface $user): Response
    {
        return $this->json($this->authorBookManager->createBook($request, $user));
    }

    /**
     * @OA\Tag(name="Author API")
     * @OA\Response(response=200, description="Remove a book")
     * @OA\Response(response=404,description="Book not found",
     *     @Model(type=ErrorResponse::class)
     * )
     */
    #[Route(path: '/api/v1/author/book/{id}', methods: ['DELETE'])]
    #[IsGranted(AuthorBookVoter::IS_AUTHOR, subject: 'id')]
    public function deleteBook(int $id): Response
    {
        $this->authorBookManager->deleteBook($id);

        return $this->json(null);
    }
}
