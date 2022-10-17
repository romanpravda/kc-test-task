<?php

declare(strict_types=1);

namespace Romanpravda\KcTestTask\Infrastructure\Database\Repositories;

use DateTimeImmutable;
use PDO;
use Romanpravda\KcTestTask\Domains\Student\Student;
use Romanpravda\KcTestTask\Domains\Student\StudentRepositoryInterface;
use Romanpravda\KcTestTask\Exceptions\Repositories\NoFilterForDeleteException;
use Romanpravda\KcTestTask\Exceptions\Repositories\PDOException;
use Romanpravda\KcTestTask\Infrastructure\Database\Repositories\Traits\HasRetrievingDataByPDO;
use Romanpravda\KcTestTask\Support\Criteria\Criteria;

final class PDOStudentRepository implements StudentRepositoryInterface
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
     * Searching student with given ID.
     *
     * @param int $id
     *
     * @return \Romanpravda\KcTestTask\Domains\Student\Student|null
     *
     * @throws \Romanpravda\KcTestTask\Exceptions\Repositories\PDOException
     */
    public function findById(int $id): ?Student
    {
        $sql = 'SELECT `student_id`, `user_id`, `full_name`, `group` FROM `students` WHERE `student_id` = :id';

        $student = $this->retrieveDataById($this->PDO, $sql, $id);
        if (is_null($student)) {
            return null;
        }

        return $this->makeStudentFromData($student);
    }

    /**
     * Searching students matching the criteria.
     *
     * @param \Romanpravda\KcTestTask\Support\Criteria\Criteria $criteria
     *
     * @return \Romanpravda\KcTestTask\Domains\Student\Student[]
     *
     * @throws \Romanpravda\KcTestTask\Exceptions\Repositories\PDOException
     */
    public function findByCriteria(Criteria $criteria): array
    {
        $students = [];

        $sql = $this->applyCriteriaToSql($criteria, 'SELECT `student_id`, `user_id`, `full_name`, `group` FROM `students`');

        $studentsData = $this->retrieveDataByCriteria($this->PDO, $sql, $criteria);
        foreach ($studentsData as $student) {
            $students[] = $this->makeStudentFromData($student);
        }

        return $students;
    }

    /**
     * Saving student data.
     *
     * @param \Romanpravda\KcTestTask\Domains\Student\Student $student
     *
     * @return \Romanpravda\KcTestTask\Domains\Student\Student
     *
     * @throws \Romanpravda\KcTestTask\Exceptions\Repositories\PDOException
     */
    public function save(Student $student): Student
    {
        $now = new DateTimeImmutable();

        $sql = 'INSERT INTO `students` (`user_id`, `full_name`, `group`, `created_at`, `created_at_timezone`, `updated_at`, `updated_at_timezone`) VALUES (:user_id, :full_name, :group, CONVERT_TZ(:created_at, :timezone, :utc), :timezone, CONVERT_TZ(:updated_at, :timezone, :utc), :timezone)';
        $statement = $this->PDO->prepare($sql);

        $res = $statement->execute([
            ':user_id' => $student->getUserId(),
            ':full_name' => $student->getFullName(),
            ':group' => $student->getGroup(),
            ':created_at' => $now->format('Y-m-d H:i:s'),
            ':updated_at' => $now->format('Y-m-d H:i:s'),
            ':timezone' => $now->format('P'),
            ':utc' => 'UTC',
        ]);

        if ($res === false) {
            $error = $statement->errorInfo();
            throw PDOException::create($error[0], $error[1], $error[2]);
        }

        $studentId = $this->PDO->lastInsertId();
        if ($studentId !== false) {
            $student->setId((int) $studentId);
        }

        return $student;
    }

    /**
     * Updating student data.
     *
     * @param \Romanpravda\KcTestTask\Domains\Student\Student $student
     *
     * @return bool
     *
     * @throws \Romanpravda\KcTestTask\Exceptions\Repositories\PDOException
     */
    public function update(Student $student): bool
    {
        $now = new DateTimeImmutable();

        $sql = 'UPDATE `students` SET `user_id` = :user_id, `full_name` = :full_name, `group` = :group, `updated_at` = CONVERT_TZ(:updated_at, :timezone, :utc), `updated_at_timezone` = :timezone WHERE `student_id` = :student_id';
        $statement = $this->PDO->prepare($sql);

        $res = $statement->execute([
            ':user_id' => $student->getUserId(),
            ':full_name' => $student->getFullName(),
            ':group' => $student->getGroup(),
            ':updated_at' => $now->format('Y-m-d H:i:s'),
            ':timezone' => $now->format('P'),
            ':utc' => 'UTC',
            ':student_id' => $student->getId(),
        ]);

        if ($res === false) {
            $error = $statement->errorInfo();
            throw PDOException::create($error[0], $error[1], $error[2]);
        }

        return $res;
    }

    /**
     * Deleting student.
     *
     * @param \Romanpravda\KcTestTask\Domains\Student\Student $student
     *
     * @return bool
     *
     * @throws \Romanpravda\KcTestTask\Exceptions\Repositories\PDOException
     */
    public function delete(Student $student): bool
    {
        $sql = 'DELETE FROM `students` WHERE `student_id` = :student_id';
        $statement = $this->PDO->prepare($sql);

        $res = $statement->execute([
            ':student_id' => $student->getId(),
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
     * @throws \Romanpravda\KcTestTask\Exceptions\Repositories\PDOException
     * @throws \Romanpravda\KcTestTask\Exceptions\Repositories\NoFilterForDeleteException
     */
    public function deleteByCriteria(Criteria $criteria): bool
    {
        if (!$criteria->hasFilter()) {
            throw NoFilterForDeleteException::create();
        }

        $sql = sprintf('DELETE FROM `students` WHERE %s', $criteria->getFilter()->getQuery());

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
     * @param array $studentData
     *
     * @return \Romanpravda\KcTestTask\Domains\Student\Student
     */
    private function makeStudentFromData(array $studentData): Student
    {
        return new Student((int) $studentData['student_id'], (int) $studentData['user_id'], $studentData['full_name'], $studentData['group'] ?? Student::DEFAULT_GROUP);
    }
}