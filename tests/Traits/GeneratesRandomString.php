<?php

declare(strict_types=1);

namespace Tests\Traits;

trait GeneratesRandomString
{
    /**
     * Generating a random string.
     *
     * @param int $length
     * @param string $keyspace
     *
     * @return string
     *
     * @throws \Exception
     */
    private function generateRandomString(int $length = 64, string $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'): string
    {
        if ($length < 1) {
            throw new \RangeException("Length must be a positive integer");
        }
        $pieces = [];
        $max = mb_strlen($keyspace, '8bit') - 1;
        for ($i = 0; $i < $length; ++$i) {
            $pieces []= $keyspace[random_int(0, $max)];
        }
        return implode('', $pieces);
    }
}