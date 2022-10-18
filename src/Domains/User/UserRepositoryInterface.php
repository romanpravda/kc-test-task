<?php

declare(strict_types=1);

namespace Romanpravda\KcTestTask\Domains\User;

use Romanpravda\KcTestTask\Support\Criteria\Criteria;

interface UserRepositoryInterface
{
    /**
     * Searching user with given ID.
     *
     * @param int $id
     *
     * @return \Romanpravda\KcTestTask\Domains\User\User|null
     *
     * @throws \Romanpravda\KcTestTask\Exceptions\Repositories\PDOException
     */
    public function findById(int $id): ?User;

    /**
     * Searching users matching the criteria.
     *
     * @param \Romanpravda\KcTestTask\Support\Criteria\Criteria $criteria
     *
     * @return \Romanpravda\KcTestTask\Domains\User\User[]
     *
     * @throws \Romanpravda\KcTestTask\Exceptions\Repositories\PDOException
     */
    public function findByCriteria(Criteria $criteria): array;

    /**
     * Saving user data.
     *
     * @param \Romanpravda\KcTestTask\Domains\User\User $user
     *
     * @return \Romanpravda\KcTestTask\Domains\User\User
     *
     * @throws \Romanpravda\KcTestTask\Exceptions\Repositories\PDOException
     */
    public function save(User $user): User;

    /**
     * Updating user data.
     *
     * @param \Romanpravda\KcTestTask\Domains\User\User $user
     *
     * @return bool
     *
     * @throws \Romanpravda\KcTestTask\Exceptions\Repositories\PDOException
     */
    public function update(User $user): bool;

    /**
     * Deleting user.
     *
     * @param \Romanpravda\KcTestTask\Domains\User\User $user
     *
     * @return bool
     *
     * @throws \Romanpravda\KcTestTask\Exceptions\Repositories\PDOException
     */
    public function delete(User $user): bool;

    /**
     * Deleting users by criteria.
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
