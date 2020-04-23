<?php

namespace C201\Security\Domain;

use Prophecy\Prophecy\ObjectProphecy;

/**
 * @author Marko Vujnovic <mv@201created.de>
 * @since  2020-04-15
 */
trait UserTestTrait
{
    /**
     * @var UserRepository|ObjectProphecy
     */
    protected $userRepository;

    protected function initUserTestTrait(): void
    {
        $this->userRepository = $this->prophesize(UserRepository::class);
    }

    protected function givenAUserId(): UserId
    {
        return UserId::next();
    }

    protected function givenAnUserEmail(): string
    {
        return uniqid();
    }

    protected function givenAUserPassword(): string
    {
        return uniqid();
    }

    protected function thenUserNotFoundExceptionShouldBeThrown(): void
    {
        $this->expectException(UserNotFoundException::class);
    }

    /**
     * @return ObjectProphecy|User
     */
    protected function givenAUserCanBeFoundByEmail(string $email): ObjectProphecy
    {
        /** @var User|ObjectProphecy $user */
        $user = $this->prophesize(User::class);
        $user->email()->willReturn($email);
        $this->userRepository->findOneByEmail($email)->willReturn($user);
        return $user;
    }

    protected function givenAUserCanNotBeFoundByEmail(string $email): void
    {
        $this->userRepository->findOneByEmail($email)->willThrow(new UserNotFoundException());
    }

    /**
     * @return ObjectProphecy|User
     */
    protected function givenAUser(): ObjectProphecy
    {
        return $this->prophesize(User::class);
    }

    /**
     * @param ObjectProphecy|User $user
     */
    protected function thenPasswordShouldBeChangedForUser(ObjectProphecy $user, string $password): void
    {
        $user->changePassword($password)->shouldBeCalled();
    }
}
