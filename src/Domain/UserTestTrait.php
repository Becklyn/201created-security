<?php

namespace C201\Security\Domain;

use C201\Security\Domain\UserId;
use C201\Security\Domain\UserNotFoundException;

/**
 * @author Marko Vujnovic <mv@201created.de>
 * @since  2020-04-15
 */
trait UserTestTrait
{
    protected function givenAUserId(): UserId
    {
        return UserId::next();
    }

    protected function givenAnUserEmail(): string
    {
        return uniqid();
    }

    protected function thenUserNotFoundExceptionShouldBeThrown(): void
    {
        $this->expectException(UserNotFoundException::class);
    }
}
