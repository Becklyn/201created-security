<?php

namespace C201\Security\Tests\Domain;

use C201\Ddd\Events\Domain\DomainEventTestTrait;
use C201\Security\Domain\RoleRemovedFromUser;
use C201\Security\Domain\User;
use C201\Security\Domain\UserTestTrait;
use PHPUnit\Framework\TestCase;

class RoleRemovedFromUserTest extends TestCase
{
    use UserTestTrait;
    use DomainEventTestTrait;

    public function testRoleReturnsRolePassedToConstructor(): void
    {
        $role = 'ROLE_TEST';
        $event = new RoleRemovedFromUser($this->givenAnEventId(), $this->givenARaisedTs(), $this->givenAUserId(), $role);
        $this->assertEquals($role, $event->role());
    }

    public function testAggregateIdReturnsUserIdPassedToConstructor(): void
    {
        $userId = $this->givenAUserId();
        $event = new RoleRemovedFromUser($this->givenAnEventId(), $this->givenARaisedTs(), $userId, uniqid());
        $this->assertSame($userId, $event->aggregateId());
    }

    public function testAggregateTypeReturnsUserClass(): void
    {
        $event = new RoleRemovedFromUser($this->givenAnEventId(), $this->givenARaisedTs(), $this->givenAUserId(), uniqid());
        $this->assertEquals(User::class, $event->aggregateType());
    }
}
