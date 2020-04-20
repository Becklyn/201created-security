<?php

namespace C201\Security\Application;

use C201\Security\Domain\UserNotFoundException;

/**
 * @author Marko Vujnovic <mv@201created.de>
 * @since  2020-03-03
 */
interface ChangePassword
{
    /**
     * @throws UserNotFoundException
     */
    public function execute(string $email, string $newPlainPassword): void;
}
