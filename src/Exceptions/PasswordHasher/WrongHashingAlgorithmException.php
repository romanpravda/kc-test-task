<?php

declare(strict_types=1);

namespace Romanpravda\KcTestTask\Exceptions\PasswordHasher;

use Exception;

final class WrongHashingAlgorithmException extends Exception
{
    public static function create(string $algo, array $validAlgos): self
    {
        return new self(sprintf('Hashing algorithm "%s" is not valid. Available hashing algorithms - "%s".', $algo, implode('", "', $validAlgos)));
    }
}