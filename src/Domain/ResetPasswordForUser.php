<?php

namespace C201\Security\Domain;

use C201\Ddd\Events\Domain\EventRegistry;

/**
 * @author Marko Vujnovic <mv@201created.de>
 * @since  2020-04-29
 */
class ResetPasswordForUser
{
    private EventRegistry $eventRegistry;
    private EncodePasswordForUser $encodePasswordForUser;

    public function __construct(EventRegistry $eventRegistry, EncodePasswordForUser $encodePasswordForUser)
    {
        $this->eventRegistry = $eventRegistry;
        $this->encodePasswordForUser = $encodePasswordForUser;
    }

    public function execute(User $user, string $newPlainPassword): void
    {
        $newEncodedPassword = $this->encodePasswordForUser->execute($user, $newPlainPassword);
        $user->resetPassword($newEncodedPassword);
        $this->eventRegistry->dequeueProviderAndRegister($user);
    }
}
