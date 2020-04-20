<?php

namespace C201\Security\Tests\Infrastructure\Domain\Doctrine;

use C201\Ddd\Events\Domain\DomainEvent;
use C201\Security\Domain\PasswordChanged;
use C201\Security\Domain\UserCreated;
use C201\Security\Infrastructure\Domain\Doctrine\DoctrineSymfonyUser;
use C201\Security\Domain\UserTestTrait;
use PHPUnit\Framework\TestCase;
use Tightenco\Collect\Support\Collection;

/**
 * @author Marko Vujnovic <mv@201created.de>
 * @since  2020-04-16
 */
class DoctrineSymfonyUserTest extends TestCase
{
    use UserTestTrait;

    public function testCreate(): void
    {
        $id = $this->givenAUserId();
        $email = $this->givenAnUserEmail();
        $password = uniqid();
        $user = DoctrineSymfonyUser::create($id, $email, $password);
        $this->assertTrue($id->equals($user->id()));
        $this->assertEquals($email, $user->email());
        $this->assertEquals($password, $user->getPassword());

        $events = Collection::make($user->dequeueEvents());
        $this->assertEquals(1, $events->count());
        $this->assertTrue(
            $events->contains(fn(DomainEvent $event) => $event instanceof UserCreated && $event->aggregateId()->equals($id) && $event->email() === $email)
        );
    }

    public function testChangePassword(): void
    {
        $originalPassword = uniqid();
        $user = DoctrineSymfonyUser::create($this->givenAUserId(), $this->givenAnUserEmail(), $originalPassword);
        $user->dequeueEvents();

        $newPassword = uniqid();
        $this->assertNotEquals($originalPassword, $newPassword);
        $user->changePassword($newPassword);
        $this->assertEquals($newPassword, $user->getPassword());
        $events = Collection::make($user->dequeueEvents());
        $this->assertEquals(1, $events->count());
        $this->assertTrue($events->contains(fn(DomainEvent $event) => $event instanceof PasswordChanged && $event->aggregateId()->equals($user->id())));
    }

    public function testSymfonyUserInterfaceFeatures(): void
    {
        $email = $this->givenAnUserEmail();
        $user = DoctrineSymfonyUser::create($this->givenAUserId(), $email, uniqid());
        $this->assertEquals(['ROLE_USER'], $user->getRoles());
        $this->assertNull($user->getSalt());
        $this->assertEquals($email, $user->getUsername());
    }
}
