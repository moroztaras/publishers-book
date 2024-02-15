<?php

namespace App\Tests\Controller;

use App\Tests\AbstractTestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UserControllerTest extends AbstractTestController
{
    public function testProfile()
    {
        // Create admin and auth
        $this->createAdminAndAuth('admin@test.com', 'testtest');

        // Send request
        $this->client->request(Request::METHOD_GET, '/api/v1/user/profile');

        // Get response
        $responseContent = json_decode($this->client->getResponse()->getContent());

        // The request was successful.
        $this->assertResponseIsSuccessful();

        // Comparing the expected value with the actual returned value.
        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        // Comparing the actual response content with the expected schema.
        $this->assertJsonDocumentMatchesSchema($responseContent, [
            'type' => 'object',
            'required' => ['firstName', 'lastName', 'email', 'roles'],
            'properties' => [
                'firstName' => ['type' => 'string'],
                'lastName' => ['type' => 'string'],
                'email' => ['type' => 'string'],
                'roles' => ['type' => 'array'],
            ],
        ]);
    }
}
