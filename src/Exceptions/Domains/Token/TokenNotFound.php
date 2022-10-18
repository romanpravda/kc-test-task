<?php

declare(strict_types=1);

namespace Romanpravda\KcTestTask\Exceptions\Domains\Token;

use Exception;

final class TokenNotFound extends Exception
{
    public static function create(): self
    {
        return new self('Authentication token not found.');
    }
}
