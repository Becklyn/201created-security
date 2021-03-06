<?php

namespace C201\Security\Application;

use C201\Security\Domain\User;

/**
 * @author Marko Vujnovic <mv@201created.de>
 * @since  2020-03-06
 */
interface Security
{
    public function getUser(): ?User;
}
