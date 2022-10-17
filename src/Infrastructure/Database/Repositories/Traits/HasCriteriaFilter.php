<?php

declare(strict_types=1);

namespace Romanpravda\KcTestTask\Infrastructure\Database\Repositories\Traits;

use Romanpravda\KcTestTask\Support\Criteria\Criteria;
use Romanpravda\KcTestTask\Support\Criteria\OrderType;

trait HasCriteriaFilter
{
    /**
     * Applying criteria to SQL.
     *
     * @param \Romanpravda\KcTestTask\Support\Criteria\Criteria $criteria
     * @param string $sql
     *
     * @return string
     */
    private function applyCriteriaToSql(Criteria $criteria, string $sql): string
    {
        if ($criteria->hasFilter()) {
            $sql .= sprintf(' WHERE %s', $criteria->getFilter()->getQuery());
        }

        switch ($criteria->getOrder()->getOrderType()) {
            case OrderType::ASC:
                $sql .= sprintf(' ORDER BY `%s` ASC', $criteria->getOrder()->getOrderBy());
                break;
            case OrderType::DESC:
                $sql .= sprintf(' ORDER BY `%s` DESC', $criteria->getOrder()->getOrderBy());
                break;
            default:
                break;
        }

        if (!is_null($criteria->getLimit())) {
            $sql .= ' LIMIT :limit';

            if (!is_null($criteria->getOffset())) {
                $sql .= ' OFFSET :offset';
            }
        }

        return $sql;
    }

    /**
     * Applying criteria to PDO Statement.
     *
     * @param \PDOStatement $statement
     * @param \Romanpravda\KcTestTask\Support\Criteria\Criteria $criteria
     *
     * @return \PDOStatement
     */
    private function getValuesForQueryFromCriteria(\PDOStatement $statement, Criteria $criteria): \PDOStatement
    {
        if ($criteria->hasFilter()) {
            foreach ($criteria->getFilter()->getValue() as $key => $value) {
                $type = \PDO::PARAM_STR;
                $statement->bindValue($key, $value, $type);
            }
        }

        if (!is_null($criteria->getLimit())) {
            $statement->bindValue(':limit', $criteria->getLimit(), \PDO::PARAM_INT);

            if (!is_null($criteria->getOffset())) {
                $statement->bindValue(':offset', $criteria->getOffset(), \PDO::PARAM_INT);
            }
        }

        return $statement;
    }
}