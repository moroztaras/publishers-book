<?php

namespace App\Manager;

use App\Model\SignUpRequest;
use Symfony\Component\HttpFoundation\Response;

interface SignUpManagerInterface
{
    public function signUp(SignUpRequest $signUpRequest): Response;
}
