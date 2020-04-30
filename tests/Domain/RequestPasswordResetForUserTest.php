<?php

namespace C201\Security\Tests\Domain;

use C201\Ddd\Events\Domain\DomainEventTestTrait;
use C201\Security\Domain\HashPasswordResetToken;
use C201\Security\Domain\RequestPasswordResetForUser;
use C201\Security\Domain\User;
use C201\Security\Domain\UserTestTrait;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

class RequestPasswordResetForUserTest extends TestCase
{
    use ProphecyTrait;
    use DomainEventTestTrait;
    use UserTestTrait;

    /**
     * @var ObjectProphecy|HashPasswordResetToken
     */
    private ObjectProphecy $hashPasswordResetToken;

    private RequestPasswordResetForUser $fixture;

    protected function setUp(): void
    {
        $this->initDomainEventTestTrait();
        $this->hashPasswordResetToken = $this->prophesize(HashPasswordResetToken::class);
        $this->fixture = new RequestPasswordResetForUser($this->eventRegistry->reveal(), $this->hashPasswordResetToken->reveal());
    }

    public function testTokenIsHashedResetRequestedWithHashedTokenAndEventsDequeuedFromUser(): void
    {
        $user = $this->givenAUser();
        $token = $this->givenAPasswordResetToken();

        $hashedToken = $this->givenTokenIsHashed($token);
        $this->thenPasswordResetShouldBeRequestedForUser($user, $hashedToken);
        $this->thenEventRegistryShouldDequeueAndRegister($user->reveal());
        $this->whenRequestPasswordResetForUserIsExecuted($user->reveal(), $token);
    }

    private function givenTokenIsHashed(string $token): string
    {
        $hashedToken = uniqid();
        $this->hashPasswordResetToken->execute($token)->willReturn($hashedToken);
        return $hashedToken;
    }

    /**
     * @param ObjectProphecy|User $user
     */
    private function thenPasswordResetShouldBeRequestedForUser(ObjectProphecy $user, string $hashedToken): void
    {
        $user->requestPasswordReset($hashedToken)->shouldBeCalled();
    }

    private function whenRequestPasswordResetForUserIsExecuted(User $user, string $token)
    {
        $this->fixture->execute($user, $token);
    }
}
