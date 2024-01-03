<?php

namespace App\Tests\Controller;

use App\Tests\AbstractControllerTest;
use App\Tests\MockUtils;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;

class AuthorControllerTest extends AbstractControllerTest
{
    public function testCreateBook(): void
    {
        // Create admin and auth
        $this->createAuthorAndAuth('user@test.com', 'testtest');
        // Send request with body
        $this->client->request(Request::METHOD_POST, '/api/v1/author/book', [], [], [], json_encode([
            'title' => 'Test Book',
        ]));

        // Get response
        $responseContent = json_decode($this->client->getResponse()->getContent());

        // Response was successful
        $this->assertResponseIsSuccessful();

        // Comparing the actual response content with the expected schema.
        $this->assertJsonDocumentMatchesSchema($responseContent, [
            'type' => 'object',
            'required' => ['id'],
            'properties' => [
                'id' => ['type' => 'integer'],
            ],
        ]);
    }

    public function testUpdateBook(): void
    {
        // Create admin and auth
        $user = $this->createAuthorAndAuth('user@test.com', 'testtest');
        // Create book
        $book = MockUtils::createBook()->setUser($user);
        // Create category
        $category = MockUtils::createBookCategory();
        // Create format
        $format = MockUtils::createBookFormat();

        // Save
        $this->em->persist($book);
        $this->em->persist($format);
        $this->em->persist($category);
        $this->em->flush();

        // Send request
        $this->client->request(Request::METHOD_PUT, '/api/v1/author/book/'.$book->getId(), [], [], [], json_encode([
            'title' => 'Updated Book',
            'authors' => ['Taras'],
            'isbn' => 'testing',
            'description' => 'testing update',
            'categories' => [$category->getId()],
            'formats' => [['id' => $format->getId(), 'price' => 123.5, 'discountPercent' => 5]],
        ]));

        // Response was successful
        $this->assertResponseIsSuccessful();
    }

    public function testPublishBook(): void
    {
        // Create and auth user
        $user = $this->createAuthorAndAuth('user@test.com', 'testtest');
        // Create book
        $book = MockUtils::createBook()->setUser($user);

        // Save
        $this->em->persist($book);
        $this->em->flush();

        // Send request
        $this->client->request(Request::METHOD_POST, '/api/v1/author/book/'.$book->getId().'/publish', [], [], [],
            json_encode(['date' => '22.02.2010']));

        // Response was successful
        $this->assertResponseIsSuccessful();
    }

    public function testUnPublishBook(): void
    {
        // Create user
        $user = $this->createAuthorAndAuth('user@test.com', 'testtest');
        // Create book
        $book = MockUtils::createBook()->setUser($user);

        // Save
        $this->em->persist($book);
        $this->em->flush();

        // Send request
        $this->client->request(Request::METHOD_POST, '/api/v1/author/book/'.$book->getId().'/unpublish');

        // Response was successful
        $this->assertResponseIsSuccessful();
    }

    public function testBooks(): void
    {
        // Create user
        $user = $this->createAuthorAndAuth('user@test.com', 'testtest');
        // Create user
        $book = MockUtils::createBook()->setUser($user);

        // Save
        $this->em->persist($book);
        $this->em->flush();

        // Send request
        $this->client->request(Request::METHOD_GET, '/api/v1/author/books');

        // Get response
        $responseContent = json_decode($this->client->getResponse()->getContent());

        // Response was successful
        $this->assertResponseIsSuccessful();
        // Comparing the actual response content with the expected schema.
        $this->assertJsonDocumentMatchesSchema($responseContent, [
            'type' => 'object',
            'required' => ['items'],
            'properties' => [
                'items' => [
                    'type' => 'array',
                    'items' => [
                        'type' => 'object',
                        'required' => ['id', 'title', 'slug', 'image'],
                        'properties' => [
                            'title' => ['type' => 'string'],
                            'slug' => ['type' => 'string'],
                            'id' => ['type' => 'integer'],
                            'image' => ['type' => 'string'],
                        ],
                    ],
                ],
            ],
        ]);
    }

