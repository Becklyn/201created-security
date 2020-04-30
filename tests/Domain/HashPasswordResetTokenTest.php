<?php

namespace C201\Security\Tests\Domain;

use C201\Security\Domain\HashPasswordResetToken;
use C201\Security\Domain\UserTestTrait;
use PHPUnit\Framework\TestCase;

class HashPasswordResetTokenTest extends TestCase
{
    use UserTestTrait;

    public function testHashPasswordResetTokenReturnsSha1OfPlainToken(): void
    {
        $token = $this->givenAPasswordResetToken();
        $fixture = new HashPasswordResetToken();
        $this->assertEquals(sha1($token), $fixture->execute($token));
    }
}
