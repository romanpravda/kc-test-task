<?php

declare(strict_types=1);

namespace Romanpravda\KcTestTask\Domains\Token;

use Romanpravda\KcTestTask\Support\Criteria\Criteria;

interface TokenRepositoryInterface
{
    /**
     * Searching user with given ID.
     *
     * @param int $id
     *
     * @return \Romanpravda\KcTestTask\Domains\Token\Token|null
     *
     * @throws \Romanpravda\KcTestTask\Exceptions\Repositories\PDOException
     */
    public function findById(int $id): ?Token;

    /**
     * Saving token data.
     *
     * @param \Romanpravda\KcTestTask\Domains\Token\Token $token
     *
     * @return \Romanpravda\KcTestTask\Domains\Token\Token
     *
     * @throws \Romanpravda\KcTestTask\Exceptions\Repositories\PDOException
     */
    public function save(Token $token): Token;

    /**
     * Deleting token.
     *
     * @param \Romanpravda\KcTestTask\Domains\Token\Token $token
     *
     * @return bool
     *
     * @throws \Romanpravda\KcTestTask\Exceptions\Repositories\PDOException
     */
    public function delete(Token $token): bool;

    /**
     * Deleting tokens by criteria.
     *
     * @param \Romanpravda\KcTestTask\Support\Criteria\Criteria $criteria
     *
     * @return bool
     *
     * @throws \Romanpravda\KcTestTask\Exceptions\Repositories\PDOException
     * @throws \Romanpravda\KcTestTask\Exceptions\Repositories\NoFilterForDeleteException
     */
    public function deleteByCriteria(Criteria $criteria): bool;

}