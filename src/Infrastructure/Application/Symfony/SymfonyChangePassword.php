<?php

namespace C201\Security\Infrastructure\Application\Symfony;

use C201\Ddd\Events\Domain\EventRegistry;
use C201\Security\Application\ChangePassword;
use C201\Security\Infrastructure\Domain\Symfony\SymfonyUserRepository;
use C201\Ddd\Transactions\Application\TransactionManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @author Marko Vujnovic <mv@201created.de>
 * @since  2020-03-03
 */
class SymfonyChangePassword implements ChangePassword
{
    private TransactionManager $transactionManager;

    private EventRegistry $eventRegistry;

    private UserPasswordEncoderInterface $encoder;

    private SymfonyUserRepository $userRepository;

    public function __construct(
        TransactionManager $transactionManager,
        EventRegistry $eventRegistry,
        UserPasswordEncoderInterface $encoder,
        SymfonyUserRepository $userRepository
    ) {
        $this->transactionManager = $transactionManager;
        $this->eventRegistry = $eventRegistry;
        $this->encoder = $encoder;
        $this->userRepository = $userRepository;
    }

    /**
     * @inheritDoc
     */
    public function execute(string $email, string $newPlainPassword): void
    {
        $this->transactionManager->begin();

        try {
            $user = $this->userRepository->findOneByEmail($email);
            $newEncodedPassword = $this->encoder->encodePassword($user, $newPlainPassword);
            $user->changePassword($newEncodedPassword);
            $this->eventRegistry->dequeueProviderAndRegister($user);
        } catch (\Exception $e) {
            $this->transactionManager->rollback();
            throw $e;
        }

        $this->transactionManager->commit();
    }
}
