<?php

namespace C201\Security\Tests\Domain;

use C201\Security\Domain\GeneratePasswordResetToken;
use PHPUnit\Framework\TestCase;

class GeneratePasswordResetTokenTest extends TestCase
{
    public function testGeneratePasswordResetTokenReturnsNonEmptyString(): void
    {
        $fixture = new GeneratePasswordResetToken();
        $this->assertNotEmpty($fixture->execute());
    }
}
