<?php

namespace App\Tests\Manager\Recommendation;

use App\Manager\Recommendation\Exception\AccessDeniedException;
use App\Manager\Recommendation\Exception\RequestException;
use App\Manager\Recommendation\RecommendationApiManager;
use App\Tests\AbstractTestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;

class RecommendationApiManagerTest extends AbstractTestCase
{
    private SerializerInterface $serializer;

    protected function setUp(): void
    {
        parent::setUp();

        // Mock
        $this->serializer = $this->createMock(SerializerInterface::class);
    }

    public function dataProvider(): array
    {
        return [
            [Response::HTTP_FORBIDDEN, AccessDeniedException::class],
            [Response::HTTP_CONFLICT, RequestException::class],
        ];
    }

    /**
     * @dataProvider dataProvider
     */
    public function testGetRecommendationsByBookId(int $responseCode, string $exceptionClass): void
    {
        // Expect exception
        $this->expectException($exceptionClass);

        // Create client
        $httpClient = new MockHttpClient(
            new MockResponse('', ['http_code' => $responseCode]),
            'http://localhost/',
        );

        // Run getRecommendationsByBookId method
        (new RecommendationApiManager($httpClient, $this->serializer))->getRecommendationsByBookId(1);
    }
}
