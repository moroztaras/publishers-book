<?php

namespace App\Controller;

use App\Attribute\RequestBody;
use App\Model\SignUpRequest;
use App\Manager\SignUpManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;
use Nelmio\ApiDocBundle\Annotation\Model;
use App\Model\IdResponse;
use App\Model\ErrorResponse;

class AuthController extends AbstractController
{
    public function __construct(private SignUpManager $signUpManager)
    {
    }

    /**
     * @OA\Response(
     *     response=200,
     *     description="Signs up a user",
     *     @Model(type=IdResponse::class)
     * )
     * @OA\Response(
     *     response="409",
     *     description="User already exists",
     *     @Model(type=ErrorResponse::class)
     * )
     * @OA\Response(
     *     response="400",
     *     description="Validation failed",
     *     @Model(type=ErrorResponse::class)
     * )
     * @OA\RequestBody(@Model(type=SignUpRequest::class))
     */
    #[Route(path: '/api/v1/auth/signUp', methods: ['POST'])]
    public function signUp(#[RequestBody] SignUpRequest $signUpRequest): Response
    {
        return $this->json($this->signUpManager->signUp($signUpRequest));
    }
}
