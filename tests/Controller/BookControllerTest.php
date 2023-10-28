<?php

namespace App\Tests\Controller;

use App\Entity\Book;
use App\Entity\BookFormat;
use App\Entity\BookCategory;
use App\Entity\BookToBookFormat;
use App\Tests\AbstractControllerTest;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\Request;

class BookControllerTest extends AbstractControllerTest
{
    public function testBooksByCategory(): void
    {
        // Create category
        $categoryId = $this->createCategory();

        // Create request
        $this->client->request(Request::METHOD_GET, '/api/v1/category/'.$categoryId.'/books');
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
                        'required' => ['id', 'title', 'slug', 'image', 'authors', 'meap', 'publicationDate'],
                        'properties' => [
                            'title' => ['type' => 'string'],
                            'slug' => ['type' => 'string'],
                            'id' => ['type' => 'integer'],
                            'publicationDate' => ['type' => 'integer'],
                            'image' => ['type' => 'string'],
                            'meap' => ['type' => 'boolean'],
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
        $bookId = $this->createBook();

        // Request
        $this->client->request(Request::METHOD_GET, '/api/v1/book/'.$bookId);
        // Get response content
        $responseContent = json_decode($this->client->getResponse()->getContent(), true);

        // Response was successful
        $this->assertResponseIsSuccessful();
        
        // Comparing the actual response content with the expected schema.
        $this->assertJsonDocumentMatchesSchema($responseContent, [
            'type' => 'object',
            'required' => [
                'id', 'title', 'slug', 'image', 'authors', 'meap', 'publicationDate', 'rating', 'reviews',
                'categories', 'formats',
            ],
            'properties' => [
                'title' => ['type' => 'string'],
                'slug' => ['type' => 'string'],
                'id' => ['type' => 'integer'],
                'publicationDate' => ['type' => 'integer'],
                'image' => ['type' => 'string'],
                'meap' => ['type' => 'boolean'],
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

    private function createCategory(): int
    {
        $bookCategory = (new BookCategory())->setTitle('Devices')->setSlug('devices');
        $this->em->persist($bookCategory);

        $this->em->persist((new Book())
            ->setTitle('Test book')
            ->setImage('http://localhost.png')
            ->setMeap(true)
            ->setIsbn('123321')
            ->setDescription('test')
            ->setPublicationDate(new \DateTimeImmutable())
            ->setAuthors(['Tester'])
            ->setCategories(new ArrayCollection([$bookCategory]))
            ->setSlug('test-book'));

        $this->em->flush();

        return $bookCategory->getId();
    }

    private function createBook(): int
    {
        $bookCategory = (new BookCategory())
            ->setTitle('Devices')
            ->setSlug('devices');

        $this->em->persist($bookCategory);

        $format = (new BookFormat())
            ->setTitle('format')
            ->setDescription('description format')
            ->setComment(null);

        $this->em->persist($format);

        $book = (new Book())
            ->setTitle('Test book')
            ->setImage('http://localhost.png')
            ->setMeap(true)
            ->setIsbn('123321')
            ->setDescription('test')
            ->setPublicationDate(new DateTimeImmutable())
            ->setAuthors(['Tester'])
            ->setCategories(new ArrayCollection([$bookCategory]))
            ->setSlug('test-book');

        $this->em->persist($book);

        $join = (new BookToBookFormat())
            ->setPrice(123.55)
            ->setFormat($format)
            ->setDiscountPercent(5)
            ->setBook($book);

        $this->em->persist($join);

        $this->em->flush();

        return $book->getId();
    }
}
