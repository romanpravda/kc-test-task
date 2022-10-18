<?php

declare(strict_types=1);

namespace Tests\Mocks;

use Romanpravda\KcTestTask\Domains\User\User;
use Romanpravda\KcTestTask\Domains\User\UserRepositoryInterface;
use Romanpravda\KcTestTask\Support\Criteria\Criteria;

final class MockedUserRepository implements UserRepositoryInterface
{
    /**
     * Users.
     *
     * @var \Romanpravda\KcTestTask\Domains\User\User[]
     */
    private array $users = [];

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
     * @return \Romanpravda\KcTestTask\Domains\User\User|null
     */
    public function findById(int $id): ?User
    {
        return $this->tokens[$id] ?? null;
    }

    /**
     * Searching users matching the criteria.
     *
     * @param \Romanpravda\KcTestTask\Support\Criteria\Criteria $criteria
     *
     * @return \Romanpravda\KcTestTask\Domains\User\User[]
     */
    public function findByCriteria(Criteria $criteria): array
    {
        if ($criteria->hasFilter() || stripos($criteria->getFilter()->getQuery(), '`username`') !== false) {
            $key = array_key_first($criteria->getFilter()->getValue());
            $value = $criteria->getFilter()->getValue()[$key];
            return array_values(array_filter($this->users, static fn (User $user) => $user->getUsername() === $value));
        }

        return array_values($this->users);
    }

    /**
     * Saving user data.
     *
     * @param \Romanpravda\KcTestTask\Domains\User\User $user
     *
     * @return \Romanpravda\KcTestTask\Domains\User\User
     */
    public function save(User $user): User
    {
        $id = $this->nextId;
        $user->setId($id);

        $this->users[$id] = $user;
        $this->nextId++;

        return $user;
    }

    /**
     * Updating user data.
     *
     * @param \Romanpravda\KcTestTask\Domains\User\User $user
     *
     * @return bool
     */
    public function update(User $user): bool
    {
        $this->users[$user->getId()] = $user;

        return true;
    }

    /**
     * Deleting user.
     *
     * @param \Romanpravda\KcTestTask\Domains\User\User $user
     *
     * @return bool
     */
    public function delete(User $user): bool
    {
        unset($this->users[$user->getId()]);

        return true;
    }

    /**
     * Deleting users by criteria.
     *
     * @param \Romanpravda\KcTestTask\Support\Criteria\Criteria $criteria
     *
     * @return bool
     */
    public function deleteByCriteria(Criteria $criteria): bool
    {
        $this->users = [];

        return true;
    }
}