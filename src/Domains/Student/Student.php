<?php

declare(strict_types=1);

namespace Romanpravda\KcTestTask\Domains\Student;

final class Student
{
    public const DEFAULT_GROUP = 'Default Group';

    /**
     * Student's ID.
     *
     * @var int|null
     */
    private ?int $id;

    /**
     * Student's user's ID.
     *
     * @var int
     */
    private int $userId;

    /**
     * Student's full name.
     *
     * @var string
     */
    private string $fullName;

    /**
     * Student's group.
     *
     * @var string|null
     */
    private ?string $group;

    /**
     * Student constructor.
     *
     * @param int|null $id
     * @param int $userId
     * @param string $fullName
     * @param string|null $group
     */
    public function __construct(?int $id, int $userId, string $fullName, ?string $group)
    {
        $this->setId($id);
        $this->setUserId($userId);
        $this->setFullName($fullName);
        $this->setGroup($group);
    }

    /**
     * Retrieving student's ID.
     *
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Setting student's ID.
     *
     * @param int|null $id
     */
    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    /**
     * Retrieving student's user's ID.
     *
     * @return int
     */
    public function getUserId(): int
    {
        return $this->userId;
    }

    /**
     * Setting student's user's ID.
     *
     * @param int $userId
     */
    public function setUserId(int $userId): void
    {
        $this->userId = $userId;
    }

    /**
     * Retrieving student's full name.
     *
     * @return string
     */
    public function getFullName(): string
    {
        return $this->fullName;
    }

    /**
     * Setting student's full name.
     *
     * @param string $fullName
     */
    public function setFullName(string $fullName): void
    {
        $this->fullName = $fullName;
    }

    /**
     * Retrieving student's group.
     *
     * @return string|null
     */
    public function getGroup(): ?string
    {
        return $this->group;
    }

    /**
     * Setting student's group.
     *
     * @param string|null $group
     */
    public function setGroup(?string $group): void
    {
        $this->group = $group;
    }
}