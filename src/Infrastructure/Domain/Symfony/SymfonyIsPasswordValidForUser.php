<?php

namespace C201\Security\Infrastructure\Domain\Symfony;

use C201\Security\Domain\IsPasswordValidForUser;
use C201\Security\Domain\User;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @author Marko Vujnovic <mv@201created.de>
 * @since  2020-04-24
 */
class SymfonyIsPasswordValidForUser implements IsPasswordValidForUser
{
    private UserPasswordEncoderInterface $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    /**
     * @param SymfonyUser $user
     */
    public function execute(User $user, string $plainPassword): bool
    {
        return $this->encoder->isPasswordValid($user, $plainPassword);
    }
}
