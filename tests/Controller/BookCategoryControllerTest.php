<?php

namespace App\Tests\Controller;

use App\Tests\AbstractTestController;
use App\Tests\MockUtils;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class BookCategoryControllerTest extends AbstractTestController
{
    // Functional test
    public function testCategories(): void
    {
        // Create new category
        $this->em->persist(MockUtils::createBookCategory());
        $this->em->flush();

        // Create request
        $this->client->request(Request::METHOD_GET, '/api/v1/book/categories');
        // Get response content decode in array
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

        // Comparing the expected value with the actual returned value.
        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
    }
}
