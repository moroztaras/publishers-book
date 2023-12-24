<?php

namespace App\Tests\Controller;

use App\Tests\AbstractControllerTest;
use App\Tests\MockUtils;
use Symfony\Component\HttpFoundation\Request;

class AdminControllerTest extends AbstractControllerTest
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
    }
}
