<?php

declare(strict_types=1);

namespace Romanpravda\KcTestTask\Support\PasswordHasher;

interface PasswordHasherInterface
{
    /**
     * Make hash.
     *
     * @param string $data
     *
     * @return string
     */
    public function makeHash(string $data): string;
}