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
        $this->client->request(Request::METHOD_PUT, '/api/v1/author/book/'.$book->getId().'/chapter', [], [], [],
            json_encode(['title' => 'Updated Book Chapter', 'id' => $chapter->getId()]));

        // Response was successful
        $this->assertResponseIsSuccessful();
    }
}
