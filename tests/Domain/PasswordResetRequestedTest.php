<?php

namespace C201\Security\Tests\Domain;

use C201\Ddd\Events\Domain\DomainEventTestTrait;
use C201\Security\Domain\PasswordResetRequested;
use C201\Security\Domain\User;
use C201\Security\Domain\UserTestTrait;
use PHPUnit\Framework\TestCase;

class PasswordResetRequestedTest extends TestCase
{
    use UserTestTrait;
    use DomainEventTestTrait;

    public function testTokenReturnsValuePassedToConstructor(): void
    {
        $token = $this->givenAPasswordResetToken();
        $event = new PasswordResetRequested($this->givenAnEventId(), $this->givenARaisedTs(), $this->givenAUserId(), $token, $this->givenAPasswordResetRequestTs());
        $this->assertEquals($token, $event->token());
    }

    public function testRequestTsReturnsValuePassedToConstructor(): void
    {
        $resetTs = $this->givenAPasswordResetRequestTs();
        $event = new PasswordResetRequested($this->givenAnEventId(), $this->givenARaisedTs(), $this->givenAUserId(), $this->givenAPasswordResetToken(), $resetTs);
        $this->assertEquals($resetTs, $event->requestTs());
    }

    public function testAggregateIdReturnsUserIdPassedToConstructor(): void
    {
        $userId = $this->givenAUserId();
        $event = new PasswordResetRequested($this->givenAnEventId(), $this->givenARaisedTs(), $userId, $this->givenAPasswordResetToken(), $this->givenAPasswordResetRequestTs());
        $this->assertSame($userId, $event->aggregateId());
    }

    public function testAggregateTypeReturnsUserClass(): void
    {
        $event = new PasswordResetRequested($this->givenAnEventId(), $this->givenARaisedTs(), $this->givenAUserId(), $this->givenAPasswordResetToken(), $this->givenAPasswordResetRequestTs());
        $this->assertEquals(User::class, $event->aggregateType());
    }
}
