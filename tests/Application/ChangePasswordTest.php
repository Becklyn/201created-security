<?php

namespace C201\Security\Tests\Application;

use C201\Ddd\Transactions\Application\TransactionManagerTestTrait;
use C201\Security\Application\ChangePassword;
use C201\Security\Domain\ChangePasswordForUser;
use C201\Security\Domain\User;
use C201\Security\Domain\UserTestTrait;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

class ChangePasswordTest extends TestCase
{
    use ProphecyTrait;
    use TransactionManagerTestTrait;
    use UserTestTrait;

    /**
     * @var ObjectProphecy|ChangePasswordForUser
     */
    private ObjectProphecy $changePasswordForUser;

    private ChangePassword $fixture;

    protected function setUp(): void
    {
        $this->initTransactionManagerTestTrait();
        $this->initUserTestTrait();
        $this->changePasswordForUser = $this->prophesize(ChangePasswordForUser::class);

        $this->fixture = new ChangePassword(
            $this->transactionManager->reveal(),
            $this->userRepository->reveal(),
            $this->changePasswordForUser->reveal()
        );
    }

    public function testPasswordIsChangedForUserAndTransactionIsCommitted(): void
    {
        $email = $this->givenAnUserEmail();
        $password = $this->givenAUserPassword();

        $this->givenTransactionIsBegun();
        $user = $this->givenAUserCanBeFoundByEmail($email);
        $this->thenPasswordShouldBeChangedForUser($user, $password);
        $this->thenTransactionShouldBeCommitted();
        $this->thenTransactionShouldNotBeRolledBack();

        $this->whenChangePasswordIsExecuted($email, $password);
    }

    /**
     * @param ObjectProphecy|User $user
     */
    private function thenPasswordShouldBeChangedForUser(ObjectProphecy $user, string $newPassword): void
    {
        $this->changePasswordForUser->execute($user->reveal(), $newPassword)->shouldBeCalled();
    }

    private function whenChangePasswordIsExecuted(string $email, string $password): void
    {
        $this->fixture->execute($email, $password);
    }

    public function testTransactionIsRolledBackAndUserNotFoundExceptionIsThrownIfUserCanNotBeFound(): void
    {
        $email = $this->givenAnUserEmail();

        $this->givenTransactionIsBegun();
        $this->givenAUserCanNotBeFoundByEmail($email);
        $this->thenTransactionShouldBeRolledBack();
        $this->thenTransactionShouldNotBeCommitted();
        $this->thenUserNotFoundExceptionShouldBeThrown();

        $this->whenChangePasswordIsExecuted($email, $this->givenAUserPassword());
    }
}
