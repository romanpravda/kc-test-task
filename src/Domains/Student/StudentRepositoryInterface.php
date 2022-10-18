<?php

declare(strict_types=1);

namespace Romanpravda\KcTestTask\Domains\Student;

use Romanpravda\KcTestTask\Support\Criteria\Criteria;

interface StudentRepositoryInterface
{
    /**
     * Searching student with given ID.
     *
     * @param int $id
     *
     * @return \Romanpravda\KcTestTask\Domains\Student\Student|null
     *
     * @throws \Romanpravda\KcTestTask\Exceptions\Repositories\PDOException
     */
    public function findById(int $id): ?Student;

    /**
     * Searching students matching the criteria.
     *
     * @param \Romanpravda\KcTestTask\Support\Criteria\Criteria $criteria
     *
     * @return array
     *
     * @throws \Romanpravda\KcTestTask\Exceptions\Repositories\PDOException
     */
    public function findByCriteria(Criteria $criteria): array;

    /**
     * Saving student data.
     *
     * @param \Romanpravda\KcTestTask\Domains\Student\Student $student
     *
     * @return \Romanpravda\KcTestTask\Domains\Student\Student
     *
     * @throws \Romanpravda\KcTestTask\Exceptions\Repositories\PDOException
     */
    public function save(Student $student): Student;

    /**
     * Updating student data.
     *
     * @param \Romanpravda\KcTestTask\Domains\Student\Student $student
     *
     * @return bool
     *
     * @throws \Romanpravda\KcTestTask\Exceptions\Repositories\PDOException
     */
    public function update(Student $student): bool;

    /**
     * Deleting student.
     *
     * @param \Romanpravda\KcTestTask\Domains\Student\Student $student
     *
     * @return bool
     *
     * @throws \Romanpravda\KcTestTask\Exceptions\Repositories\PDOException
     */
    public function delete(Student $student): bool;

    /**
     * Deleting students by criteria.
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
