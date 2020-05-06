<?php

namespace C201\Security\Application;

use C201\Ddd\Transactions\Application\TransactionManager;
use C201\Security\Domain\CreateUser as DomainCreateUser;
use C201\Security\Domain\UserId;
use C201\Security\Domain\UserRepository;

/**
 * @author Marko Vujnovic <mv@201created.de>
 * @since  2020-03-03
 */
class CreateUser
{
    private TransactionManager $transactionManager;
    private UserRepository $userRepository;
    private DomainCreateUser $createUser;

    public function __construct(
        TransactionManager $transactionManager,
        UserRepository $userRepository,
        DomainCreateUser $createUser
    ) {
        $this->transactionManager = $transactionManager;
        $this->userRepository = $userRepository;
        $this->createUser = $createUser;
    }
    public function execute(string $email, string $plainPassword): UserId
    {
        $this->transactionManager->begin();

        try {
            $id = $this->userRepository->nextIdentity();
            $this->createUser->execute($id, $email, $plainPassword);
        } catch (\Exception $e) {
            $this->transactionManager->rollback();
            throw $e;
        }

        $this->transactionManager->commit();

        return $id;
    }
}
