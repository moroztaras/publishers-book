<?php

namespace App\Tests\Controller;

use App\Tests\AbstractControllerTest;
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
}
