<?php

namespace C201\Security\Domain;

use C201\Ddd\Events\Domain\AbstractDomainEvent;
use C201\Ddd\Events\Domain\EventId;
use C201\Security\Domain\User;
use C201\Security\Domain\UserId;

/**
 * @author Marko Vujnovic <mv@201created.de>
 * @since  2020-04-14
 */
class UserCreated extends AbstractDomainEvent
{
    private UserId $userId;

    private string $email;

    public function __construct(EventId $id, \DateTimeImmutable $raisedTs, UserId $userId, string $email)
    {
        parent::__construct($id, $raisedTs);
        $this->userId = $userId;
        $this->email = $email;
    }

    public function aggregateId(): UserId
    {
        return $this->userId;
    }

    public function aggregateType(): string
    {
        return User::class;
    }

    public function email(): string
    {
        return $this->email;
    }
}
