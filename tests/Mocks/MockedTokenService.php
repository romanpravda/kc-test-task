<?php

declare(strict_types=1);

namespace Tests\Mocks;

use Romanpravda\KcTestTask\Domains\Token\TokenServiceInterface;
use Romanpravda\KcTestTask\Exceptions\Domains\Token\TokenNotFound;
use Tests\Traits\GeneratesRandomString;

final class MockedTokenService implements TokenServiceInterface
{
    use GeneratesRandomString;

    /**
     * The map of tokens and user's IDs for tokens.
     *
     * @var int[]
     */
    private array $usersByTokens = [];

    /**
     * Issuing JWT token string for user with given ID.
     *
     * @param int $userId
     *
     * @return string
     */
    public function issueTokenForUserId(int $userId): string
    {
        $token = $this->generateRandomString();

        $this->usersByTokens[$token] = $userId;

        return $token;
    }

    /**
     * Retrieving user's ID from JWT token string.
     *
     * @param string $jwt
     *
     * @return int
     */
    public function getUserIdFromToken(string $jwt): int
    {
        $userId = $this->usersByTokens[$jwt] ?? null;
        if (is_null($userId)) {
            throw TokenNotFound::create();
        }

        return $userId;
    }

    /**
     * Revoking JWT token from string.
     *
     * @param string $jwt
     *
     * @return bool
     */
    public function revokingToken(string $jwt): bool
    {
        unset($this->usersByTokens[$jwt]);

        return true;
    }
}