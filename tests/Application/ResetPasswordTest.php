<?php

namespace C201\Security\Tests\Application;

use C201\Ddd\Transactions\Application\TransactionManagerTestTrait;
use C201\Security\Application\ResetPassword;
use C201\Security\Domain\ResetPasswordForUser;
use C201\Security\Domain\User;
use C201\Security\Domain\UserNotFoundException;
use C201\Security\Domain\UserTestTrait;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

class ResetPasswordTest extends TestCase
{
    use ProphecyTrait;
    use TransactionManagerTestTrait;
    use UserTestTrait;

    /**
     * @var ObjectProphecy|ResetPasswordForUser
     */
    private ObjectProphecy $resetPasswordForUser;
    private ResetPassword $fixture;

    protected function setUp(): void
    {
        $this->initTransactionManagerTestTrait();
        $this->initUserTestTrait();
        $this->resetPasswordForUser = $this->prophesize(ResetPasswordForUser::class);
        $this->fixture = new ResetPassword($this->transactionManager->reveal(), $this->userRepository->reveal(), $this->resetPasswordForUser->reveal());
    }

    public function testPasswordIsResetForUserAndTransactionCommitted(): void
    {
        $email = $this->givenAnUserEmail();
        $password = $this->givenAUserPassword();
        $this->givenTransactionIsBegun();
        $user = $this->givenAUserCanBeFoundByEmail($email);
        $this->thenPasswordShouldBeResetForUser($user->reveal(), $password);
        $this->thenTransactionShouldBeCommitted();
        $this->thenTransactionShouldNotBeRolledBack();
        $this->whenResetPasswordIsExecuted($email, $password);
    }

    private function thenPasswordShouldBeResetForUser(User $user, string $password): void
    {
        $this->resetPasswordForUser->execute($user, $password)->shouldBeCalled();
    }

    private function whenResetPasswordIsExecuted(string $email, string $password): void
    {
        $this->fixture->execute($email, $password);
    }

    public function testPasswordIsNotResetForUserTransactionIsRolledBackAndUserNotFoundExceptionIsThrownIfUserCanNotBeFound(): void
    {
        $email = $this->givenAnUserEmail();
        $password = $this->givenAUserPassword();
        $this->givenTransactionIsBegun();
        $this->givenAUserCanNotBeFoundByEmail($email);
        $this->thenPasswordShouldNotBeResetForUser();
        $this->thenTransactionShouldBeRolledBack();
        $this->thenTransactionShouldNotBeCommitted();
        $this->expectException(UserNotFoundException::class);
        $this->whenResetPasswordIsExecuted($email, $password);
    }

    private function thenPasswordShouldNotBeResetForUser(): void
    {
        $this->resetPasswordForUser->execute(Argument::any(), Argument::any())->shouldNotBeCalled();
    }
}
