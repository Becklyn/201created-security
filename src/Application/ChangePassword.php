<?php

namespace C201\Security\Application;

use C201\Ddd\Transactions\Application\TransactionManager;
use C201\Security\Domain\ChangePasswordForUser;
use C201\Security\Domain\UserNotFoundException;
use C201\Security\Domain\UserRepository;

/**
 * @author Marko Vujnovic <mv@201created.de>
 * @since  2020-03-03
 */
class ChangePassword
{
    private TransactionManager $transactionManager;

    private UserRepository $userRepository;

    private ChangePasswordForUser $changePasswordForUser;

    public function __construct(
        TransactionManager $transactionManager,
        UserRepository $userRepository,
        ChangePasswordForUser $changePasswordForUser
    ) {
        $this->transactionManager = $transactionManager;
        $this->userRepository = $userRepository;
        $this->changePasswordForUser = $changePasswordForUser;
    }

    /**
     * @throws UserNotFoundException
     */
    public function execute(string $email, string $newPlainPassword): void
    {
        $this->transactionManager->begin();

        try {
            $user = $this->userRepository->findOneByEmail($email);
            $this->changePasswordForUser->execute($user, $newPlainPassword);
        } catch (\Exception $e) {
            $this->transactionManager->rollback();
            throw $e;
        }

        $this->transactionManager->commit();
    }
}
