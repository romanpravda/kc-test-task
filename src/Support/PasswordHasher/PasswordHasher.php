<?php

declare(strict_types=1);

namespace Romanpravda\KcTestTask\Support\PasswordHasher;

use Romanpravda\KcTestTask\Exceptions\PasswordHasher\WrongHashingAlgorithmException;

final class PasswordHasher implements PasswordHasherInterface
{
    /**
     * Hashing algorithm.
     *
     * @var string
     */
    private string $algo;

    /**
     * PasswordHasher constructor.
     *
     * @param string $algo
     *
     * @throws \Romanpravda\KcTestTask\Exceptions\PasswordHasher\WrongHashingAlgorithmException
     */
    public function __construct(string $algo = 'sha256')
    {
        $this->setHashingAlgorithmIfValid($algo);
    }

    /**
     * Make hash.
     *
     * @param string $data
     *
     * @return string
     */
    public function makeHash(string $data): string
    {
        return hash($this->algo, $data);
    }

    /**
     * Setting hashing algorithm if it's valid.
     *
     * @param string $algo
     *
     * @return void
     *
     * @throws \Romanpravda\KcTestTask\Exceptions\PasswordHasher\WrongHashingAlgorithmException
     */
    private function setHashingAlgorithmIfValid(string $algo): void
    {
        $this->checkIfHashingAlgorithmIsValid($algo);

        $this->algo = $algo;
    }

    /**
     * Check for valid hashing algorithm.
     *
     * @param string $algo
     *
     * @throws \Romanpravda\KcTestTask\Exceptions\PasswordHasher\WrongHashingAlgorithmException
     */
    private function checkIfHashingAlgorithmIsValid(string $algo): void
    {
        if (!in_array($algo, hash_algos(), true)) {
            throw WrongHashingAlgorithmException::create($algo, hash_algos());
        }
    }
}
