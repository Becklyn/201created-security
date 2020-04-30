<?php

namespace C201\Security\Tests\Infrastructure\Domain\Doctrine;

use C201\Security\Domain\UserId;
use C201\Security\Domain\UserNotFoundException;
use C201\Security\Infrastructure\Domain\Doctrine\DoctrineSymfonyUser;
use C201\Security\Infrastructure\Domain\Doctrine\DoctrineSymfonyUserRepository;
use C201\Security\Domain\UserTestTrait;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * @author Marko Vujnovic <mv@201created.de>
 * @since  2020-04-16
 */
class DoctrineSymfonyUserRepositoryTest extends TestCase
{
    use ProphecyTrait;
    use UserTestTrait;

    /**
     * @var EntityManagerInterface|ObjectProphecy
     */
    private $entityManager;

    /**
     * @var EntityRepository|ObjectProphecy
     */
    private $doctrineRepository;

    private DoctrineSymfonyUserRepository $fixture;

    protected function setUp(): void
    {
        $this->entityManager = $this->prophesize(EntityManagerInterface::class);
        $this->doctrineRepository = $this->prophesize(EntityRepository::class);
        $this->entityManager->getRepository(DoctrineSymfonyUser::class)->willReturn($this->doctrineRepository->reveal());
        $this->fixture = new DoctrineSymfonyUserRepository($this->entityManager->reveal());
    }

    public function testNextIdentityReturnsAUserId(): void
    {
        $this->assertInstanceOf(UserId::class, $this->fixture->nextIdentity());
    }

    public function testAddPersistsUserInEntityManager(): void
    {
        $user = DoctrineSymfonyUser::create($this->givenAUserId(), uniqid(), uniqid());
        $this->entityManager->persist($user)->shouldBeCalled();
        $this->fixture->add($user);
    }

    public function testFindOneByEmailReturnsUserFoundByDoctrineRepository(): void
    {
        $email = $this->givenAnUserEmail();
        $user = DoctrineSymfonyUser::create($this->givenAUserId(), $email, uniqid());
        $this->doctrineRepository->findOneBy(['email' => $email])->willReturn($user);
        $this->assertSame($user, $this->fixture->findOneByEmail($email));
    }

    public function testFindOneByEmailThrowsUserNotFoundExceptionIfDoctrineRepositoryReturnsNull(): void
    {
        $email = $this->givenAnUserEmail();
        $this->doctrineRepository->findOneBy(['email' => $email])->willReturn(null);
        $this->expectException(UserNotFoundException::class);
        $this->fixture->findOneByEmail($email);
    }

    public function testFindOneByPasswordResetTokenReturnsUserFoundByDoctrineRepository(): void
    {
        $token = $this->givenAPasswordResetToken();
        $user = DoctrineSymfonyUser::create($this->givenAUserId(), $this->givenAnUserEmail(), uniqid());
        $this->doctrineRepository->findOneBy(['passwordResetToken' => $token])->willReturn($user);
        $this->assertSame($user, $this->fixture->findOneByPasswordResetToken($token));
    }

    public function testFindOneByPasswordResetTokenThrowsUserNotFoundExceptionIfDoctrineRepositoryReturnsNull(): void
    {
        $token = $this->givenAPasswordResetToken();
        $this->doctrineRepository->findOneBy(['passwordResetToken' => $token])->willReturn(null);
        $this->expectException(UserNotFoundException::class);
        $this->fixture->findOneByPasswordResetToken($token);
    }
}
