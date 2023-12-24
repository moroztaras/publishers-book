<?php

namespace App\Tests\Controller;

use App\Tests\AbstractControllerTest;
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
}
