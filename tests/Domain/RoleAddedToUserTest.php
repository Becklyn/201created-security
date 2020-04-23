<?php

namespace C201\Security\Tests\Domain;

use C201\Ddd\Events\Domain\DomainEventTestTrait;
use C201\Security\Domain\RoleAddedToUser;
use C201\Security\Domain\User;
use C201\Security\Domain\UserTestTrait;
use PHPUnit\Framework\TestCase;

class RoleAddedToUserTest extends TestCase
{
    use UserTestTrait;
    use DomainEventTestTrait;

    public function testRoleReturnsRolePassedToConstructor(): void
    {
        $role = 'ROLE_TEST';
        $event = new RoleAddedToUser($this->givenAnEventId(), $this->givenARaisedTs(), $this->givenAUserId(), $role);
        $this->assertEquals($role, $event->role());
    }

    public function testAggregateIdReturnsUserIdPassedToConstructor(): void
    {
        $userId = $this->givenAUserId();
        $event = new RoleAddedToUser($this->givenAnEventId(), $this->givenARaisedTs(), $userId, uniqid());
        $this->assertSame($userId, $event->aggregateId());
    }

    public function testAggregateTypeReturnsUserClass(): void
    {
        $event = new RoleAddedToUser($this->givenAnEventId(), $this->givenARaisedTs(), $this->givenAUserId(), uniqid());
        $this->assertEquals(User::class, $event->aggregateType());
    }
}
