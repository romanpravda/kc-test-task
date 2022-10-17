<?php

declare(strict_types=1);

namespace Tests\Unit\Support\PasswordHasher;

use PHPUnit\Framework\TestCase;
use Romanpravda\KcTestTask\Exceptions\PasswordHasher\WrongHashingAlgorithmException;
use Romanpravda\KcTestTask\Support\PasswordHasher\PasswordHasher;

final class PasswordHasherTest extends TestCase
{
    public function test_making_hash(): void
    {
        $tcs = [
            [
                'algo' => 'sha256',
                'data' => 'foobar',
            ],
            [
                'algo' => 'sha512',
                'data' => 'foobar',
            ],
            [
                'algo' => 'sha1',
                'data' => 'foobar',
            ],
        ];

        foreach ($tcs as $tc) {
            $this->assertEquals(
                hash($tc['algo'], $tc['data']),
                (new PasswordHasher($tc['algo']))->makeHash($tc['data']),
            );
        }
    }

    public function test_wrong_hash_algorithm(): void
    {
        $algo = 'foobar';

        $this->expectException(WrongHashingAlgorithmException::class);
        $this->expectDeprecationMessage(sprintf('Hashing algorithm "%s" is not valid. Available hashing algorithms - "%s".', $algo, implode('", "', hash_algos())));

        new PasswordHasher($algo);
    }
}