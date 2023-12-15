<?php

namespace App\Tests\Security\Voter;

use App\Entity\User;
use App\Repository\BookRepository;
use App\Security\Voter\AuthorBookVoter;
use App\Tests\AbstractTestCase;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class AuthorBookVoterTest extends AbstractTestCase
{
    private BookRepository $bookRepository;

    private TokenInterface $token;

    private AuthorBookVoter $voter;

    protected function setUp(): void
    {
        parent::setUp();
        // Mock
        $this->bookRepository = $this->createMock(BookRepository::class);
        $this->token = $this->createMock(TokenInterface::class);

        $this->voter = new AuthorBookVoter($this->bookRepository);
    }

    // Test for check ACCESS_ABSTAIN
    public function testVoteNotSupports(): void
    {
        $this->token->expects($this->never())->method('getUser');

        $this->assertEquals(VoterInterface::ACCESS_ABSTAIN, $this->voter->vote($this->token, 1, ['test']));
    }

    public function testVote(): void
    {
        $this->vote(true, VoterInterface::ACCESS_GRANTED);
    }

    private function vote(bool $existsUserBookByIdResult, int $expectedAccess): void
    {
        $user = new User();

        // Set behavior for method - getUser
        $this->token->expects($this->once())
            ->method('getUser')
            ->willReturn($user);

        // Set behavior for method - existsUserBookById
        $this->bookRepository->expects($this->once())
            ->method('existsUserBookById')
            ->with(1, $user)
            ->willReturn($existsUserBookByIdResult);

        // Comparing the expected value with the actual returned value.
        $this->assertEquals($expectedAccess, $this->voter->vote($this->token, 1, [AuthorBookVoter::IS_AUTHOR]));
    }
}
