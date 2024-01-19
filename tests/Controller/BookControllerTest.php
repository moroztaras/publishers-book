<?php

namespace App\Tests\Controller;

use App\Tests\AbstractTestController;
use App\Tests\MockUtils;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class BookControllerTest extends AbstractTestController
{
    public function testBooksByCategory(): void
    {
        // Create user
        $user = MockUtils::createUser();
        $this->em->persist($user);

        // Create category
        $bookCategory = MockUtils::createBookCategory();
        $this->em->persist($bookCategory);
        // Create book
        $book = MockUtils::createBook()
            ->setCategories(new ArrayCollection([$bookCategory]))
            ->setUser($user);

        $this->em->persist($book);

        $this->em->flush();

        // Create request
        $this->client->request(Request::METHOD_GET, '/api/v1/category/'.$bookCategory->getId().'/books');
        // Get response content
        $responseContent = json_decode($this->client->getResponse()->getContent(), true);

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
                        'required' => ['id', 'title', 'slug', 'image', 'authors', 'publicationDate'],
                        'properties' => [
                            'title' => ['type' => 'string'],
                            'slug' => ['type' => 'string'],
                            'id' => ['type' => 'integer'],
                            'publicationDate' => ['type' => 'integer'],
                            'image' => ['type' => 'string'],
                            'authors' => [
                                'type' => 'array',
                                'items' => ['type' => 'string'],
                            ],
                        ],
                    ],
                ],
            ],
        ]);
        // Comparing the expected value with the actual returned value.
        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
    }

    public function testBooksByCategoryNotFound(): void
    {
        // Create request
        $this->client->request(Request::METHOD_GET, '/api/v1/category/1/books');

        // Comparing the expected value with the actual returned value.
        $this->assertEquals(Response::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode());
    }

    public function testBookById(): void
    {
        // Create user
        $user = MockUtils::createUser();
        // Create category
        $bookCategory = MockUtils::createBookCategory();
        // Create format
        $format = MockUtils::createBookFormat();
        // Create book
        $book = MockUtils::createBook()
            ->setCategories(new ArrayCollection([$bookCategory]))
            ->setUser($user);

        // Save
        $this->em->persist($user);
        $this->em->persist($bookCategory);
        $this->em->persist($format);
        $this->em->persist($book);
        $this->em->persist(MockUtils::createBookFormatLink($book, $format));
        $this->em->flush();

        // Send Request
        $this->client->request(Request::METHOD_GET, '/api/v1/book/'.$book->getId());
        // Get response content
        $responseContent = json_decode($this->client->getResponse()->getContent(), true);

        // Response was successful
        $this->assertResponseIsSuccessful();

        // Comparing the actual response content with the expected schema.
        $this->assertJsonDocumentMatchesSchema($responseContent, [
            'type' => 'object',
            'required' => [
                'id', 'title', 'slug', 'image', 'authors', 'publicationDate', 'rating', 'reviews',
                'categories', 'formats',  'chapters',
            ],
            'properties' => [
                'title' => ['type' => 'string'],
                'slug' => ['type' => 'string'],
                'id' => ['type' => 'integer'],
                'publicationDate' => ['type' => 'integer'],
                'image' => ['type' => 'string'],
                'authors' => [
                    'type' => 'array',
                    'items' => ['type' => 'string'],
                ],
                'rating' => ['type' => 'number'],
                'reviews' => ['type' => 'integer'],
                'chapters' => [
                    'type' => 'array',
                    'items' => [
                        'type' => 'object',
                        'required' => ['id', 'title', 'slug', 'items'],
                        'properties' => [
                            'title' => ['type' => 'string'],
                            'slug' => ['type' => 'string'],
                            'id' => ['type' => 'integer'],
                        ],
                    ],
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
            ],
        ]);
    }

    public function testChapterContent(): void
    {
        // Create user
        $user = MockUtils::createUser();
        $this->em->persist($user);

        // Create book
        $book = MockUtils::createBook()->setUser($user);
        $this->em->persist($book);

        // Create book chapter
        $bookChapter = MockUtils::createBookChapter($book);
        $this->em->persist($bookChapter);

        // Create book content
        $bookContent = MockUtils::createBookContent($bookChapter);
        $this->em->persist($bookContent);

        // Create unpublished book content
        $unpublishedBookContent = MockUtils::createBookContent($bookChapter)->setIsPublished(false);
        $this->em->persist($unpublishedBookContent);

        // Save
        $this->em->flush();

        $url = sprintf('/api/v1/book/%d/chapter/%d/content', $book->getId(), $bookChapter->getId());

        // Send request
        $this->client->request(Request::METHOD_GET, $url);
        // Get response content
        $responseContent = json_decode($this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);

        // Response was successful
        $this->assertResponseIsSuccessful();

        // Comparing the expected status code with the actual returned status code.
        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $this->assertJsonDocumentMatches($responseContent, ['$.items' => self::countOf(1)]);

        // Comparing the actual response content with the expected schema.
        $this->assertJsonDocumentMatchesSchema($responseContent, [
            'type' => 'object',
            'required' => ['items', 'page', 'pages', 'perPage', 'total'],
            'properties' => [
                'page' => ['type' => 'integer'],
                'pages' => ['type' => 'integer'],
                'perPage' => ['type' => 'integer'],
                'total' => ['type' => 'integer'],
                'items' => [
                    'type' => 'array',
                    'items' => [
                        'type' => 'object',
                        'required' => ['id', 'content', 'published'],
                        'properties' => [
                            'id' => ['type' => 'integer'],
                            'content' => ['type' => 'string'],
                            'published' => ['type' => 'boolean'],
                        ],
                    ],
                ],
            ],
        ]);
    }
}
