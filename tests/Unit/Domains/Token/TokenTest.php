<?php

declare(strict_types=1);

namespace Tests\Unit\Domains\Token;

use PHPUnit\Framework\TestCase;
use Romanpravda\KcTestTask\Domains\Token\Token;

final class TokenTest extends TestCase
{
    public function test_token(): void
    {
        $userId = 2;

        $token = new Token(null, $userId);

        $this->assertNull(
            $token->getId()
        );
        $this->assertEquals(
            $userId,
            $token->getUserId(),
        );

        $id = 1;
        $token->setId($id);

        $this->assertEquals(
            $id,
            $token->getId(),
        );
    }
}