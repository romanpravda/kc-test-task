<?php

declare(strict_types=1);

namespace Romanpravda\KcTestTask\Infrastructure\Database\Repositories;

use DateTimeImmutable;
use PDO;
use Romanpravda\KcTestTask\Domains\User\User;
use Romanpravda\KcTestTask\Domains\User\UserFactory;
use Romanpravda\KcTestTask\Domains\User\UserRepositoryInterface;
use Romanpravda\KcTestTask\Exceptions\Repositories\NoFilterForDeleteException;
use Romanpravda\KcTestTask\Exceptions\Repositories\PDOException;
use Romanpravda\KcTestTask\Infrastructure\Database\Repositories\Traits\HasRetrievingDataByPDO;
use Romanpravda\KcTestTask\Support\Criteria\Criteria;

/**
 * @codeCoverageIgnore
 */
final class PDOUserRepository implements UserRepositoryInterface
{
    use HasRetrievingDataByPDO;

    /**
     * PDO connection to database.
     *
     * @var \PDO
     */
    private PDO $PDO;

    /**
     * User's factory.
     *
     * @var \Romanpravda\KcTestTask\Domains\User\UserFactory
     */
    private UserFactory $factory;

    /**
     * PDOUserRepository constructor.
     *
     * @param \PDO $PDO
     * @param \Romanpravda\KcTestTask\Domains\User\UserFactory $factory
     */
    public function __construct(PDO $PDO, UserFactory $factory)
    {
        $this->PDO = $PDO;
        $this->factory = $factory;
    }

    /**
     * Searching user with given ID.
     *
     * @param int $id
     *
     * @return \Romanpravda\KcTestTask\Domains\User\User|null
     *
     * @throws \Romanpravda\KcTestTask\Exceptions\Repositories\PDOException
     */
    public function findById(int $id): ?User
    {
        $sql = 'SELECT `user_id`, `email`, `username`, `password` FROM `users` WHERE `user_id` = :id';

        $user = $this->retrieveDataById($this->PDO, $sql, $id);
        if ($user === false) {
            return null;
        }

        return $this->makeUserData($user);
    }

    /**
     * Searching users matching the criteria.
     *
     * @param \Romanpravda\KcTestTask\Support\Criteria\Criteria $criteria
     *
     * @return \Romanpravda\KcTestTask\Domains\User\User[]
     *
     * @throws \Romanpravda\KcTestTask\Exceptions\Repositories\PDOException
     */
    public function findByCriteria(Criteria $criteria): array
    {
        $users = [];

        $sql = $this->applyCriteriaToSql($criteria, 'SELECT `user_id`, `email`, `username`, `password` FROM `users`');

        $usersData = $this->retrieveDataByCriteria($this->PDO, $sql, $criteria);
        foreach ($usersData as $user) {
            $users[] = $this->makeUserData($user);
        }

        return $users;
    }

    /**
     * Saving user data.
     *
     * @param \Romanpravda\KcTestTask\Domains\User\User $user
     *
     * @return \Romanpravda\KcTestTask\Domains\User\User
     *
     * @throws \Romanpravda\KcTestTask\Exceptions\Repositories\PDOException
     */
    public function save(User $user): User
    {
        $now = new DateTimeImmutable();

        $sql = 'INSERT INTO `users` (`email`, `username`, `password`, `created_at`, `created_at_timezone`, `updated_at`, `updated_at_timezone`) VALUES (:email, :username, :password, CONVERT_TZ(:created_at, :timezone, :utc), :timezone, CONVERT_TZ(:updated_at, :timezone, :utc), :timezone)';
        $statement = $this->PDO->prepare($sql);
        if ($statement === false) {
            $error = $this->PDO->errorInfo();
            throw PDOException::create($error[0], $error[1], $error[2]);
        }

        $res = $statement->execute([
            ':email' => $user->getEmail(),
            ':username' => $user->getUsername(),
            ':password' => $user->getPassword(),
            ':created_at' => $now->format('Y-m-d H:i:s'),
            ':updated_at' => $now->format('Y-m-d H:i:s'),
            ':timezone' => $now->format('P'),
            ':utc' => 'UTC',
        ]);

        if ($res === false) {
            $error = $statement->errorInfo();
            throw PDOException::create($error[0], $error[1], $error[2]);
        }

        $userId = $this->PDO->lastInsertId();
        if ($userId !== false) {
            $user->setId((int) $userId);
        }

        return $user;
    }

