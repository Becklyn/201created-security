<?php

namespace C201\Security\Domain;

/**
 * @author Marko Vujnovic <mv@201created.de>
 * @since  2020-04-27
 */
class HashPasswordResetToken
{
    public function execute(string $plainToken): string
    {
        return sha1($plainToken);
    }
}
