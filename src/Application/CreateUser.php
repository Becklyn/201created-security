<?php

namespace C201\Security\Application;

use C201\Security\Domain\UserId;

/**
 * @author Marko Vujnovic <mv@201created.de>
 * @since  2020-03-03
 */
interface CreateUser
{
    public function execute(string $email, string $rawPassword): UserId;
}
