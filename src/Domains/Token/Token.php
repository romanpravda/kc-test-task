<?php

declare(strict_types=1);

namespace Romanpravda\KcTestTask\Domains\Token;

final class Token
{
    /**
     * Token's ID.
     *
     * @var int|null
     */
    private ?int $id;

    /**
     * User's id for token.
     *
     * @var int
     */
    private int $userId;

    /**
     * Token constructor.
     *
     * @param int|null $id
     * @param int $userId
     */
    public function __construct(?int $id, int $userId)
    {
        $this->id = $id;
        $this->setUserId($userId);
    }

    /**
     * Retrieving token's ID.
     *
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Setting token's ID.
     *
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * Retrieving user's ID for token.
     *
     * @return int
     */
    public function getUserId(): int
    {
        return $this->userId;
    }

    /**
     * Setting user's ID for token.
     *
     * @param int $userId
     */
    public function setUserId(int $userId): void
    {
        $this->userId = $userId;
    }
}