    /**
     * Updating user data.
     *
     * @param \Romanpravda\KcTestTask\Domains\User\User $user
     *
     * @return bool
     *
     * @throws \Romanpravda\KcTestTask\Exceptions\Repositories\PDOException
     */
    public function update(User $user): bool
    {
        $now = new DateTimeImmutable();

        $sql = 'UPDATE `users` SET `email` = :email, `username` = :username, `password` = :password, `updated_at` = CONVERT_TZ(:updated_at, :timezone, :utc), `updated_at_timezone` = :timezone WHERE `user_id` = :user_id';
        $statement = $this->PDO->prepare($sql);
        if ($statement === false) {
            $error = $this->PDO->errorInfo();
            throw PDOException::create($error[0], $error[1], $error[2]);
        }

        $res = $statement->execute([
            ':email' => $user->getEmail(),
            ':username' => $user->getUsername(),
            ':password' => $user->getPassword(),
            ':updated_at' => $now->format('Y-m-d H:i:s'),
            ':timezone' => $now->format('P'),
            ':utc' => 'UTC',
            ':user_id' => $user->getId(),
        ]);

        if ($res === false) {
            $error = $statement->errorInfo();
            throw PDOException::create($error[0], $error[1], $error[2]);
        }

        return $res;
    }

    /**
     * Deleting user.
     *
     * @param \Romanpravda\KcTestTask\Domains\User\User $user
     *
     * @return bool
     *
     * @throws \Romanpravda\KcTestTask\Exceptions\Repositories\PDOException
     */
    public function delete(User $user): bool
    {
        $sql = 'DELETE FROM `users` WHERE `user_id` = :user_id';
        $statement = $this->PDO->prepare($sql);
        if ($statement === false) {
            $error = $this->PDO->errorInfo();
            throw PDOException::create($error[0], $error[1], $error[2]);
        }

        $res = $statement->execute([
            ':user_id' => $user->getId(),
        ]);

        if ($res === false) {
            $error = $statement->errorInfo();
            throw PDOException::create($error[0], $error[1], $error[2]);
        }

        return $res;
    }

    /**
     * Deleting students by criteria.
     *
     * @param \Romanpravda\KcTestTask\Support\Criteria\Criteria $criteria
     *
     * @return bool
     *
     * @throws \Romanpravda\KcTestTask\Exceptions\Repositories\NoFilterForDeleteException
     * @throws \Romanpravda\KcTestTask\Exceptions\Repositories\PDOException
     */
    public function deleteByCriteria(Criteria $criteria): bool
    {
        if (!$criteria->hasFilter()) {
            throw NoFilterForDeleteException::create();
        }

        $sql = sprintf('DELETE FROM `users` WHERE %s', $criteria->getFilter()->getQuery());
        $statement = $this->PDO->prepare($sql);
        if ($statement === false) {
            $error = $this->PDO->errorInfo();
            throw PDOException::create($error[0], $error[1], $error[2]);
        }

        $res = $statement->execute($criteria->getFilter()->getValue());
        if ($res === false) {
            $error = $statement->errorInfo();
            throw PDOException::create($error[0], $error[1], $error[2]);
        }

        return $res;
    }

    /**
     * Creating student object with data from array.
     *
     * @param array $userData
     *
     * @return \Romanpravda\KcTestTask\Domains\User\User
     */
    private function makeUserData(array $userData): User
    {
        return $this->factory->create((int) $userData['user_id'], $userData['email'], $userData['username'], $userData['password']);
    }
}