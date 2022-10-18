<?php

declare(strict_types=1);

namespace Romanpravda\KcTestTask\Infrastructure\Database\Repositories\Traits;

use PDO;
use Romanpravda\KcTestTask\Exceptions\Repositories\PDOException;
use Romanpravda\KcTestTask\Support\Criteria\Criteria;

/**
 * @codeCoverageIgnore
 */
trait HasRetrievingDataByPDO
{
    use HasCriteriaFilter;

    /**
     * Retrieving data by ID from database.
     *
     * @param PDO $PDO
     * @param string $sql
     * @param int $id
     *
     * @return array|null
     *
     * @throws \Romanpravda\KcTestTask\Exceptions\Repositories\PDOException
     */
    private function retrieveDataById(PDO $PDO, string $sql, int $id): ?array
    {
        $statement = $PDO->prepare($sql);
        if ($statement === false) {
            $error = $this->PDO->errorInfo();
            throw PDOException::create($error[0], $error[1], $error[2]);
        }

        $statement->bindParam(':id', $id, PDO::PARAM_INT);

        $res = $statement->execute();
        if ($res === false) {
            $error = $statement->errorInfo();
            throw PDOException::create($error[0], $error[1], $error[2]);
        }

        $data = $statement->fetch(PDO::FETCH_ASSOC);
        if ($data === false) {
            return null;
        }

        return $data;
    }

    /**
     * Retrieving data by criteria from database.
     *
     * @param \PDO $PDO
     * @param string $sql
     * @param \Romanpravda\KcTestTask\Support\Criteria\Criteria $criteria
     *
     * @return array
     *
     * @throws \Romanpravda\KcTestTask\Exceptions\Repositories\PDOException
     */
    private function retrieveDataByCriteria(PDO $PDO, string $sql, Criteria $criteria): array
    {
        $statement = $PDO->prepare($sql);
        if ($statement === false) {
            $error = $this->PDO->errorInfo();
            throw PDOException::create($error[0], $error[1], $error[2]);
        }

        $statement = $this->getValuesForQueryFromCriteria($statement, $criteria);

        $res = $statement->execute();
        if ($res === false) {
            $error = $statement->errorInfo();
            throw PDOException::create($error[0], $error[1], $error[2]);
        }

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }
}
