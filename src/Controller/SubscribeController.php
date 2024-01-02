<?php

namespace App\Controller;

use App\Attribute\RequestBody;
use App\Manager\SubscriberManager;
use App\Model\ErrorResponse;
use App\Model\SubscriberRequest;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SubscribeController extends AbstractController
{
    public function __construct(private readonly SubscriberManager $subscriberManager)
    {
    }

    /**
     * @OA\Tag(name="Subscriber API")
     *
     * @OA\Response(
     *     response=200,
     *     description="Subscribe email to newsletter mailing list"
     * )
     * @OA\Response(
     *     response="400",
     *     description="Validation failed",
     *
     *     @Model(type=ErrorResponse::class)
     * )
     *
     * @OA\RequestBody(@Model(type=SubscriberRequest::class))
     */
    #[Route(path: '/api/v1/subscribe', methods: ['POST'])]
    public function subscribe(#[RequestBody] SubscriberRequest $subscriberRequest): Response
    {
        $this->subscriberManager->subscribe($subscriberRequest);

        return $this->json('Subscribe email to newsletter mailing list');
    }
}
