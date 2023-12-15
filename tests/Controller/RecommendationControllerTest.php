<?php

namespace App\Tests\Controller;

use App\Tests\AbstractControllerTest;
use App\Tests\MockUtils;
use Hoverfly\Client as HoverflyClient;
use Hoverfly\Model\RequestFieldMatcher;
use Hoverfly\Model\Response;
use Symfony\Component\HttpFoundation\Request;

class RecommendationControllerTest extends AbstractControllerTest
{
    private HoverflyClient $hoverfly;

    protected function setUp(): void
    {
        parent::setUp();

        $this->setUpHoverfly();
    }

    public function testRecommendationsByBookId(): void
    {
        // Create user
        $user = MockUtils::createUser();
        $this->em->persist($user);

        // Create book
        $book = MockUtils::createBook()->setUser($user);
        $this->em->persist($book);

        $this->em->flush();
        $requestedId = 123;

        // Setting hoverfly
        $this->hoverfly->simulate(
            $this->hoverfly
                ->buildSimulation()
                ->service()
                ->get(new RequestFieldMatcher(
                    '/api/v1/book/'.$requestedId.'/recommendations',
                    RequestFieldMatcher::GLOB
                ))
                ->headerExact('Authorization', 'Bearer test')
                ->willReturn(Response::json([
                    'ts' => 12345,
                    'id' => $requestedId,
                    'recommendations' => [['id' => $book->getId()]],
                ]))
        );

        $this->client->request(Request::METHOD_GET, '/api/v1/book/123/recommendations');
        $responseContent = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertResponseIsSuccessful();
        $this->assertJsonDocumentMatchesSchema($responseContent, [
            'type' => 'object',
            'required' => ['items'],
            'properties' => [
                'items' => [
                    'type' => 'array',
                    'items' => [
                        'type' => 'object',
                        'required' => ['id', 'title', 'slug', 'image', 'shortDescription'],
                        'properties' => [
                            'id' => ['type' => 'integer'],
                            'title' => ['type' => 'string'],
                            'slug' => ['type' => 'string'],
                            'image' => ['type' => 'string'],
                            'shortDescription' => ['type' => 'string'],
                        ],
                    ],
                ],
            ],
        ]);
    }

    // Initialization HoverFly
    private function setUpHoverFly(): void
    {
        $this->hoverfly = new HoverflyClient(['base_uri' => $_ENV['HOVERFLY_API']]);
        $this->hoverfly->deleteJournal();
        $this->hoverfly->deleteSimulation();
    }
}
