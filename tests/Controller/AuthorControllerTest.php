<?php

namespace App\Tests\Controller;

use App\Tests\AbstractControllerTest;
use Symfony\Component\HttpFoundation\Request;
use App\Tests\MockUtils;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class AuthorControllerTest extends AbstractControllerTest
{
    public function testCreateBook(): void
    {
        // Create admin and auth
        $this->createAuthorAndAuth('user@test.com', 'testtest');
        // Send request with body
        $this->client->request(Request::METHOD_POST, '/api/v1/author/book', [], [], [], json_encode([
            'title' => 'Test Book',
        ]));

        // Get response
        $responseContent = json_decode($this->client->getResponse()->getContent());

        // Response was successful
        $this->assertResponseIsSuccessful();

        // Comparing the actual response content with the expected schema.
        $this->assertJsonDocumentMatchesSchema($responseContent, [
            'type' => 'object',
            'required' => ['id'],
            'properties' => [
                'id' => ['type' => 'integer'],
            ],
        ]);
    }

    public function testUploadBookCover(): void
    {
        // Create admin and auth
        $user = $this->createAuthorAndAuth('user@test.com', 'testtest');
        // Create book
        $book = MockUtils::createBook()->setUser($user)->setImage(null);

        // Save
        $this->em->persist($book);
        $this->em->flush();

        $fixturePath = __DIR__.'/../Fixtures/book_cover.png';
        $clonedImagePath = sys_get_temp_dir().PATH_SEPARATOR.'test.png';

        (new Filesystem())->copy($fixturePath, $clonedImagePath);

        $uploadedFile = new UploadedFile(
            $clonedImagePath,
            'test.png',
            'image/png',
            null,
            true,
        );

        // Send request
        $this->client->request(Request::METHOD_POST, '/api/v1/author/book/'.$book->getId().'/uploadCover', [], [
            'cover' => $uploadedFile,
        ]);

        // Get response
        $responseContent = json_decode($this->client->getResponse()->getContent());

        // Response was successful
        $this->assertResponseIsSuccessful();

        // Comparing the actual response content with the expected schema.
        $this->assertJsonDocumentMatchesSchema($responseContent, [
            'type' => 'object',
            'required' => ['link'],
            'properties' => [
                'link' => ['type' => 'string'],
            ],
        ]);
    }

    public function testDeleteBook(): void
    {
        // Create admin and auth
        $user = $this->createAuthorAndAuth('user@test.com', 'testtest');
        // Create book
        $book = MockUtils::createBook()->setUser($user);

        // Save
        $this->em->persist($book);
        $this->em->flush();

        // Send request
        $this->client->request(Request::METHOD_DELETE, '/api/v1/author/book/'.$book->getId());

        // Response was successful
        $this->assertResponseIsSuccessful();
    }
}
