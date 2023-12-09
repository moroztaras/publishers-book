<?php

namespace App\Tests\Controller;

use App\Tests\AbstractControllerTest;
use App\Tests\MockUtils;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\Request;

class BookControllerTest extends AbstractControllerTest
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
    }

    public function testBookById(): void
    {
        // Create user
        $user = MockUtils::createUser();
        $this->em->persist($user);
        // Create category
        $bookCategory = MockUtils::createBookCategory();
        $this->em->persist($bookCategory);
        // Create format
        $format = MockUtils::createBookFormat();
        $this->em->persist($format);
        // Create book
        $book = MockUtils::createBook()
            ->setCategories(new ArrayCollection([$bookCategory]))
            ->setUser($user);

        $this->em->persist($book);
        $this->em->persist(MockUtils::createBookFormatLink($book, $format));
        $this->em->flush();

        // Request
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
                'categories', 'formats',
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
}
