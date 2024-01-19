<?php

namespace App\Tests\Controller;

use App\Tests\AbstractTestController;
use App\Tests\MockUtils;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthorBookChapterContentControllerTest extends AbstractTestController
{
    public function testChapterContent(): void
    {
        // Create user
        $user = $this->createAuthorAndAuth('user@test.com', 'testtest');
        // Create book
        $book = MockUtils::createBook()->setUser($user);
        // Create chapter
        $chapter = MockUtils::createBookChapter($book);
        // Create content of book chapter
        $content = MockUtils::createBookContent($chapter);
        // Create unpublished content of book chapter
        $unpublishedContent = MockUtils::createBookContent($chapter)->setIsPublished(false);

        // Save
        $this->em->persist($book);
        $this->em->persist($chapter);
        $this->em->persist($content);
        $this->em->persist($unpublishedContent);
        $this->em->flush();

        $url = sprintf('/api/v1/author/book/%d/chapter/%d/content', $book->getId(), $chapter->getId());
        // Send request
        $this->client->request(Request::METHOD_GET, $url);

        // Get response content
        $responseContent = json_decode($this->client->getResponse()->getContent(), null, 512, JSON_THROW_ON_ERROR);

        // Response was successful
        $this->assertResponseIsSuccessful();

        $this->assertJsonDocumentMatches($responseContent, ['$.items' => self::countOf(2)]);

        // Comparing the expected value with the actual returned value.
        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

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

    public function testCreateBookContent(): void
    {
        // Create user
        $user = $this->createAuthorAndAuth('user@test.com', 'testtest');
        // Create book
        $book = MockUtils::createBook()->setUser($user);
        // Create book chapter
        $chapter = MockUtils::createBookChapter($book);

        // Save
        $this->em->persist($book);
        $this->em->persist($chapter);
        $this->em->flush();

        $url = sprintf('/api/v1/author/book/%d/chapter/%d/content', $book->getId(), $chapter->getId());
        $requestContent = json_encode(['content' => 'New Test Content', 'published' => true]);

        $this->client->request(Request::METHOD_POST, $url, [], [], [], $requestContent);

        // Get response
        $responseContent = json_decode($this->client->getResponse()->getContent(), null, 512, JSON_THROW_ON_ERROR);

        // Response was successful
        $this->assertResponseIsSuccessful();

        // Comparing the expected value with the actual returned value.
        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        // Comparing the actual response content with the expected schema.
        $this->assertJsonDocumentMatchesSchema($responseContent, [
            'type' => 'object',
            'required' => ['id'],
            'properties' => [
                'id' => ['type' => 'integer'],
            ],
        ]);
    }

    public function testCreateBookContentChapterNotFound(): void
    {
        // Create user
        $user = $this->createAuthorAndAuth('user@test.com', 'testtest');
        // Create book
        $book = MockUtils::createBook()->setUser($user);

        // Save
        $this->em->persist($book);
        $this->em->flush();

        $url = sprintf('/api/v1/author/book/%d/chapter/%d/content', $book->getId(), 1);
        $requestContent = json_encode(['content' => 'New Test Content', 'published' => true]);

        $this->client->request(Request::METHOD_POST, $url, [], [], [], $requestContent);

        // Comparing the expected value with the actual returned value.
        $this->assertEquals(Response::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode());
    }

    public function testCreateBookBadContent(): void
    {
        // Create user
        $user = $this->createAuthorAndAuth('user@test.com', 'testtest');
        // Create book
        $book = MockUtils::createBook()->setUser($user);
        // Create book chapter
        $chapter = MockUtils::createBookChapter($book);

        // Save
        $this->em->persist($book);
        $this->em->persist($chapter);
        $this->em->flush();

        $url = sprintf('/api/v1/author/book/%d/chapter/%d/content', $book->getId(), $chapter->getId());

        $this->client->request(Request::METHOD_POST, $url, [], [], [], json_encode([]));

        // Comparing the expected value with the actual returned value.
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
    }

    public function testUpdateBookContent(): void
    {
        // Create user
        $user = $this->createAuthorAndAuth('user@test.com', 'testtest');
        // Create book
        $book = MockUtils::createBook()->setUser($user);
        // Create chapter
        $chapter = MockUtils::createBookChapter($book);
        // Create content
        $content = MockUtils::createBookContent($chapter);

        // Save
        $this->em->persist($book);
        $this->em->persist($chapter);
        $this->em->persist($content);
        $this->em->flush();

        $url = sprintf('/api/v1/author/book/%d/chapter/%d/content/%d', $book->getId(), $chapter->getId(), $content->getId());
        $requestContent = json_encode(['content' => 'Edit Test Content', 'published' => false]);

        // Send request
        $this->client->request(Request::METHOD_PUT, $url, [], [], [], $requestContent);

        // Response was successful
        $this->assertResponseIsSuccessful();

        // Comparing the expected value with the actual returned value.
        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
    }

    public function testDeleteBookContent(): void
    {
        // Create user
        $user = $this->createAuthorAndAuth('user@test.com', 'testtest');
        // Create book
        $book = MockUtils::createBook()->setUser($user);
        // Create chapter
        $chapter = MockUtils::createBookChapter($book);
        // Create content
        $content = MockUtils::createBookContent($chapter);

        // Save
        $this->em->persist($book);
        $this->em->persist($chapter);
        $this->em->persist($content);
        $this->em->flush();

        $url = sprintf('/api/v1/author/book/%d/chapter/%d/content/%d', $book->getId(), $chapter->getId(), $content->getId());
        // Send request
        $this->client->request(Request::METHOD_DELETE, $url);

        // Response was successful
        $this->assertResponseIsSuccessful();

        // Comparing the expected value with the actual returned value.
        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
    }
}
