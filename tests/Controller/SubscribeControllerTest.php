<?php

namespace App\Tests\Controller;

use App\Tests\AbstractTestController;
use App\Tests\MockUtils;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SubscribeControllerTest extends AbstractTestController
{
    final public const EMAIL = 'test@localhost.local';

    // Tests for successful
    public function testSubscribe(): void
    {
        $content = json_encode(['email' => self::EMAIL, 'agreed' => true]);
        $this->client->request(Request::METHOD_POST, '/api/v1/subscribe', [], [], [], $content);

        // Response was successful
        $this->assertResponseIsSuccessful();

        // Comparing the expected value with the actual returned value.
        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
    }

    public function testSubscriberAlreadyExistsException(): void
    {
        // Create subscribe
        $subscribe = MockUtils::createSubscriber();
        $this->em->persist($subscribe);
        $this->em->flush();

        $content = json_encode(['email' => self::EMAIL, 'agreed' => true]);
        $this->client->request(Request::METHOD_POST, '/api/v1/subscribe', [], [], [], $content);

        // Comparing the expected value with the actual returned value.
        $this->assertEquals(Response::HTTP_CONFLICT, $this->client->getResponse()->getStatusCode());
    }

    public function testSubscribeNotAgreed(): void
    {
        $content = json_encode(['email' => self::EMAIL]);
        $this->client->request(Request::METHOD_POST, '/api/v1/subscribe', [], [], [], $content);
        $responseContent = json_decode($this->client->getResponse()->getContent());

        // Check status code
        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        // Comparing the actual response content with the expected schema.
        $this->assertJsonDocumentMatches($responseContent, [
            '$.message' => 'Validation failed',
            '$.details.violations' => self::countOf(1),
            '$.details.violations[0].field' => 'agreed',
        ]);
    }
}
