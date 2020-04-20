<?php

namespace C201\Security\Infrastructure\Domain\Symfony;

use C201\Security\Domain\User;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @author Marko Vujnovic <mv@201created.de>
 * @since  2020-04-02
 */
interface SymfonyUser extends User, UserInterface
{
}
