<?php

namespace App\Tests\Controller;

use App\Tests\AbstractControllerTest;
use Symfony\Component\HttpFoundation\Request;

class AuthControllerTest extends AbstractControllerTest
{
    public function testSignUp(): void
    {
        // Send request
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
    }
}
