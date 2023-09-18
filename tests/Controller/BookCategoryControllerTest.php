<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

class BookCategoryControllerTest extends WebTestCase
{
    // Functional test
    public function testCategories(): void
    {
        // Create client
        $client = static::createClient();
        // Create request
        $client->request(Request::METHOD_GET, '/api/v1/book/categories');
        // Get response
        $responseContent = $client->getResponse()->getContent();

        // Response was successful
        $this->assertResponseIsSuccessful();

        // Comparing the actual returned value with the expected value.
        $this->assertJsonStringEqualsJsonFile(
            __DIR__.'/BookCategoryControllerTest/testCategories.json',
            $responseContent
        );
    }
}
