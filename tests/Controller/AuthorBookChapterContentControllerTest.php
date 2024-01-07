<?php

namespace App\Tests\Controller;

use App\Tests\AbstractControllerTest;
use App\Tests\MockUtils;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthorBookChapterContentControllerTest extends AbstractControllerTest
{
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
}
