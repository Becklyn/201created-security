<?php

namespace C201\Security\Infrastructure\Domain\Symfony;

use C201\Security\Domain\UserId;
use C201\Security\Domain\UserNotFoundException;
use C201\Security\Infrastructure\Domain\Symfony\SymfonyUser;
use C201\Security\Infrastructure\Domain\Symfony\SymfonyUserRepository;
use C201\Security\Tests\Infrastructure\Domain\Symfony\Argument;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * @author Marko Vujnovic <mv@201created.de>
 * @since  2020-04-15
 */
trait SymfonyUserTestTrait
{
    /**
     * @var SymfonyUserRepository|ObjectProphecy
     */
    protected $symfonyUserRepository;

    protected function initSymfonyUserTestTrait(): void
    {
        $this->symfonyUserRepository = $this->prophesize(SymfonyUserRepository::class);
    }

    /**
     * @return ObjectProphecy|SymfonyUser
     */
    protected function givenASymfonyUserCanBeFoundByEmail(string $email): ObjectProphecy
    {
        /** @var SymfonyUser|ObjectProphecy $user */
        $user = $this->prophesize(SymfonyUser::class);
        $user->email()->willReturn($email);
        $this->symfonyUserRepository->findOneByEmail($email)->willReturn($user);
        return $user;
    }

    protected function givenASymfonyUserCanNotBeFoundByEmail(string $email)
    {
        $this->symfonyUserRepository->findOneByEmail($email)->willThrow(new UserNotFoundException());
    }

    protected function givenSymfonyUserRepositoryGeneratesAUserId(): UserId
    {
        $userId = UserId::next();
        $this->symfonyUserRepository->nextIdentity()->willReturn($userId);
        return $userId;
    }

    /**
     * @param SymfonyUser|Argument
     */
    protected function thenSymfonyUserShouldBeAddedToRepository($user): void
    {
        $this->symfonyUserRepository->add($user)->shouldBeCalled();
    }

    /**
     * @return ObjectProphecy|SymfonyUser
     */
    protected function givenASymfonyUser(): ObjectProphecy
    {
        return $this->prophesize(SymfonyUser::class);
    }
}
