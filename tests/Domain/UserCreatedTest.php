<?php

namespace C201\Security\Tests\Domain;

use C201\Ddd\Events\Domain\DomainEventTestTrait;
use C201\Security\Domain\User;
use C201\Security\Domain\UserCreated;
use C201\Security\Domain\UserTestTrait;
use PHPUnit\Framework\TestCase;

class UserCreatedTest extends TestCase
{
    use UserTestTrait;
    use DomainEventTestTrait;

    public function testAggregateIdReturnsUserIdPassedToConstructor(): void
    {
        $userId = $this->givenAUserId();
        $event = new UserCreated($this->givenAnEventId(), $this->givenARaisedTs(), $userId, $this->givenAnUserEmail());
        $this->assertSame($userId, $event->aggregateId());
    }

    public function testEmailReturnsEmailPassedToConstructor(): void
    {
        $email = $this->givenAnUserEmail();
        $event = new UserCreated($this->givenAnEventId(), $this->givenARaisedTs(), $this->givenAUserId(), $email);
        $this->assertEquals($email, $event->email());
    }

    public function testAggregateTypeReturnsUserClass(): void
    {
        $event = new UserCreated($this->givenAnEventId(), $this->givenARaisedTs(), $this->givenAUserId(), $this->givenAnUserEmail());
        $this->assertEquals(User::class, $event->aggregateType());
    }
}
