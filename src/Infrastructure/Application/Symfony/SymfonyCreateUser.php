<?php

namespace C201\Security\Infrastructure\Application\Symfony;

use C201\Ddd\Events\Domain\EventRegistry;
use C201\Security\Application\CreateUser;
use C201\Security\Domain\UserId;
use C201\Security\Infrastructure\Domain\Doctrine\DoctrineSymfonyUser;
use C201\Security\Infrastructure\Domain\Symfony\SymfonyUserRepository;
use C201\Ddd\Transactions\Application\TransactionManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @author Marko Vujnovic <mv@201created.de>
 * @since  2020-03-03
 */
class SymfonyCreateUser implements CreateUser
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

    public function execute(string $email, string $plainPassword): UserId
    {
        $this->transactionManager->begin();

        try {
            $id = $this->userRepository->nextIdentity();
            $user = DoctrineSymfonyUser::create($id, $email, $plainPassword);
            $this->eventRegistry->dequeueProviderAndRegister($user);

            $encodedPassword = $this->encoder->encodePassword($user, $plainPassword);
            $user->changePassword($encodedPassword);

            $this->userRepository->add($user);
        } catch (\Exception $e) {
            $this->transactionManager->rollback();
            throw $e;
        }

        $this->transactionManager->commit();

        return $id;
    }
}
