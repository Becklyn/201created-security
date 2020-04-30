<?php

namespace C201\Security\Tests\Domain;

use C201\Security\Domain\HashPasswordResetToken;
use C201\Security\Domain\UserTestTrait;
use PHPUnit\Framework\TestCase;

class HashPasswordResetTokenTest extends TestCase
{
    use UserTestTrait;

    public function testHashPasswordResetTokenReturnsSaltedSha1OfPlainToken(): void
    {
        $token = $this->givenAPasswordResetToken();
        $salt = uniqid();
        $fixture = new HashPasswordResetToken($salt);
        $this->assertEquals(sha1($salt . $token), $fixture->execute($token));
    }
}
