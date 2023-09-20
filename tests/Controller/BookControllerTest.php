<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

class BookControllerTest extends WebTestCase
{
    public function testBooksByCategory(): void
    {
        $client = static::createClient();
        // Create request
        $client->request(Request::METHOD_GET, '/api/v1/category/10/books');
        // Get response content
        $responseContent = $client->getResponse()->getContent();

        // Response was successful
        $this->assertResponseIsSuccessful();

        // Comparing the actual returned value with the expected value.
        $this->assertJsonStringEqualsJsonFile(
            __DIR__.'/BookControllerTest/testBooksByCategory.json',
            $responseContent
        );
    }
}
