<?php

declare(strict_types=1);

namespace Romanpravda\KcTestTask\Exceptions\Repositories;

use Exception;

/**
 * @codeCoverageIgnore
 */
final class NoFilterForDeleteException extends Exception
{
    public static function create(): self
    {
        return new self('There is no filters in criteria for delete data.');
    }
}
