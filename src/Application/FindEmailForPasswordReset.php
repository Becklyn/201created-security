<?php

namespace C201\Security\Application;

use C201\Security\Domain\FindUserForPasswordReset;
use C201\Security\Domain\PasswordResetExpiredException;
use C201\Security\Domain\UserNotFoundException;

/**
 * @author Marko Vujnovic <mv@201created.de>
 * @since  2020-04-29
 */
class FindEmailForPasswordReset
{
    private FindUserForPasswordReset $findUser;
    private int $tokenExpirationMinutes;

    public function __construct(FindUserForPasswordReset $findUser, int $tokenExpirationMinutes)
    {
        $this->findUser = $findUser;
        $this->tokenExpirationMinutes = $tokenExpirationMinutes;
    }

    /**
     * @throws PasswordResetExpiredException
     * @throws UserNotFoundException
     */
    public function execute(string $passwordResetToken): string
    {
        return $this->findUser->execute($passwordResetToken, $this->tokenExpirationMinutes)->email();
    }
}
