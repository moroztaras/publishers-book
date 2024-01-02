<?php

namespace App\Manager;

use App\Entity\User;
use App\Exception\UserAlreadyExistsException;
use App\Model\SignUpRequest;
use App\Repository\UserRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Http\Authentication\AuthenticationSuccessHandler;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class SignUpManager implements SignUpManagerInterface
{
    public function __construct(
        private readonly UserPasswordHasherInterface $hasher,
        private readonly UserRepository $userRepository,
        private readonly AuthenticationSuccessHandler $successHandler
    ) {
    }

    public function signUp(SignUpRequest $signUpRequest): Response
    {
        // Check email of user
        if ($this->userRepository->existsByEmail($signUpRequest->getEmail())) {
            throw new UserAlreadyExistsException();
        }

        // Create new user
        $user = (new User())
            ->setFirstName($signUpRequest->getFirstName())
            ->setLastName($signUpRequest->getLastName())
            ->setEmail($signUpRequest->getEmail())
            ->setRoles([User::ROLE_USER]);

        // Set user password
        $user->setPassword($this->hasher->hashPassword($user, $signUpRequest->getPassword()));

        $this->userRepository->saveAndCommit($user);

        return $this->successHandler->handleAuthenticationSuccess($user);
    }
}
