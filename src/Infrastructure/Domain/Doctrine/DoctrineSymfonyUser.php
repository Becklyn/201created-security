<?php

namespace C201\Security\Infrastructure\Domain\Doctrine;

use C201\Ddd\Events\Domain\EventProviderCapabilities;
use C201\Security\Domain\PasswordChanged;
use C201\Security\Domain\Role;
use C201\Security\Domain\RoleAddedToUser;
use C201\Security\Domain\RoleRemovedFromUser;
use C201\Security\Domain\UserCreated;
use C201\Security\Domain\User;
use C201\Security\Domain\UserDisabled;
use C201\Security\Domain\UserEnabled;
use C201\Security\Domain\UserId;
use C201\Security\Infrastructure\Domain\Symfony\SymfonyUser;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Tightenco\Collect\Support\Collection;

/**
 * @author Marko Vujnovic <mv@201created.de>
 * @since  2020-04-02
 *
 * @ORM\Entity
 * @ORM\Table(
 *     name="c201_users",
 *     uniqueConstraints={
 *          @ORM\UniqueConstraint(name="uniq_user_uuid", columns={"uuid"}),
 *          @ORM\UniqueConstraint(name="uniq_user_email", columns={"email"})
 *     }
 * )
 */
class DoctrineSymfonyUser implements SymfonyUser
{
    use EventProviderCapabilities;

    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * Internal ids must be nullable otherwise Doctrine breaks when deleting records
     */
    protected ?int $internalId = null;

    /**
     * @ORM\Column(name="uuid", type="string", length=36, unique=true, nullable=false)
     */
    protected string $id;

    /**
     * @ORM\Column(type="string", length=255, unique=true, nullable=false)
     */
    protected string $email;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    protected string $password;

    /**
     * @ORM\Column(type="boolean")
     */
    protected bool $enabled = true;

    /**
     * @ORM\Column(type="json")
     */
    protected array $roles = [];

    /**
     * @ORM\Column(type="datetime_immutable", nullable=false)
     * @Gedmo\Timestampable(on="create")
     */
    protected \DateTimeImmutable $createdTs;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=false)
     * @Gedmo\Timestampable(on="update")
     */
    protected \DateTimeImmutable $updatedTs;

    protected function __construct()
    {
    }

    public static function create(UserId $id, string $email, string $password): self
    {
        $user = new static();
        $user->id = $id->asString();
        $user->email = $email;
        $user->password = $password;
        $user->raiseEvent(new UserCreated($user->nextEventIdentity(), new \DateTimeImmutable(), $id, $email));
        return $user;
    }

    public function id(): UserId
    {
        return UserId::fromString($this->id);
    }

    public function email(): string
    {
        return $this->email;
    }

    public function roles(): Collection
    {
        return Collection::make($this->getRoles());
    }

    public function hasRole(string $role): bool
    {
        return $this->roles()->containsStrict(strtoupper($role));
    }

    public function addRole(string $role): self
    {
        $role = strtoupper($role);
        if ($role === Role::DEFAULT) {
            return $this;
        }

        if (!$this->hasRole($role)) {
            $this->roles[] = $role;
            $this->raiseEvent(new RoleAddedToUser($this->nextEventIdentity(), new \DateTimeImmutable(), $this->id(), $role));
        }

        return $this;
    }

    public function removeRole(string $role): self
    {
        $role = strtoupper($role);
        if (false !== $key = array_search($role, $this->roles, true)) {
            unset($this->roles[$key]);
            $this->roles = array_values($this->roles);
            $this->raiseEvent(new RoleRemovedFromUser($this->nextEventIdentity(), new \DateTimeImmutable(), $this->id(), $role));
        }

        return $this;
    }

    public function changePassword(string $newPassword): User
    {
        $this->password = $newPassword;
        $this->raiseEvent(new PasswordChanged($this->nextEventIdentity(), new \DateTimeImmutable(), $this->id()));
        return $this;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function enable(): self
    {
        if (!$this->enabled) {
            $this->enabled = true;
            $this->raiseEvent(new UserEnabled($this->nextEventIdentity(), new \DateTimeImmutable(), $this->id()));
        }
        return $this;
    }

    public function disable(): self
    {
        if ($this->enabled) {
            $this->enabled = false;
            $this->raiseEvent(new UserDisabled($this->nextEventIdentity(), new \DateTimeImmutable(), $this->id()));
        }
        return $this;
    }

    /**
     * Returns the roles granted to the user.
     *
     *     public function getRoles()
     *     {
     *         return ['ROLE_USER'];
     *     }
     *
     * Alternatively, the roles might be stored on a ``roles`` property,
     * and populated in any number of different ways when the user object
     * is created.
     *
     * @return   (Role|string)[] The user roles
     * @internal Required by Symfony
     */
    public function getRoles()
    {
        // ensure that the user always has the default user role
        return array_values(array_unique(array_merge($this->roles, [Role::DEFAULT])));
    }

    /**
     * Returns the password used to authenticate the user.
     *
     * This should be the encoded password. On authentication, a plain-text
     * password will be salted, encoded, and then compared to this value.
     *
     * @return string|null The encoded password if any
     * @internal Required by Symfony
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Returns the salt that was originally used to encode the password.
     *
     * This can return null if the password was not encoded using a salt.
     *
     * @return string|null The salt
     * @internal Required by Symfony
     */
    public function getSalt()
    {
        return null;
    }

    /**
     * Returns the username used to authenticate the user.
     *
     * @return string The username
     * @internal Required by Symfony
     */
    public function getUsername()
    {
        return $this->email;
    }

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     *
     * @internal Required by Symfony
     */
    public function eraseCredentials()
    {
    }
}
