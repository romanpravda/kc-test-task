<?php

declare(strict_types=1);

namespace Tests\Mocks;

use Romanpravda\KcTestTask\Domains\Token\Token;
use Romanpravda\KcTestTask\Domains\Token\TokenRepositoryInterface;
use Romanpravda\KcTestTask\Support\Criteria\Criteria;

final class MockedTokenRepository implements TokenRepositoryInterface
{
    /**
     * Tokens.
     *
     * @var \Romanpravda\KcTestTask\Domains\Token\Token[]
     */
    private array $tokens = [];

    /**
     * Next ID.
     *
     * @var int
     */
    private int $nextId = 1;

    /**
     * Searching user with given ID.
     *
     * @param int $id
     *
     * @return \Romanpravda\KcTestTask\Domains\Token\Token|null
     */
    public function findById(int $id): ?Token
    {
        return $this->tokens[$id] ?? null;
    }

    /**
     * Saving token data.
     *
     * @param \Romanpravda\KcTestTask\Domains\Token\Token $token
     *
     * @return \Romanpravda\KcTestTask\Domains\Token\Token
     */
    public function save(Token $token): Token
    {
        $id = $this->nextId;
        $token->setId($id);

        $this->tokens[$id] = $token;
        $this->nextId++;

        return $token;
    }

    /**
     * Deleting token.
     *
     * @param \Romanpravda\KcTestTask\Domains\Token\Token $token
     *
     * @return bool
     */
    public function delete(Token $token): bool
    {
        unset($this->tokens[$token->getId()]);

        return true;
    }

    /**
     * Deleting tokens by criteria.
     *
     * @param \Romanpravda\KcTestTask\Support\Criteria\Criteria $criteria
     *
     * @return bool
     */
    public function deleteByCriteria(Criteria $criteria): bool
    {
        $this->tokens = [];

        return true;
    }
}
