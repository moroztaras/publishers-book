<?php

namespace App\Tests\Controller;

use App\Tests\AbstractControllerTest;
use App\Tests\MockUtils;
use Symfony\Component\HttpFoundation\Request;

class AuthorBookChapterControllerTest extends AbstractControllerTest
{
    public function testCreateBookChapter(): void
    {
        // Create author and auth
        $user = $this->createAuthorAndAuth('user@test.com', 'testtest');
        // Create book
        $book = MockUtils::createBook()->setUser($user);

        // Save
        $this->em->persist($book);
        $this->em->flush();

        // Send request with body
        $this->client->request(Request::METHOD_POST, '/api/v1/author/book/'.$book->getId().'/chapter', [], [], [],
            json_encode(['title' => 'Test Book']));

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

    public function testUpdateBookChapter(): void
    {
        // Create author and auth
        $user = $this->createAuthorAndAuth('user@test.com', 'testtest');
        // Create book
        $book = MockUtils::createBook()->setUser($user);
        // Create chapter
        $chapter = MockUtils::createBookChapter($book);

        // Save
        $this->em->persist($book);
        $this->em->persist($chapter);
        $this->em->flush();

        // Send request with body
        $this->client->request(Request::METHOD_POST, '/api/v1/author/book/'.$book->getId().'/chapter', [], [], [],
            json_encode(['title' => 'Updated Book Chapter', 'id' => $chapter->getId()]));

        // Response was successful
        $this->assertResponseIsSuccessful();
    }

    //    public function testUpdateBookChapterSort(): void
    //    {
    //        // Create author and auth
    //        $user = $this->createAuthorAndAuth('user@test.com', 'testtest');
    //        // Create book
    //        $book = MockUtils::createBook()->setUser($user);
    //        // Create chapter 1 of book
    //        $chapterFirst = MockUtils::createBookChapter($book);
    //        // Create chapter 2 of book
    //        $chapterSecond = MockUtils::createBookChapter($book);
    //        // Create chapter 3 of book
    //        $chapterThird = MockUtils::createBookChapter($book);
    //
    //        // Save
    //        $this->em->persist($book);
    //        $this->em->persist($chapterFirst);
    //        $this->em->persist($chapterSecond);
    //        $this->em->persist($chapterThird);
    //        $this->em->flush();
    //
    //        // Get response with body
    //        $this->client->request(Request::METHOD_POST, '/api/v1/author/book/'.$book->getId().'/chapter/sort', [], [], [],
    //            json_encode([
    //                'id' => $chapterFirst->getId(),
    //                'nextId' => $chapterThird->getId(),
    //                'previousId' => $chapterSecond->getId(),
    //            ]));
    //
    //        // Response was successful
    //        $this->assertResponseIsSuccessful();
    //    }

    //    public function testGetBookChapterTree(): void
    //    {
    //        // Create author and auth
    //        $user = $this->createAuthorAndAuth('user@test.com', 'testtest');
    //        // Create book
    //        $book = MockUtils::createBook()->setUser($user);
    //        // Create main chapter
    //        $chapterMain = MockUtils::createBookChapter($book);
    //        // Create nested chapter
    //        $chapterNested = MockUtils::createBookChapter($book)
    //            ->setLevel(2)
    //            ->setParent($chapterMain)
    //            ->setSort(2);
    //
    //        // Save
    //        $this->em->persist($book);
    //        $this->em->persist($chapterMain);
    //        $this->em->persist($chapterNested);
    //        $this->em->flush();
    //
    //        // Send request
    //        $this->client->request(Request::METHOD_GET, '/api/v1/author/book/'.$book->getId().'/chapters');
    //
    //        // Get response
    //        $responseContent = json_decode($this->client->getResponse()->getContent());
    //
    //        // Response was successful
    //        $this->assertResponseIsSuccessful();
    //
    //        // Comparing the actual response content with the expected schema.
    //        $this->assertJsonDocumentMatchesSchema($responseContent, [
    //            'type' => 'object',
    //            'required' => ['items'],
    //            'properties' => [
    //                'items' => [
    //                    'type' => 'array',
    //                    'items' => [
    //                        'type' => 'object',
    //                        'required' => ['id', 'title', 'slug', 'items'],
    //                        'properties' => [
    //                            'title' => ['type' => 'string'],
    //                            'slug' => ['type' => 'string'],
    //                            'id' => ['type' => 'integer'],
    //                            'items' => [
    //                                'type' => 'array',
    //                                'items' => [
    //                                    'type' => 'object',
    //                                    'required' => ['id', 'title', 'slug', 'items'],
    //                                    'properties' => [
    //                                        'title' => ['type' => 'string'],
    //                                        'slug' => ['type' => 'string'],
    //                                        'id' => ['type' => 'integer'],
    //                                    ],
    //                                ],
    //                            ],
    //                        ],
    //                    ],
    //                ],
    //            ],
    //        ]);
    //    }

    public function testDeleteBookChapter(): void
    {
        // Create user
        $user = $this->createAuthorAndAuth('user@test.com', 'testtest');
        // Create book
        $book = MockUtils::createBook()->setUser($user);
        // Create chapter
        $chapter = MockUtils::createBookChapter($book);

        // Save
        $this->em->persist($book);
        $this->em->persist($chapter);
        $this->em->flush();

        // Send request
        $this->client->request(Request::METHOD_DELETE, '/api/v1/author/book/'.$book->getId().'/chapter/'.$chapter->getId());

        // Response was successful
        $this->assertResponseIsSuccessful();
    }
}
