<?php

namespace C201\Security\Infrastructure\Domain\Doctrine;

use C201\Security\Domain\User;
use C201\Security\Domain\UserId;
use C201\Security\Domain\UserNotFoundException;
use C201\Security\Infrastructure\Domain\Symfony\SymfonyUserRepository;
use Ramsey\Uuid\Uuid;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

/**
 * @author Marko Vujnovic <mv@201created.de>
 * @since  2020-03-03
 */
class DoctrineSymfonyUserRepository implements SymfonyUserRepository
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var EntityRepository
     */
    private $repository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->repository = $entityManager->getRepository(DoctrineSymfonyUser::class);
    }

    public function nextIdentity(): UserId
    {
        return UserId::fromString(Uuid::uuid4());
    }

    /**
     * @inheritDoc
     * @param DoctrineSymfonyUser $user
     */
    public function add(User $user): void
    {
        $this->entityManager->persist($user);
    }

    /**
     * @inheritDoc
     */
    public function findOneByEmail(string $email): DoctrineSymfonyUser
    {
        /** @var DoctrineSymfonyUser $user */
        $user = $this->repository->findOneBy(['email' => $email]);

        if (!$user) {
            throw new UserNotFoundException("User $email not found");
        }

        return $user;
    }
}
