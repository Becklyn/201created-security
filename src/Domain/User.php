<?php

namespace C201\Security\Domain;

use C201\Ddd\Events\Domain\EventProvider;
use Tightenco\Collect\Support\Collection;

/**
 * @author Marko Vujnovic <mv@201created.de>
 * @since  2020-03-02
 */
interface User extends EventProvider
{
    public function id(): UserId;

    public function email(): string;

    public function isEnabled(): bool;

    public function enable(): self;

    public function disable(): self;

    public function roles(): Collection;

    public function hasRole(string $role): bool;

    public function addRole(string $role): self;

    public function removeRole(string $role): self;

    public function changePassword(string $newPassword): self;
}
