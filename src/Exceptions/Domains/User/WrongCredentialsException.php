<?php

declare(strict_types=1);

namespace Romanpravda\KcTestTask\Exceptions\Domains\User;

use Exception;

final class WrongCredentialsException extends Exception
{
    public static function create(): self
    {
        return new self('Wrong username or password');
    }
}