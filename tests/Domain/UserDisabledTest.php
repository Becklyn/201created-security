<?php

namespace C201\Security\Tests\Domain;

use C201\Ddd\Events\Domain\DomainEventTestTrait;
use C201\Security\Domain\User;
use C201\Security\Domain\UserDisabled;
use C201\Security\Domain\UserTestTrait;
use PHPUnit\Framework\TestCase;

class UserDisabledTest extends TestCase
{
    use UserTestTrait;
    use DomainEventTestTrait;

    public function testAggregateIdReturnsUserIdPassedToConstructor(): void
    {
        $userId = $this->givenAUserId();
        $event = new UserDisabled($this->givenAnEventId(), $this->givenARaisedTs(), $userId);
        $this->assertSame($userId, $event->aggregateId());
    }

    public function testAggregateTypeReturnsUserClass(): void
    {
        $event = new UserDisabled($this->givenAnEventId(), $this->givenARaisedTs(), $this->givenAUserId());
        $this->assertEquals(User::class, $event->aggregateType());
    }
}
