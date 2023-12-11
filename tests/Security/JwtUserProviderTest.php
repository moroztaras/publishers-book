<?php

namespace App\Tests\Security;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Security\JwtUserProvider;
use App\Tests\AbstractTestCase;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;

class JwtUserProviderTest extends AbstractTestCase
{
    const EMAIL = 'test@test.com';

    private UserRepository $userRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userRepository = $this->createMock(UserRepository::class);
    }

    public function testSupportsClass(): void
    {
        // Create user
        $user = (new User())->setEmail(self::EMAIL);
        // Create JwtUserProvider
        $provider = new JwtUserProvider($this->userRepository);

        // Set the behavior & expected for the method - findOneBy
        $this->userRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['email' => self::EMAIL])
            ->willReturn($user);

        // Expected value
        $expected = $provider->loadUserByIdentifier(self::EMAIL);

        // Comparing the actual returned value with the expected value.
        $this->assertEquals($user, $expected);
    }

    public function testLoadUserByIdentifierNotFoundException(): void
    {
        $this->expectException(UserNotFoundException::class);

        $this->userRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['email' => self::EMAIL])
            ->willReturn(null);

        (new JwtUserProvider($this->userRepository))->loadUserByIdentifier(self::EMAIL);
    }
}
