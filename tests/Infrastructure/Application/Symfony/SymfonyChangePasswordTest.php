<?php

namespace C201\Security\Tests\Infrastructure\Application\Symfony;

use C201\Ddd\Events\Domain\DomainEventTestTrait;
use C201\Ddd\Transactions\Application\TransactionManagerTestTrait;
use C201\Security\Infrastructure\Application\Symfony\SymfonyChangePassword;
use C201\Security\Infrastructure\Domain\Symfony\SymfonyUser;
use C201\Security\Domain\UserTestTrait;
use C201\Security\Infrastructure\Domain\Symfony\SymfonyUserTestTrait;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class SymfonyChangePasswordTest extends TestCase
{
    use ProphecyTrait;
    use TransactionManagerTestTrait;
    use DomainEventTestTrait;
    use SymfonyUserTestTrait;
    use UserTestTrait;

    /**
     * @var UserPasswordEncoderInterface|ObjectProphecy
     */
    private $encoder;

    private SymfonyChangePassword $fixture;

    protected function setUp(): void
    {
        $this->initTransactionManagerTestTrait();
        $this->initDomainEventTestTrait();
        $this->initSymfonyUserTestTrait();
        $this->encoder = $this->prophesize(UserPasswordEncoderInterface::class);

        $this->fixture = new SymfonyChangePassword(
            $this->transactionManager->reveal(),
            $this->eventRegistry->reveal(),
            $this->encoder->reveal(),
            $this->symfonyUserRepository->reveal()
        );
    }

    public function testEncodedPasswordIsSetToUserEventsAreDequeuedAndTransactionIsCommitted(): void
    {
        $email = $this->givenAnUserEmail();
        $password = $this->givenANewPassword();

        $this->givenTransactionIsBegun();
        $user = $this->givenASymfonyUserCanBeFoundByEmail($email);
        $newEncodedPassword = $this->givenPasswordIsEncoded($password);
        $this->thenPasswordShouldBeChangedForUser($user, $newEncodedPassword);
        $this->thenEventRegistryShouldDequeueAndRegister($user->reveal());
        $this->thenTransactionShouldBeCommitted();
        $this->thenTransactionShouldNotBeRolledBack();

        $this->whenSymfonyChangePasswordIsExecuted($email, $password);
    }

    private function givenANewPassword(): string
    {
        return uniqid();
    }

    private function givenPasswordIsEncoded(string $password): string
    {
        $encodedPassword = uniqid();
        $this->encoder->encodePassword(Argument::any(), $password)->willReturn($encodedPassword);
        return $encodedPassword;
    }

    /**
     * @param ObjectProphecy|SymfonyUser $user
     */
    private function thenPasswordShouldBeChangedForUser(ObjectProphecy $user, string $newEncodedPassword)
    {
        $user->changePassword($newEncodedPassword)->shouldBeCalled();
    }

    private function whenSymfonyChangePasswordIsExecuted(string $email, string $password): void
    {
        $this->fixture->execute($email, $password);
    }

    public function testTransactionIsRolledBackAndUserNotFoundExceptionIsThrownIfUserCanNotBeFound(): void
    {
        $email = $this->givenAnUserEmail();

        $this->givenTransactionIsBegun();
        $this->givenASymfonyUserCanNotBeFoundByEmail($email);
        $this->thenTransactionShouldBeRolledBack();
        $this->thenTransactionShouldNotBeCommitted();
        $this->thenUserNotFoundExceptionShouldBeThrown();

        $this->whenSymfonyChangePasswordIsExecuted($email, $this->givenANewPassword());
    }
}
