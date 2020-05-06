<?php

namespace C201\Security\Infrastructure\Domain\Doctrine;

use C201\Security\Domain\User;
use C201\Security\Domain\UserFactory;
use C201\Security\Domain\UserId;

/**
 * @author Marko Vujnovic <mv@201created.de>
 * @since  2020-04-30
 */
class DoctrineSymfonyUserFactory implements UserFactory
{
    public function create(UserId $id, string $email, string $password): User
    {
        return DoctrineSymfonyUser::create($id, $email, $password);
    }
}
