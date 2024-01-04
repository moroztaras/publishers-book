<?php

namespace App\Tests\Security;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Security\JwtUserProvider;
use App\Tests\AbstractTestCase;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;

class JwtUserProviderTest extends AbstractTestCase
{
    final public const EMAIL = 'test@test.com';

    private UserRepository $userRepository;

    private User $user;

    private JwtUserProvider $provider;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userRepository = $this->createMock(UserRepository::class);
        // Create user
        $this->user = (new User())->setEmail(self::EMAIL);
        // Create JwtUserProvider
        $this->provider = new JwtUserProvider($this->userRepository);
    }

    public function testSupportsClass(): void
    {
        // Set the behavior & expected for the method - findOneBy
        $this->userRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['email' => self::EMAIL])
            ->willReturn($this->user);

        // Expected value
        $expected = $this->provider->loadUserByIdentifier(self::EMAIL);

        // Comparing the actual returned value with the expected value.
        $this->assertEquals($this->user, $expected);
    }

    public function testLoadUserByIdentifierNotFoundException(): void
    {
        $this->expectException(UserNotFoundException::class);

        // Set behavior for method - findOneBy
        $this->userRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['email' => self::EMAIL])
            ->willReturn(null);

        // Run provider
        (new JwtUserProvider($this->userRepository))->loadUserByIdentifier(self::EMAIL);
    }

    public function testLoadUserByIdentifierAndPayload(): void
    {
        // Set behavior for method - findOneBy
        $this->userRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['id' => '1'])
            ->willReturn($this->user);

        // Comparing the actual returned value with the expected value.
        $this->assertEquals($this->user, $this->provider->loadUserByIdentifierAndPayload(self::EMAIL, ['id' => 1]));
    }

    public function testLoadUserByIdentifierAndPayloadNotFoundException(): void
    {
        $this->expectException(UserNotFoundException::class);

        // Set behavior for method - findOneBy
        $this->userRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['id' => '1'])
            ->willReturn(null);

        // Run provider
        (new JwtUserProvider($this->userRepository))->loadUserByIdentifierAndPayload(self::EMAIL, ['id' => 1]);
    }
}
