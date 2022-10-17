<?php

declare(strict_types=1);

namespace Romanpravda\KcTestTask\Domains\Student\Exceptions;

use Exception;

final class StudentNotFoundException extends Exception
{
    public static function create(int $id): self
    {
        return new self(sprintf('Student with ID %d not found.', $id));
    }
}