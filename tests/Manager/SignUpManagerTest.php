<?php

namespace App\Tests\Manager;

use App\Entity\User;
use App\Exception\UserAlreadyExistsException;
use App\Manager\SignUpManager;
use App\Model\SignUpRequest;
use App\Repository\UserRepository;
use App\Tests\AbstractTestCase;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Http\Authentication\AuthenticationSuccessHandler;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasher;

class SignUpManagerTest extends AbstractTestCase
{
    private UserPasswordHasher $hasher;

    private UserRepository $userRepository;

    private AuthenticationSuccessHandler $successHandler;

    protected function setUp(): void
    {
        parent::setUp();

        // Mock
        $this->hasher = $this->createMock(UserPasswordHasher::class);
        $this->userRepository = $this->createMock(UserRepository::class);
        $this->successHandler = $this->createMock(AuthenticationSuccessHandler::class);
    }

    // Testing when user already exists
    public function testSignUpUserAlreadyExists(): void
    {
        // Expect exception
        $this->expectException(UserAlreadyExistsException::class);

        // Set behavior and expects for method existsByEmail
        $this->userRepository->expects($this->once())
            ->method('existsByEmail')
            ->with('test@test.com')
            ->willReturn(true)
        ;

        // Run SignUpManager
        $this->createManager()->signUp((new SignUpRequest())->setEmail('test@test.com'));
    }

    // Testing for sign up success
    public function testSignUp(): void
    {
        $response = new Response();
        $expectedHasherUser = (new User())
            ->setRoles([User::ROLE_USER])
            ->setFirstName('TestFirstName')
            ->setLastName('TestLastName')
            ->setEmail('test@test.com')
        ;

        $expectedUser = clone $expectedHasherUser;
        $expectedUser->setPassword('hashed_password');

        // Set behavior and expects for method existsByEmail
        $this->userRepository->expects($this->once())
            ->method('existsByEmail')
            ->with('test@test.com')
            ->willReturn(false)
        ;

        // Set behavior and expects for method hashPassword
        $this->hasher->expects($this->once())
            ->method('hashPassword')
            ->with($expectedHasherUser, 'testtest')
            ->willReturn('hashed_password');

        // Mock method 'saveAndCommit'
        $this->userRepository->expects($this->once())
            ->method('saveAndCommit')
            ->with($expectedUser);

        // Set behavior and expects for method handleAuthenticationSuccess
        $this->successHandler->expects($this->once())
            ->method('handleAuthenticationSuccess')
            ->with($expectedUser)
            ->willReturn($response)
        ;

        // Create request
        $signUpRequest = (new SignUpRequest())
            ->setFirstName('TestFirstName')
            ->setLastName('TestLastName')
            ->setEmail('test@test.com')
            ->setPassword('testtest')
        ;
        // Comparing the expected value with the actual returned value.
        $this->assertEquals($response, $this->createManager()->signUp($signUpRequest));
    }

    // Helper for create manager
    private function createManager(): SignUpManager
    {
        return new SignUpManager($this->hasher, $this->userRepository, $this->successHandler);
    }
}
