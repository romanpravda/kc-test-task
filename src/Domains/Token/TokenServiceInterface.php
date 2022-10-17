<?php

declare(strict_types=1);

namespace Romanpravda\KcTestTask\Domains\Token;

interface TokenServiceInterface
{
    /**
     * Issuing JWT token string for user with given ID.
     *
     * @param int $userId
     *
     * @return string
     *
     * @throws \Romanpravda\KcTestTask\Exceptions\Repositories\PDOException
     */
    public function issueTokenForUserId(int $userId): string;

    /**
     * Retrieving user's ID from JWT token string.
     *
     * @param string $jwt
     *
     * @return int
     *
     * @throws \Romanpravda\KcTestTask\Exceptions\Domains\Token\TokenNotFound
     * @throws \Romanpravda\KcTestTask\Exceptions\Repositories\PDOException
     */
    public function getUserIdFromToken(string $jwt): int;

    /**
     * Revoking JWT token from string.
     *
     * @param string $jwt
     *
     * @return bool
     *
     * @throws \Romanpravda\KcTestTask\Exceptions\Domains\Token\TokenNotFound
     * @throws \Romanpravda\KcTestTask\Exceptions\Repositories\PDOException
     */
    public function revokingToken(string $jwt): bool;
}