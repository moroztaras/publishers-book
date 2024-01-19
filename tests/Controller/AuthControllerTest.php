<?php

namespace App\Tests\Controller;

use App\Tests\AbstractTestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthControllerTest extends AbstractTestController
{
    public function testSignUp(): void
    {
        // Send request with body
        $this->client->request(Request::METHOD_POST, '/api/v1/auth/signUp', [], [], [], json_encode([
            'firstName' => 'Taras',
            'lastName' => 'Moroz',
            'email' => 'test@test.com',
            'password' => '1234567854',
            'confirmPassword' => '1234567854',
        ]));

        // Get response content
        $responseContent = json_decode($this->client->getResponse()->getContent());

        // Response was successful
        $this->assertResponseIsSuccessful();

        // Comparing the actual response content with the expected schema.
        $this->assertJsonDocumentMatchesSchema($responseContent, [
            'type' => 'object',
            'required' => ['token', 'refresh_token'],
            'properties' => [
                'token' => ['type' => 'string'],
                'refresh_token' => ['type' => 'string'],
            ],
        ]);

        // Comparing the expected value with the actual returned value.
        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
    }

    public function testSignUpBadRequest(): void
    {
        // Send request with body
        $this->client->request(Request::METHOD_POST, '/api/v1/auth/signUp', [], [], [], json_encode([]));

        // Comparing the expected value with the actual returned value.
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
    }
}
