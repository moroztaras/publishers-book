<?php

namespace App\Controller;

use App\Attribute\RequestBody;
use App\Manager\SubscriberManager;
use App\Model\ErrorResponse;
use App\Model\SubscriberRequest;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SubscribeController extends AbstractController
{
    public function __construct(private readonly SubscriberManager $subscriberManager)
    {
    }

    #[Route(path: '/api/v1/subscribe', methods: ['POST'])]
    #[OA\Tag(name: 'Subscriber API')]
    #[OA\Response(response: 200, description: 'Subscribe email to newsletter mailing list')]
    #[OA\Response(response: 400, description: 'Validation failed', attachables: [new Model(type: ErrorResponse::class)])]
    #[OA\RequestBody(attachables: [new Model(type: SubscriberRequest::class)])]
    public function subscribe(#[RequestBody] SubscriberRequest $subscriberRequest): Response
    {
        $this->subscriberManager->subscribe($subscriberRequest);

        return $this->json('Subscribe email to newsletter mailing list');
    }
}