    public function testBook(): void
    {
        // Create and auth user
        $user = $this->createAuthorAndAuth('user@test.com', 'testtest');
        // Create category
        $category = MockUtils::createBookCategory();
        // Create format
        $format = MockUtils::createBookFormat();
        // Create book
        $book = MockUtils::createBook()->setUser($user)->setCategories(new ArrayCollection([$category]));
        // Create relation between book & format
        $join = MockUtils::createBookFormatLink($book, $format);

        // Save
        $this->em->persist($category);
        $this->em->persist($format);
        $this->em->persist($book);
        $this->em->persist($join);
        $this->em->flush();

        // Send request
        $this->client->request(Request::METHOD_GET, '/api/v1/author/book/'.$book->getId());

        // Get response
        $responseContent = json_decode($this->client->getResponse()->getContent());

        // Response was successful
        $this->assertResponseIsSuccessful();
        // Comparing the actual response content with the expected schema.
        $this->assertJsonDocumentMatchesSchema($responseContent, [
            'type' => 'object',
            'required' => [
                'slug', 'isbn', 'description', 'categories', 'title', 'image', 'formats', 'authors', 'publicationDate',
            ],
            'properties' => [
                'title' => ['type' => 'string'],
                'description' => ['type' => 'string'],
                'slug' => ['type' => 'string'],
                'id' => ['type' => 'integer'],
                'publicationDate' => ['type' => 'integer'],
                'image' => ['type' => 'string'],
                'isbn' => ['type' => 'string'],
                'authors' => [
                    'type' => 'array',
                    'items' => ['type' => 'string'],
                ],
                'categories' => [
                    'type' => 'array',
                    'items' => [
                        'type' => 'object',
                        'required' => ['id', 'title', 'slug'],
                        'properties' => [
                            'title' => ['type' => 'string'],
                            'slug' => ['type' => 'string'],
                            'id' => ['type' => 'integer'],
                        ],
                    ],
                ],
                'formats' => [
                    'type' => 'array',
                    'items' => [
                        'type' => 'object',
                        'required' => ['id', 'title', 'description', 'comment', 'price', 'discountPercent'],
                        'properties' => [
                            'title' => ['type' => 'string'],
                            'description' => ['type' => 'string'],
                            'id' => ['type' => 'integer'],
                            'comment' => ['type' => ['string', 'null']],
                            'price' => ['type' => 'number'],
                            'discountPercent' => ['type' => 'integer'],
                        ],
                    ],
                ],
            ],
        ]);
    }

    public function testUploadBookCover(): void
    {
        // Create admin and auth
        $user = $this->createAuthorAndAuth('user@test.com', 'testtest');
        // Create book
        $book = MockUtils::createBook()->setUser($user)->setImage(null);

        // Save
        $this->em->persist($book);
        $this->em->flush();

        $fixturePath = __DIR__.'/../Fixtures/book_cover.png';
        $clonedImagePath = sys_get_temp_dir().PATH_SEPARATOR.'test.png';

        (new Filesystem())->copy($fixturePath, $clonedImagePath);

        $uploadedFile = new UploadedFile(
            $clonedImagePath,
            'test.png',
            'image/png',
            null,
            true,
        );

        // Send request
        $this->client->request(Request::METHOD_POST, '/api/v1/author/book/'.$book->getId().'/cover', [], [
            'cover' => $uploadedFile,
        ]);

        // Get response
        $responseContent = json_decode($this->client->getResponse()->getContent());

        // Response was successful
        $this->assertResponseIsSuccessful();

        // Comparing the actual response content with the expected schema.
        $this->assertJsonDocumentMatchesSchema($responseContent, [
            'type' => 'object',
            'required' => ['link'],
            'properties' => [
                'link' => ['type' => 'string'],
            ],
        ]);
    }

    public function testDeleteBook(): void
    {
        // Create admin and auth
        $user = $this->createAuthorAndAuth('user@test.com', 'testtest');
        // Create book
        $book = MockUtils::createBook()->setUser($user);

        // Save
        $this->em->persist($book);
        $this->em->flush();

        // Send request
        $this->client->request(Request::METHOD_DELETE, '/api/v1/author/book/'.$book->getId());

        // Response was successful
        $this->assertResponseIsSuccessful();
    }
}
