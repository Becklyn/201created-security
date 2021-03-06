<?php

namespace C201\Security\Domain;

use C201\Ddd\Events\Domain\AbstractDomainEvent;
use C201\Ddd\Events\Domain\EventId;
use C201\Ddd\Identity\Domain\AggregateId;

/**
 * @author Marko Vujnovic <mv@201created.de>
 * @since  2020-04-20
 */
class UserEnabled extends AbstractDomainEvent
{
    private UserId $userId;

    public function __construct(EventId $id, \DateTimeImmutable $raisedTs, UserId $userId)
    {
        parent::__construct($id, $raisedTs);
        $this->userId = $userId;
    }

    public function aggregateId(): UserId
    {
        return $this->userId;
    }

    public function aggregateType(): string
    {
        return User::class;
    }
}
