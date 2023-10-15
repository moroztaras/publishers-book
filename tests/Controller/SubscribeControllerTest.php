<?php

namespace App\Tests\Controller;

use App\Tests\AbstractControllerTest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SubscribeControllerTest extends AbstractControllerTest
{
    // Tests for successful
    public function testSubscribe(): void
    {
        $content = json_encode(['email' => 'test@test.com', 'agreed' => true]);
        $this->client->request(Request::METHOD_POST, '/api/v1/subscribe', [], [], [], $content);

        // Response was successful
        $this->assertResponseIsSuccessful();

        // Comparing the expected value with the actual returned value.
        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
    }
}
