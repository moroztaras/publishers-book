<?php

namespace App\Controller;

use App\Attribute\RequestBody;
use App\Manager\SubscriberManager;
use App\Model\SubscriberRequest;
use App\Service\SubscriberService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;
use Nelmio\ApiDocBundle\Annotation\Model;
use App\Model\ErrorResponse;

class SubscribeController extends AbstractController
{
    public function __construct(
        private SubscriberManager $subscriberManager
    ) {
    }

    /**
     * @OA\Response(
     *     response=200,
     *     description="Subscribe email to newsletter mailing list"
     * )
     * @OA\Response(
     *     response="400",
     *     description="Validation failed",
     *     @Model(type=ErrorResponse::class)
     * )
     * @OA\RequestBody(@Model(type=SubscriberRequest::class))
     */
    #[Route(path: '/api/v1/subscribe', methods: ['POST'])]
    public function subscribe(#[RequestBody] SubscriberRequest $subscriberRequest): Response
    {
        $this->subscriberManager->subscribe($subscriberRequest);

        return $this->json(null);
    }
}
