<?php

namespace C201\Security\Domain;

/**
 * @author Marko Vujnovic <mv@201created.de>
 * @since  2020-04-30
 */
interface UserFactory
{
    public function create(UserId $id, string $email, string $password): User;
}
