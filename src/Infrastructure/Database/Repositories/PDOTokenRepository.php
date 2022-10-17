<?php

declare(strict_types=1);

namespace Romanpravda\KcTestTask\Infrastructure\Database\Repositories;

use DateTimeImmutable;
use PDO;
use Romanpravda\KcTestTask\Domains\Token\Token;
use Romanpravda\KcTestTask\Domains\Token\TokenRepositoryInterface;
use Romanpravda\KcTestTask\Exceptions\Repositories\NoFilterForDeleteException;
use Romanpravda\KcTestTask\Exceptions\Repositories\PDOException;
use Romanpravda\KcTestTask\Infrastructure\Database\Repositories\Traits\HasRetrievingDataByPDO;
use Romanpravda\KcTestTask\Support\Criteria\Criteria;

final class PDOTokenRepository implements TokenRepositoryInterface
{
    use HasRetrievingDataByPDO;

    /**
     * PDO connection to database.
     *
     * @var \PDO
     */
    private PDO $PDO;

    /**
     * PDOStudentRepository constructor.
     *
     * @param \PDO $PDO
     */
    public function __construct(PDO $PDO)
    {
        $this->PDO = $PDO;
    }

    /**
     * Searching user with given ID.
     *
     * @param int $id
     *
     * @return \Romanpravda\KcTestTask\Domains\Token\Token|null
     *
     * @throws \Romanpravda\KcTestTask\Exceptions\Repositories\PDOException
     */
    public function findById(int $id): ?Token
    {
        $sql = 'SELECT `token_id`, `user_id` FROM `tokens` WHERE `token_id` = :id';

        $token = $this->retrieveDataById($this->PDO, $sql, $id);
        if (is_null($token)) {
            return null;
        }

        return $this->makeTokenFromData($token);
    }

    /**
     * Saving token data.
     *
     * @param \Romanpravda\KcTestTask\Domains\Token\Token $token
     *
     * @return \Romanpravda\KcTestTask\Domains\Token\Token
     *
     * @throws \Romanpravda\KcTestTask\Exceptions\Repositories\PDOException
     */
    public function save(Token $token): Token
    {
        $now = new DateTimeImmutable();

        $sql = 'INSERT INTO `tokens` (`user_id`, `created_at`, `created_at_timezone`) VALUES (:user_id, CONVERT_TZ(:created_at, :timezone, :utc), :timezone)';
        $statement = $this->PDO->prepare($sql);

        $res = $statement->execute([
            ':user_id' => $token->getUserId(),
            ':created_at' => $now->format('Y-m-d H:i:s'),
            ':timezone' => $now->format('P'),
            ':utc' => 'UTC',
        ]);

        if ($res === false) {
            $error = $statement->errorInfo();
            throw PDOException::create($error[0], $error[1], $error[2]);
        }

        $tokenId = $this->PDO->lastInsertId();
        if ($tokenId !== false) {
            $token->setId((int) $tokenId);
        }

        return $token;
    }

    /**
     * Deleting token.
     *
     * @param \Romanpravda\KcTestTask\Domains\Token\Token $token
     *
     * @return bool
     *
     * @throws \Romanpravda\KcTestTask\Exceptions\Repositories\PDOException
     */
    public function delete(Token $token): bool
    {
        $sql = 'DELETE FROM `tokens` WHERE `token_id` = :token_id';
        $statement = $this->PDO->prepare($sql);

        $res = $statement->execute([
            ':token_id' => $token->getId(),
        ]);

        if ($res === false) {
            $error = $statement->errorInfo();
            throw PDOException::create($error[0], $error[1], $error[2]);
        }

        return $res;
    }

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
    public function deleteByCriteria(Criteria $criteria): bool
    {
        if (!$criteria->hasFilter()) {
            throw NoFilterForDeleteException::create();
        }

        $sql = sprintf('DELETE FROM `tokens` WHERE %s', $criteria->getFilter()->getQuery());

        $statement = $this->PDO->prepare($sql);

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
     * @param array $tokenData
     *
     * @return \Romanpravda\KcTestTask\Domains\Token\Token
     */
    private function makeTokenFromData(array $tokenData): Token
    {
        return new Token((int) $tokenData['token_id'], (int) $tokenData['user_id']);
    }
}