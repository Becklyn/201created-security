<?php

namespace C201\Security\Infrastructure\Application\Symfony;

use C201\Security\Application\Security;
use C201\Security\Infrastructure\Domain\Symfony\SymfonyUser;
use Symfony\Component\Security\Core\Security as BaseSymfonySecurity;

/**
 * @author Marko Vujnovic <mv@201created.de>
 * @since  2020-03-06
 */
class SymfonySecurity implements Security
{
    private BaseSymfonySecurity $security;

    public function __construct(BaseSymfonySecurity $security)
    {
        $this->security = $security;
    }

    public function getUser(): ?SymfonyUser
    {
        /** @var SymfonyUser | null $user */
        $user = $this->security->getUser();
        return $user;
    }
}
