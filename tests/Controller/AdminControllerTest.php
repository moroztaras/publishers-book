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

        $adminUserName = 'admin@test.com';
        $adminUserPassword = 'test_password';
        // Create user with role 'ROLE_ADMIN'
        $this->createAdmin($adminUserName, $adminUserPassword);

        // Login user
        $this->auth($adminUserName, $adminUserPassword);

        // Send request
        $this->client->request(Request::METHOD_POST, '/api/v1/admin/grantAuthor/'.$user->getId());

        // The request was successful.
        $this->assertResponseIsSuccessful();
    }
}
