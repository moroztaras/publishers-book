<?php

namespace App\Manager\Recommendation;

use App\Manager\Recommendation\Exception\AccessDeniedException;
use App\Manager\Recommendation\Exception\RequestException;
use App\Manager\Recommendation\Model\RecommendationResponse;
use Symfony\Component\HttpClient\Exception\ClientException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class RecommendationApiManager
{
    public function __construct(
        private readonly HttpClientInterface $recommendationClient,
        private readonly SerializerInterface $serializer
    ) {
    }

    /**
     * @throws AccessDeniedException
     * @throws RequestException
     */
    public function getRecommendationsByBookId(int $bookId): RecommendationResponse
    {
        try {
            $response = $this->recommendationClient->request(Request::METHOD_GET, '/api/v1/book/'.$bookId.'/recommendations');

            return $this->serializer->deserialize(
                $response->getContent(),
                RecommendationResponse::class,
                JsonEncoder::FORMAT
            );
        } catch (\Throwable $ex) {
            if ($ex instanceof ClientException && Response::HTTP_FORBIDDEN === $ex->getCode()) {
                throw new AccessDeniedException($ex);
            }

            throw new RequestException($ex->getMessage(), $ex);
        }
    }
}
