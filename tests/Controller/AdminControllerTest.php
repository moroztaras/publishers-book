<?php

namespace App\Tests\Controller;

use App\Tests\AbstractTestController;
use App\Tests\MockUtils;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminControllerTest extends AbstractTestController
{
    public function testGrantAuthor(): void
    {
        // Create user with role 'ROLE_USER'
        $user = $this->createUser('user@test.com', 'test_password');

        // Create admin and auth
        $this->createAdminAndAuth('admin@test.com', 'testtest');

        // Send request
        $this->client->request(Request::METHOD_POST, '/api/v1/admin/grantAuthor/'.$user->getId());

        // The request was successful.
        $this->assertResponseIsSuccessful();

        // Comparing the expected value with the actual returned value.
        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
    }

    public function testGrantAuthorUserNotFoundException(): void
    {
        // Create admin and auth
        $this->createAdminAndAuth('admin@test.com', 'testtest');

        // Send request
        $this->client->request(Request::METHOD_POST, '/api/v1/admin/grantAuthor/3');

        // Comparing the expected value with the actual returned value.
        $this->assertEquals(Response::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode());
    }

    public function testCreateCategory(): void
    {
        // Create admin and auth
        $this->createAdminAndAuth('user@test.com', 'testtest');

        $url = '/api/v1/admin/bookCategory';
        $content = json_encode(['title' => 'Test Chapter']);
        // Send request with request body
        $this->client->request(Request::METHOD_POST, $url, [], [], [], $content);

        // Get response
        $responseContent = json_decode($this->client->getResponse()->getContent());

        // The request was successful.
        $this->assertResponseIsSuccessful();

        // Comparing the actual response content with the expected schema.
        $this->assertJsonDocumentMatchesSchema($responseContent, [
            'type' => 'object',
            'required' => ['id'],
            'properties' => [
                'id' => ['type' => 'integer'],
            ],
        ]);

        // Comparing the expected value with the actual returned value.
        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
    }

    public function testCreateCategoryBadContent(): void
    {
        // Create admin and auth
        $this->createAdminAndAuth('user@test.com', 'testtest');

        // Send request with request body
        $this->client->request(Request::METHOD_POST, '/api/v1/admin/bookCategory', [], [], [], json_encode([]));

        // Comparing the expected value with the actual returned value.
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
    }

    public function testUpdateCategory(): void
    {
        // Create category
        $bookCategory = MockUtils::createBookCategory();
        // Save category
        $this->em->persist($bookCategory);
        $this->em->flush();

        // Create admin and auth
        $this->createAdminAndAuth('user@test.com', 'testtest');
        // Send request with request body
        $this->client->request(Request::METHOD_PUT, '/api/v1/admin/bookCategory/'.$bookCategory->getId(), [], [], [],
            json_encode(['title' => 'Test Chapter 2'])
        );

        // The request was successful.
        $this->assertResponseIsSuccessful();

        // Comparing the expected value with the actual returned value.
        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
    }

    public function testDeleteCategory(): void
    {
        // Create book category
        $bookCategory = MockUtils::createBookCategory();
        // Save category
        $this->em->persist($bookCategory);
        $this->em->flush();

        // Create admin and auth
        $this->createAdminAndAuth('user@test.com', 'testtest');
        // Request
        $this->client->request(Request::METHOD_DELETE, '/api/v1/admin/bookCategory/'.$bookCategory->getId());

        // The request was successful.
        $this->assertResponseIsSuccessful();

        // Comparing the expected value with the actual returned value.
        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
    }

    public function testDeleteCategoryNotFoundException(): void
    {
        // Create admin and auth
        $this->createAdminAndAuth('user@test.com', 'testtest');
        // Request
        $this->client->request(Request::METHOD_DELETE, '/api/v1/admin/bookCategory/1');

        // Comparing the expected value with the actual returned value.
        $this->assertEquals(Response::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode());
    }
}
