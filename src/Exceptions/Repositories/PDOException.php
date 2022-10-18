<?php

declare(strict_types=1);

namespace Romanpravda\KcTestTask\Exceptions\Repositories;

use Exception;

/**
 * @codeCoverageIgnore
 */
final class PDOException extends Exception
{
    public static function create(string $code, int $resultCode, string $reason): self
    {
        return new self(sprintf('PDOException: SQLSTATE[%s] [%d] %s', $code, $resultCode, $reason));
    }
}